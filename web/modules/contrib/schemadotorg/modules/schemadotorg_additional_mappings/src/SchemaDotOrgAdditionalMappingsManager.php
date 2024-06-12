<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_mappings;

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

    $additional_mappings = ($mapping)
      ? $mapping->getAdditionalMappings()
      : $this->getDefaultAdditionalMappings($entity_type_id, $bundle, $schema_type);

    // Apply starter kit default mappings.
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingManager::getMappingDefaults
    if (!empty($defaults['additional_mappings'])) {
      foreach ($defaults['additional_mappings'] as $schema_type => $additional_mapping) {
        if (isset($additional_mappings[$schema_type])) {
          $additional_mappings[$schema_type]['schema_properties'] = $additional_mapping['schema_properties']
            + $additional_mappings[$schema_type]['schema_properties'];
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

    $default_additional_mappings = $this->getDefaultAdditionalMappings($target_entity_type_id, $target_bundle, $schema_type);
    if (!$default_additional_mappings) {
      return;
    }

    // Store default additional mappings for ::mappingFormValidate callback.
    $form_state->set('default_additional_mappings', $default_additional_mappings);

    $additional_mappings = $mapping->isNew()
      ? $default_additional_mappings
      : $mapping->getAdditionalMappings();

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

      $form['mapping']['additional_mappings'][$default_additional_schema_type] = [];
      $form['mapping']['additional_mappings'][$default_additional_schema_type]['schema_type'] = [
        '#type' => 'checkbox',
        '#title' => $type_definition['drupal_label'],
        '#description' => $this->schemaTypeBuilder->formatComment($type_definition['drupal_description']),
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
        '#default_value' => array_combine($additional_schema_properties, $additional_schema_properties),
        '#access' => (bool) $options,
        // Add missing wrapper for #states to work as expected.
        '#prefix' => '<div class="js-form-wrapper">',
        '#suffix' => '</div>',
        '#states' => [
          'visible' => [
            'input[name="mapping[additional_mappings][' . $default_additional_schema_type . '][schema_type]"]' => ['checked' => TRUE],
          ],
          'checked' => [
            'input[name="mapping[additional_mappings][' . $default_additional_schema_type . '][schema_type]"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    // Add validation callback.
    $form['#validate'][] = [static::class, 'mappingFormValidate'];
  }

  /**
   * Form submission validation for schemadotorg_additional_mappings_form_schemadotorg_mapping_form.
   *
   * @see \Drupal\schemadotorg_additional_mappings\SchemaDotOrgAdditionalMappingsManager::mappingFormAlter()
   */
  public static function mappingFormValidate(array &$form, FormStateInterface $form_state): void {
    $default_additional_mappings = $form_state->get('default_additional_mappings');
    $additional_mappings = $form_state->getValue(['mapping', 'additional_mappings']);

    foreach ($additional_mappings as $schema_type => &$additional_mapping) {
      if (empty($additional_mapping['schema_type'])) {
        unset($additional_mappings[$schema_type]);
      }
      else {
        $default_schema_properties = $default_additional_mappings[$schema_type]['schema_properties'];
        $schema_properties = array_filter($additional_mapping['schema_properties']);
        $additional_mapping['schema_properties'] = array_flip(
          array_intersect_key(
            array_flip($default_schema_properties),
            array_flip($schema_properties),
          )
        );
      }
    }
    $form_state->setValue(['mapping', 'additional_mappings'], $additional_mappings);
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

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $schema_type = $mapping->get('schema_type');
    $schema_properties = $mapping->get('schema_properties');

    $additional_mappings = $mapping->getAdditionalMappings();

    foreach ($additional_mappings as $additional_mapping) {
      $additional_schema_type = $additional_mapping['schema_type'];
      $additional_schema_properties_field_names = array_flip($additional_mapping['schema_properties']);
      if ($this->schemaTypeManager->isSubTypeOf($additional_schema_type, $schema_type)) {
        continue;
      }

      $mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
        entity_type_id: $entity_type_id,
        bundle: $bundle,
        schema_type: $additional_schema_type,
      );

      // Set the bundle that will have the additional mappings applied to it.
      $mapping_defaults['entity']['id'] = $bundle;

      // Set the additional mapping properties.
      foreach ($mapping_defaults['properties'] as $schema_property => &$field) {
        $field['name'] = $additional_schema_properties_field_names[$schema_property] ?? NULL;
      }

      // Clear the additional mappings to prevent a recursion.
      $mapping_defaults['additional_mappings'] = [];

      // Save the additional mapping to create expected fields, forms, and displays
      // and get back the updated mapping, which will be reverted.
      $mapping = $this->schemaMappingManager->saveMapping($entity_type_id, $additional_schema_type, $mapping_defaults);
    }

    // Re-save the original mapping with update additional mappings
    // without syncing enabled.
    $mapping->setSyncing(TRUE);
    $mapping
      ->set('target_bundle', $bundle)
      ->set('schema_type', $schema_type)
      ->set('schema_properties', $schema_properties)
      ->set('additional_mappings', $additional_mappings)
      ->save();
    $mapping->setSyncing(FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultAdditionalMappings(string $entity_type_id, ?string $bundle, string $schema_type): array {
    $default_additional_mappings = $this->configFactory
      ->get('schemadotorg_additional_mappings.settings')
      ->get('default_additional_mappings');
    $setting_parts = [
      'entity_type_id' => $entity_type_id,
      'bundle' => $bundle,
      'schema_type' => $schema_type,
    ];

    $additional_mapping_types = $this->schemaTypeManager->getSetting($default_additional_mappings, $setting_parts, TRUE);
    if (!$additional_mapping_types) {
      return [];
    }

    $additional_mappings = [];

    $has_webpage_schema_type = FALSE;
    foreach ($additional_mapping_types as $additional_mapping_type) {
      foreach ($additional_mapping_type as $additional_schema_type) {
        if ($this->schemaTypeManager->isSubTypeOf($additional_schema_type, $schema_type)) {
          continue;
        }

        // Limit additional mapping Schema.org types to one type of
        // https://schema.org/WebPage.
        if ($this->schemaTypeManager->isSubTypeOf($additional_schema_type, 'WebPage')) {
          if ($has_webpage_schema_type) {
            continue;
          }
          $has_webpage_schema_type = TRUE;
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
    $properties = $default_properties[$additional_schema_type]
      ?? $default_properties["$schema_type--$additional_schema_type"]
      ?? NULL;
    return ($properties)
      ? array_combine($properties, $properties)
      : [];
  }

}
