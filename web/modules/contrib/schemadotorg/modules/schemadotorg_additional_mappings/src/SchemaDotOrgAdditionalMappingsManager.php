<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_mappings;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org additional mappings manager.
 */
class SchemaDotOrgAdditionalMappingsManager implements SchemaDotOrgAdditionalMappingsManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgAdditionalMappingsManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $fieldTypePluginManager
   *   The field type plugin manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $schemaMappingManager
   *   The Schema.org mapping manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder
   *   The Schema.org schema type builder.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityFieldManagerInterface $entityFieldManager,
    protected FieldTypePluginManagerInterface $fieldTypePluginManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgMappingManagerInterface $schemaMappingManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void {
    $bundle = $bundle ?? $defaults['entity']['id'];

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $this->entityTypeManager
      ->getStorage('schemadotorg_mapping')
      ->load("$entity_type_id.$bundle");

    $default_additional_mappings = $this->getDefaultAdditionalMappings($entity_type_id, $bundle, $schema_type);

    $additional_mappings = ($mapping)
      ? $mapping->getAdditionalMappings()
      : $default_additional_mappings;

    // Convert 'field name' => 'Schema.org property' associative array
    // to 'Schema.org property' => TRUE associative array.
    foreach ($additional_mappings as &$additional_mapping) {
      $schema_properties = [];
      foreach ($additional_mapping['schema_properties'] as $schema_property) {
        $schema_properties[$schema_property] = TRUE;
      }
      $additional_mapping['schema_properties'] = $schema_properties;
    }

    // Apply starter kit default mappings.
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingManager::getMappingDefaults
    if (!empty($defaults['additional_mappings'])) {
      foreach ($defaults['additional_mappings'] as $additional_schema_type => $default_additional_mapping) {
        // Make sure that the default additional mapping exists.
        if (!isset($default_additional_mappings[$additional_schema_type])) {
          continue;
        }

        // Unset additional mapping if the additional mapping's Schema.org type
        // is set to NULL or the starter kit sets it to FALSE.
        $is_schema_type_null = is_array($default_additional_mapping)
          && (NestedArray::keyExists($default_additional_mapping, ['schema_type']))
          && (NestedArray::getValue($default_additional_mapping, ['schema_type']) === NULL);
        if ($default_additional_mapping === FALSE || $is_schema_type_null) {
          $additional_mappings[$additional_schema_type] = [
            'schema_type' => NULL,
            'schema_properties' => [],
          ];
          continue;
        }

        // Make sure the additional mapping for the Schema.org type is defined.
        $additional_mappings += [
          $additional_schema_type => [
            'schema_type' => $additional_schema_type,
            'schema_properties' => [],
          ],
        ];

        // Apply the defaults additional mapping Schema.org properties.
        $default_schema_properties = $default_additional_mapping['properties']
          ?? $default_additional_mapping['schema_properties']
          ?? [];

        // Create a list of allowed Schema.org properties.
        $allowed_schema_properties = array_combine(
          $default_additional_mappings[$additional_schema_type]['schema_properties'],
          $default_additional_mappings[$additional_schema_type]['schema_properties']
        );
        foreach ($default_schema_properties as $default_schema_property => $default_schema_property_state) {
          if (isset($allowed_schema_properties[$default_schema_property])) {
            $additional_mappings[$additional_schema_type]['schema_properties'][$default_schema_property] = $default_schema_property_state;
          }
        }
      }
    }

    $defaults['additional_mappings'] = $additional_mappings;
  }

  /**
   * {@inheritdoc}
   */
  public function mappingFormAlter(array &$form, FormStateInterface $form_state): void {
    if (!$this->moduleHandler->moduleExists('schemadotorg_ui')) {
      return;
    }

    /** @var \Drupal\schemadotorg\Form\SchemaDotOrgMappingForm $form_object */
    $form_object = $form_state->getFormObject();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $form_object->getEntity();

    // Exit if no Schema.org type has been selected or if we are currently
    // editing the WebPage Schema.org content type.
    $schema_type = $mapping->getSchemaType();
    if (!$schema_type) {
      return;
    }

    $mapping_defaults = $form_state->get('mapping_defaults');
    $target_entity_type_id = $mapping->getTargetEntityTypeId();
    $target_bundle = $mapping_defaults['entity']['id'];
    $additional_mappings = $mapping_defaults['additional_mappings'];

    $default_additional_mappings = $this->getDefaultAdditionalMappings($target_entity_type_id, $target_bundle, $schema_type);
    if (!$default_additional_mappings) {
      return;
    }

    $form['mapping']['additional_mappings'] = [
      '#type' => 'details',
      '#title' => $this->t('Additional mappings'),
      '#description' => $this->t('For JSON-LD, additional Schema.org mappings will be merged into the root JSON-LD, except for https://schema.org/WebPage which replace the root JSON-LD and set the https://schema.org/mainEntity property.'),
    ];

    foreach ($default_additional_mappings as $default_additional_mapping) {
      $default_additional_schema_type = $default_additional_mapping['schema_type'];
      $default_additional_schema_properties = $default_additional_mapping['schema_properties'];

      $additional_schema_type = $additional_mappings[$default_additional_schema_type]['schema_type'] ?? NULL;
      $additional_schema_properties = $additional_mappings[$default_additional_schema_type]['schema_properties'] ?? [];
      if ($additional_schema_type) {
        $form['mapping']['additional_mappings']['#open'] = TRUE;
      }

      $additional_mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
        entity_type_id: $target_entity_type_id,
        schema_type: $default_additional_schema_type,
      );
      $additional_mapping_defaults['properties'] = array_intersect_key(
        $additional_mapping_defaults['properties'],
        array_combine($default_additional_schema_properties, $default_additional_schema_properties)
      );

      $field_type_definitions = $this->fieldTypePluginManager->getUiDefinitions();
      $field_definitions = $this->entityFieldManager->getFieldDefinitions($target_entity_type_id, $target_bundle);

      $options = [];
      foreach ($additional_mapping_defaults['properties'] as $property_name => $property) {
        $field_name = $property['name'];
        if (empty($field_name) || $field_name === SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD) {
          $field_name = $this->schemaNames->getFieldPrefix() . $property['machine_name'];
        }

        $field_definition = $field_definitions[$field_name] ?? [];
        $field_type = ($field_definition) ? $field_definition->getType() : $property['type'];
        $field_type_definition = $field_type_definitions[$field_type] ?? [];

        $options[$property_name] = [
          'property' => [
            'data' => [
              '#type' => 'link',
              '#title' => $property_name,
              '#url' => $this->schemaTypeBuilder->getItemUrl($property_name),
            ],
          ],
          'label' => $property['label'],
          'name' => $field_name,
          'type' => $field_type_definition['label'] ?? $property['type'],
          'status' => ($field_definition) ? $this->t('Existing') : $this->t('New'),
          '#attributes' => [
            'class' => ($field_definition) ? 'color-success' : 'color-warning',
          ],
        ];
      }

      $type_definition = $this->schemaTypeManager->getType($default_additional_schema_type);

      $form['mapping']['additional_mappings'][$default_additional_schema_type] = [
        '#type' => 'details',
        '#title' => $type_definition['drupal_label'],
        '#description' => $this->schemaTypeBuilder->formatComment($type_definition['drupal_description']),
        '#open' => (bool) $additional_schema_type,
      ];
      $t_args = [
        '@type' => $type_definition['drupal_label'],
        ':href' => $this->schemaTypeBuilder->getItemUrl($default_additional_schema_type)->toString(),
      ];
      $form['mapping']['additional_mappings'][$default_additional_schema_type]['schema_type'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable @type mapping', ['@type' => $type_definition['drupal_label']]),
        '#description' => $this->t('If checked, additional Schema.org properties related to the <a href=":href">@type</a> Schema.org type will be included with this mapping.', $t_args),
        '#return_value' => $default_additional_schema_type,
        '#default_value' => $additional_schema_type,
      ];
      $form['mapping']['additional_mappings'][$default_additional_schema_type]['schema_properties'] = [
        '#type' => 'tableselect',
        '#title' => $this->t('WebPage mapping properties'),
        '#header' => [
          'property' => $this->t('Schema.org property'),
          'label' => $this->t('Field label'),
          'name' => $this->t('Field name'),
          'type' => $this->t('Field type'),
          'status' => $this->t('Field status'),
        ],
        '#options' => $options,
        '#default_value' => $additional_schema_properties,
        '#access' => (bool) $options,
        // Add missing wrapper for #states to work as expected.
        '#prefix' => '<div class="js-form-wrapper">',
        '#suffix' => '</div>',
        '#states' => [
          'visible' => [
            'input[name="mapping[additional_mappings][' . $default_additional_schema_type . '][schema_type]"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function mappingPreSave(SchemaDotOrgMappingInterface $mapping): void {
    $default_additional_mappings = $this->getDefaultAdditionalMappings(
      $mapping->getTargetEntityTypeId(),
      $mapping->getTargetBundle(),
      $mapping->getSchemaType(),
    );

    $additional_mappings = $mapping->getAdditionalMappings();
    foreach ($additional_mappings as $schema_type => &$additional_mapping) {
      // Unset the additional mapping if it doesn't have a Schema.org type.
      if (array_key_exists('schema_type', $additional_mapping)
        && empty($additional_mapping['schema_type'])) {
        unset($additional_mappings[$schema_type]);
        continue;
      }

      // Get the default additional mapping Schema.org properties.
      $default_schema_properties = $default_additional_mappings[$schema_type]['schema_properties'] ?? [];

      // Get Schema.org properties is an associative array.
      // The additional mapping Schema.org properties can either be
      // 'field name' => 'Schema.org property', or
      // 'Schema.org property' => TRUE, associative array.
      $schema_properties = [];
      foreach ($additional_mapping['schema_properties'] as $key => $value) {
        if ($value !== FALSE) {
          $schema_property = ($value === TRUE || is_array($value)) ? $key : $value;
          $schema_properties[$schema_property] = $schema_property;
        }
      }

      // Convert 'Schema.org property' => TRUE associative array
      // back to 'field name' => 'Schema.org property' associative array.
      $additional_mapping['schema_properties'] = array_flip(
        array_intersect_key(
          array_flip($default_schema_properties),
          $schema_properties,
        )
      );
    }
    $mapping->set('additional_mappings', $additional_mappings);
  }

  /**
   * {@inheritdoc}
   *
   * The below code works around the architecture limitation (or simplicity)
   * which only allows one Schema.org mapping per content type.
   *
   * Therefore, we need to save the mapping as a WebPage with the selected
   * properties, which builds the expected fields, forms, and displays
   * and then revert the mapping back to its original state.
   */
  public function mappingPostSave(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    /** @var \Drupal\schemadotorg\Entity\SchemaDotOrgMapping $mapping */
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $schema_type = $mapping->get('schema_type');
    $schema_properties = $mapping->get('schema_properties');
    $original = (isset($mapping->original)) ? clone $mapping->original : NULL;

    $mapping_defaults = $mapping->getMappingDefaults();
    $additional_mappings = $mapping->getAdditionalMappings();
    foreach ($additional_mappings as $additional_mapping) {
      $additional_schema_type = $additional_mapping['schema_type'];
      $additional_schema_properties = $additional_mapping['schema_properties'];
      if ($this->schemaTypeManager->isSubTypeOf($additional_schema_type, $schema_type)) {
        continue;
      }

      $additional_mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
        entity_type_id: $entity_type_id,
        bundle: $bundle,
        schema_type: $additional_schema_type,
      );

      // Set the bundle that will have the additional mappings applied to it.
      $additional_mapping_defaults['entity']['id'] = $bundle;

      // Set the additional mapping properties.
      $additional_schema_properties_field_names = array_flip($additional_schema_properties);
      foreach ($additional_mapping_defaults['properties'] as $schema_property => &$field) {
        $field['name'] = $additional_schema_properties_field_names[$schema_property] ?? NULL;
        $mapping_defaults_schema_property = $mapping_defaults['additional_mappings'][$additional_schema_type]['schema_properties'][$schema_property] ?? NULL;
        if (is_array($mapping_defaults_schema_property)) {
          $additional_mapping_defaults['properties'][$schema_property] = $mapping_defaults_schema_property
            + $additional_mapping_defaults['properties'][$schema_property];
        }
      }

      // Clear the additional mappings to prevent a recursion.
      $additional_mapping_defaults['additional_mappings'] = [];

      // Update the mapping.
      $mapping
        ->set('schema_type', $additional_schema_type)
        ->set('schema_properties', $additional_schema_properties)
        ->set('additional_mappings', []);

      // Set original mapping.
      if (isset($mapping->original)) {
        $additional_mapping_original = $original->getAdditionalMapping($additional_schema_type);
        $mapping->original->set('schema_properties', $additional_schema_type);
        $mapping->original->set('schema_properties', $additional_mapping_original['schema_properties'] ?? []);
        $mapping->original->set('additional_mappings', []);
      }

      // Save the additional mapping to create expected fields, forms, and displays
      // and get back the updated mapping, which will be reverted.
      $this->schemaMappingManager->saveMapping($entity_type_id, $additional_schema_type, $additional_mapping_defaults, $mapping);
    }

    // Re-save the original mapping with updated additional mappings
    // without syncing enabled.
    $mapping->setSyncing(TRUE);
    $mapping
      ->set('target_bundle', $bundle)
      ->set('schema_type', $schema_type)
      ->set('schema_properties', $schema_properties)
      ->set('additional_mappings', $additional_mappings);
    $mapping->original = $original;
    $mapping->calculateDependencies();
    $mapping->save();
    $mapping->setSyncing(FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultAdditionalMappings(string $entity_type_id, ?string $bundle, string $schema_type): array {
    $default_additional_mappings = $this->configFactory
      ->get('schemadotorg_additional_mappings.settings')
      ->get('default_additional_mappings');
    $parts = [
      'entity_type_id' => $entity_type_id,
      'bundle' => $bundle,
      'schema_type' => $schema_type,
    ];
    $default_additional_mapping = $this->schemaTypeManager->getSetting($default_additional_mappings, $parts);
    if (!$default_additional_mapping) {
      return [];
    }

    $additional_mappings = [];

    foreach ($default_additional_mapping as $additional_schema_type) {
      // Prevent recursion.
      if ($this->schemaTypeManager->isSubTypeOf($schema_type, $additional_schema_type)) {
        continue;
      }

      $mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
        entity_type_id: $entity_type_id,
        schema_type: $additional_schema_type,
      );

      $default_properties = $this->getDefaultProperties($schema_type, $additional_schema_type);
      $schema_properties = [];
      foreach ($mapping_defaults['properties'] as $schema_property => $property) {
        if (isset($default_properties[$schema_property])) {
          $field_name = $property['name'];
          // Make sure the field is set if it does not already exist.
          if (empty($field_name)
            || $field_name === SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD) {
            $field_name = $this->schemaNames->getFieldPrefix() . $property['machine_name'];
          }
          $schema_properties[$field_name] = $schema_property;
        }
      }
      $additional_mappings[$additional_schema_type] = [
        'schema_type' => $additional_schema_type,
        'schema_properties' => $schema_properties,
      ];
    }
    return $additional_mappings;
  }

  /**
   * Get default properties for an additional Schema.org type.
   *
   * @param string $schema_type
   *   A main Schema.org type.
   * @param string $additional_schema_type
   *   An additional Schema.org type.
   *
   * @return array
   *   Default properties for an additional Schema.org type.
   */
  protected function getDefaultProperties(string $schema_type, string $additional_schema_type): array {
    $default_properties = $this->configFactory
      ->get('schemadotorg_additional_mappings.settings')
      ->get('default_properties');
    $properties = $default_properties["$schema_type--$additional_schema_type"]
      ?? $default_properties[$additional_schema_type]
      ?? NULL;
    return ($properties)
      ? array_combine($properties, $properties)
      : [];
  }

}
