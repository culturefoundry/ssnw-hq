<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Schema.org mapping manager service.
 *
 * The Schema.org mapping manager service provides a API for get the mapping
 * defaults for create an entity bundle with fields for a Schema.org type
 * and properties and then use these mapping defaults to create
 * entity bundle with fields.
 *
 * This service is used by the UI, mapping sets, and starter kits.
 */
class SchemaDotOrgMappingManager implements SchemaDotOrgMappingManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgMappingManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder
   *   The Schema.org schema type builder.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface $schemaEntityFieldManager
   *   The Schema.org entity field manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilderInterface $schemaEntityTypeBuilder
   *   The Schema.org entity type builder.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder
   *   The Schema.org entity display builder.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityFieldManagerInterface $entityFieldManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder,
    protected SchemaDotOrgEntityFieldManagerInterface $schemaEntityFieldManager,
    protected SchemaDotOrgEntityTypeBuilderInterface $schemaEntityTypeBuilder,
    protected SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getIgnoredProperties(): array {
    $ignored_properties = $this->configFactory
      ->get('schemadotorg.settings')
      ->get('schema_properties.ignored_properties');
    return $ignored_properties ? array_combine($ignored_properties, $ignored_properties) : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getMappingDefaults(string $entity_type_id = '', ?string $bundle = NULL, string $schema_type = '', array $defaults = []): array {
    // Validate entity type id..
    if (!$this->getMappingTypeStorage()->load($entity_type_id)) {
      throw new \Exception(sprintf("A mapping type for '%s' does not exist and is required to create a Schema.org '%s'.", $entity_type_id, $schema_type));
    }

    // Validate schema type.
    if (!$this->schemaTypeManager->isType($schema_type)) {
      throw new \Exception(sprintf("A Schema.org type for '%s' does not exist.", $schema_type));
    }

    $mapping_defaults = [];

    // Get entity, properties, third_party_settings defaults.
    $mapping_defaults['entity'] = $this->getMappingEntityDefaults($entity_type_id, $bundle, $schema_type);
    $mapping_defaults['properties'] = $this->getMappingPropertiesFieldDefaults($entity_type_id, $bundle, $schema_type);
    $mapping_defaults['third_party_settings'] = $this->getMappingThirdPartySettingsDefaults($entity_type_id, $bundle, $schema_type);

    // Apply custom entity defaults.
    if (isset($defaults['entity'])) {
      $mapping_defaults['entity'] = $defaults['entity'] + $mapping_defaults['entity'];
    }

    // Apply custom properties defaults.
    if (isset($defaults['properties'])) {
      foreach ($defaults['properties'] as $property_name => $property) {
        if ($property === FALSE) {
          // Unset the name to not have the property added.
          $mapping_defaults['properties'][$property_name]['name'] = '';
        }
        elseif ($property === TRUE) {
          // Make sure the property is being added.
          if (empty($mapping_defaults['properties'][$property_name]['name'])) {
            $mapping_defaults['properties'][$property_name]['name'] = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;
          }
        }
        elseif (is_array($property)) {
          // Merge the custom defaults with the property's defaults.
          $mapping_defaults['properties'][$property_name] = $property
            + ($mapping_defaults['properties'][$property_name] ?? []);
        }
      }
    }

    // Apply additional mappings defaults.
    // @see \Drupal\schemadotorg_additional_mappings\SchemaDotOrgAdditionalMappingsManager::mappingDefaultsAlter
    $mapping_defaults['additional_mappings'] = $defaults['additional_mappings'] ?? [];

    // Allow modules to alter the mapping defaults via a hook.
    $this->moduleHandler->invokeAllWith(
      'schemadotorg_mapping_defaults_alter',
      function (callable $hook) use (&$mapping_defaults, $entity_type_id, $bundle, $schema_type): void {
        $hook($mapping_defaults, $entity_type_id, $bundle, $schema_type);
      }
    );

    return $mapping_defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function getMappingDefaultsByType(string $type, array $defaults = []): array {
    [$entity_type_id, $bundle , $schema_type] = $this->getMappingStorage()->parseType($type);
    return $this->getMappingDefaults($entity_type_id, $bundle, $schema_type, $defaults);
  }

  /**
   * Get Schema.org mapping entity default values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   Schema.org mapping entity default values.
   */
  protected function getMappingEntityDefaults(string $entity_type_id, ?string $bundle, string $schema_type): array {
    $defaults = [];
    $mapping = $this->loadMapping($entity_type_id, $bundle);
    if ($mapping) {
      $defaults['label'] = $mapping->label();
      $defaults['id'] = $bundle;
      $defaults['description'] = ($mapping->getTargetEntityBundleEntity())
        ? $mapping->getTargetEntityBundleEntity()->get('description')
        : $this->schemaTypeManager->getType($mapping->getSchemaType())['drupal_description'];
      return $defaults;
    }

    $default_type = $this->configFactory
      ->get('schemadotorg.settings')
      ->get("schema_types.default_types.$schema_type") ?? [];
    $type_definition = $this->schemaTypeManager->getType($schema_type);

    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

    if (empty($entity_type->getBundleEntityType())) {
      // If the entity type does not support bundles (i.e. user), the
      // bundle label and id must always be the same as the entity type.
      $defaults['label'] = $entity_type->getLabel();
      $defaults['id'] = $entity_type_id;
    }
    else {
      // Get label and id prefixes.
      $mapping_type = $this->loadMappingType($entity_type_id);
      $label_prefix = $mapping_type->get('label_prefix') ?? '';
      $id_prefix = $mapping_type->get('id_prefix') ?? '';
      // Get label and id.
      $label = $default_type['label'] ?? $type_definition['drupal_label'];
      $id = $bundle ?: $default_type['name'] ?? $type_definition['drupal_name'];

      $defaults['label'] = $label_prefix . $label;
      $defaults['id'] = $id_prefix . $id;
    }
    $defaults['description'] = $default_type['description']
      ?? $this->schemaTypeBuilder->formatComment(
        $type_definition['drupal_description'],
        ['base_path' => 'https://schema.org/']
      );
    return $defaults;
  }

  /**
   * Get Schema.org mapping properties field default values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   Schema.org mapping properties field default values.
   */
  protected function getMappingPropertiesFieldDefaults(string $entity_type_id, ?string $bundle, string $schema_type): array {
    $mapping = $this->loadMapping($entity_type_id, $bundle);

    $fields = ['label', 'comment', 'range_includes', 'superseded_by'];
    $property_definitions = $this->schemaTypeManager->getTypeProperties($schema_type, $fields);
    $ignored_properties = $this->getIgnoredProperties();
    $property_definitions = array_diff_key($property_definitions, $ignored_properties);

    $defaults = [];
    foreach ($property_definitions as $property => $property_definition) {
      // Skip a superseded property unless it is already mapped.
      if (!empty($property_definition['superseded_by'])
        && (!$mapping || !$mapping->getSchemaPropertyMapping($property))) {
        continue;
      }

      $defaults[$property] = $this->getMappingPropertyFieldDefaults($entity_type_id, $bundle, $schema_type, $property_definition);
    }

    return $defaults;
  }

  /**
   * Get Schema.org mapping third party settings default values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   Schema.org mapping third party settings default values.
   */
  protected function getMappingThirdPartySettingsDefaults(string $entity_type_id, ?string $bundle, string $schema_type): array {
    $mapping = $this->loadMapping($entity_type_id, $bundle);
    return ($mapping)
      ? $mapping->get('third_party_settings')
      : [];
  }

  /**
   * Get Schema.org mapping property default values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $property_definition
   *   The property definition.
   *
   * @return array
   *   Schema.org mapping property default values.
   */
  protected function getMappingPropertyFieldDefaults(string $entity_type_id, ?string $bundle, string $schema_type, array $property_definition): array {
    $schema_property = $property_definition['label'];

    $mapping_type = $this->loadMappingType($entity_type_id);

    // Exit if no mapping type is defined for the entity type.
    if (!$mapping_type) {
      return [];
    }

    $mapping = $this->loadMapping($entity_type_id, $bundle);

    $is_new_mapping = empty($mapping);

    $base_field_mappings = $mapping_type->getBaseFieldMappings();
    $property_defaults = $mapping_type->getDefaultSchemaTypeProperties($schema_type);
    $property_mappings = $mapping ? array_flip($mapping->getSchemaProperties()) : [];

    $default_field = $this->schemaEntityFieldManager->getPropertyDefaultField($schema_type, $schema_property);

    // Get field name default value.
    $field_name = $property_mappings[$schema_property] ?? NULL;
    if (!$field_name && $is_new_mapping && isset($property_defaults[$schema_property])) {
      // Try getting the base field mapping.
      if (isset($base_field_mappings[$schema_property])) {
        foreach ($base_field_mappings[$schema_property] as $base_field_name) {
          $field_storage_exists = $this->schemaEntityFieldManager->fieldStorageExists(
            $entity_type_id,
            $base_field_name
          );
          if ($field_storage_exists) {
            $field_name = $base_field_name;
            break;
          }
        }
      }

      if (!$field_name) {
        $field_name = $this->schemaNames->getFieldPrefix() . $default_field['name'];
        $field_storage_exists = $this->schemaEntityFieldManager->fieldStorageExists(
          $entity_type_id,
          $field_name
        );
        if (!$field_storage_exists) {
          $field_name = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;
        }
      }
    }

    // Get field type default value from field type options.
    $field_type_options = $this->schemaEntityFieldManager->getPropertyFieldTypeOptions($schema_type, $schema_property);
    $recommended_category = (string) $this->t('Recommended');
    $field_type = (isset($field_type_options[$recommended_category]))
      ? array_key_first($field_type_options[$recommended_category])
      : NULL;

    $defaults = [];
    $defaults['name'] = $field_name;
    $defaults['type'] = $field_type;
    $defaults['label'] = $default_field['label'];
    $defaults['machine_name'] = $default_field['name'];
    $defaults['unlimited'] = $default_field['unlimited'];
    $defaults['required'] = $default_field['required'];
    $defaults['description'] = $this->schemaTypeBuilder->formatComment(
      $default_field['description'],
      ['base_path' => 'https://schema.org/']
    );
    if (isset($default_field['default_value'])) {
      $defaults['default_value'] = $default_field['default_value'];
    }
    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function saveMapping(string $entity_type_id, string $schema_type, array $values): SchemaDotOrgMappingInterface {
    $bundle = $values['entity']['id'] ?? $entity_type_id;

    // Get mapping entity.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $this->loadMapping($entity_type_id, $bundle)
      ?: $this->getMappingStorage()->create([
        'target_entity_type_id' => $entity_type_id,
        'target_bundle' => $bundle,
        'schema_type' => $schema_type,
      ]);

    // Create target bundle entity.
    if ($mapping->isNewTargetEntityTypeBundle()) {
      $bundle_entity_type_id = $mapping->getTargetEntityTypeBundleId();
      $bundle_entity = $this->schemaEntityTypeBuilder->addEntityBundle($bundle_entity_type_id, $schema_type, $values);
      $mapping->setTargetBundle($bundle_entity->id());
    }

    // Reset Schema.org properties.
    $mapping->set('schema_properties', []);
    foreach ($values['properties'] as $property_name => $field) {
      $field_name = $field['name'];

      // Skip empty field names.
      if (!$field_name) {
        continue;
      }

      // Add Schema.org type and property to property values.
      $field['schema_type'] = $schema_type;
      $field['schema_property'] = $property_name;

      // Update title field definition for new mappings.
      // @see \Drupal\node\NodeTypeForm::save
      if ($mapping->isNew() && $entity_type_id === 'node' && $field_name === 'title') {
        $field_definitions = $this->entityFieldManager->getFieldDefinitions('node', $bundle);
        /** @var \Drupal\Core\Field\BaseFieldDefinition $title_field */
        $title_field = $field_definitions['title'];
        $title_label = $field['label'];
        if ($title_field->getLabel() != $title_label) {
          $title_field->getConfig($bundle)->setLabel($title_label)->save();
        }
      }

      // If field does not exist create it.
      $field_exists = $this->schemaEntityFieldManager->fieldExists(
        $entity_type_id,
        $bundle,
        $field_name
      );
      if (!$field_exists) {
        if ($field_name === SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD) {
          $field_name = $this->schemaNames->getFieldPrefix() . $field['machine_name'];
        }
        $field['machine_name'] = $field_name;
        $this->schemaEntityTypeBuilder->addFieldToEntity($entity_type_id, $bundle, $field);
      }

      // Check mappings Schema.org type and not the $schema.type because the
      // $schema_type could be an additional mapping's Schema.org type.
      // @see \Drupal\schemadotorg_additional_mappings\SchemaDotOrgAdditionalMappingsManager::mappingPostSave
      if ($this->schemaTypeManager->hasProperty($mapping->getSchemaType(), $property_name)) {
        $mapping->setSchemaPropertyMapping($field_name, $property_name);
      }
    }

    // Set field weights for new properties.
    $this->schemaEntityDisplayBuilder->setFieldWeights($mapping);

    // Set additional mappings.
    if (isset($values['additional_mappings'])) {
      $mapping->set('additional_mappings', $values['additional_mappings']);
    }

    // Set third party settings.
    if (isset($values['third_party_settings'])) {
      $mapping->set('third_party_settings', array_filter($values['third_party_settings']));
    }

    // Set mapping defaults.
    // This allows mapping hooks to act on the mapping defaults.
    // @see \Drupal\schemadotorg_field_group\SchemaDotOrgFieldGroupEntityDisplayBuilder::setFieldGroups
    $mapping->setMappingDefaults($values);

    // Save the mapping entity.
    $mapping->save();

    return $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function createTypeValidate(string $entity_type_id, string $schema_type): void {
    // Validate entity type.
    $entity_types = $this->getMappingTypeStorage()->getEntityTypes();
    if (!in_array($entity_type_id, $entity_types)) {
      $t_args = [
        '@entity_type' => $entity_type_id,
        '@entity_types' => implode(', ', $entity_types),
      ];
      $message = (string) $this->t("The entity type '@entity_type' is not valid. Please select a entity type (@entity_types).", $t_args);
      throw new \Exception($message);
    }

    // Validate Schema.org type.
    if (!$this->schemaTypeManager->isType($schema_type)) {
      $t_args = ['@schema_type' => $schema_type];
      $message = (string) $this->t("The Schema.org type '@schema_type' is not valid.", $t_args);
      throw new \Exception($message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createType(string $entity_type_id, string $schema_type, array $defaults = []): void {
    $mapping_type = $this->loadMappingType($entity_type_id);
    if (!$mapping_type) {
      throw new \Exception(sprintf("Mapping type '%s' does not exist and is required to create a Schema.org '%s'.", $entity_type_id, $schema_type));
    }

    $bundle = $defaults['entity']['id'] ?? NULL;
    $bundles = $mapping_type->getDefaultSchemaTypeBundles($schema_type);
    if (empty($bundle) && !empty($bundles)) {
      foreach ($bundles as $bundle) {
        $mapping_defaults = $this->getMappingDefaults($entity_type_id, $bundle, $schema_type, $defaults);
        $this->saveMapping($entity_type_id, $schema_type, $mapping_defaults);
      }
    }
    else {
      $mapping_defaults = $this->getMappingDefaults(
        entity_type_id: $entity_type_id,
        bundle: $bundle,
        schema_type: $schema_type,
        defaults: $defaults
      );
      $this->saveMapping($entity_type_id, $schema_type, $mapping_defaults);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createDefaultTypes(string $entity_type_id): void {
    // Get default Schema.org types for the entity type.
    /** @var array $default_schema_types */
    $default_schema_types = $this->loadMappingType($entity_type_id)
      ->get('default_schema_types');

    // Compare the default Schema.org types with the existing bundles.
    $bundle_entity_type_id = $this->entityTypeManager
      ->getDefinition($entity_type_id)
      ->getBundleEntityType();
    $bundle_entity_types = $this->entityTypeManager
      ->getStorage($bundle_entity_type_id)
      ->loadMultiple();
    $install_schema_types = array_unique(
      array_values(
        array_intersect_key($default_schema_types, $bundle_entity_types)
      )
    );

    foreach ($install_schema_types as $install_schema_type) {
      $this->createType($entity_type_id, $install_schema_type);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteTypeValidate(string $entity_type_id, string $schema_type): void {
    $mappings = $this->getMappingStorage()
      ->loadByProperties([
        'target_entity_type_id' => $entity_type_id,
        'schema_type' => $schema_type,
      ]);
    if (empty($mappings)) {
      $t_args = ['@entity_type' => $entity_type_id, '@schema_type' => $schema_type];
      $message = (string) $this->t('No Schema.org mapping exists for @schema_type (@entity_type).', $t_args);
      throw new \Exception($message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteType(string $entity_type_id, string $schema_type, array $options = []): void {
    $options += [
      'delete-entity' => FALSE,
      'delete-fields' => FALSE,
    ];

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
    $mappings = $this->getMappingStorage()
      ->loadByProperties([
        'target_entity_type_id' => $entity_type_id,
        'schema_type' => $schema_type,
      ]);
    foreach ($mappings as $mapping) {
      $target_entity_bundle = $mapping->getTargetEntityBundleEntity();
      if ($options['delete-entity'] && $target_entity_bundle) {
        $target_entity_bundle->delete();
      }
      else {
        if ($options['delete-fields']) {
          $this->deleteFields($mapping);
        }
        $mapping->delete();
      }
    }
  }

  /**
   * Delete fields associated with Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  protected function deleteFields(SchemaDotOrgMappingInterface $mapping): void {
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();

    /** @var \Drupal\field\FieldStorageConfigStorage $field_storage_config_storage */
    $field_storage_config_storage = $this->entityTypeManager->getStorage('field_storage_config');
    /** @var \Drupal\field\FieldConfigStorage $field_config_storage */
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');

    $base_field_definitions = $this->entityFieldManager->getBaseFieldDefinitions($entity_type_id);

    $mapping_type = $this->loadMappingType($entity_type_id);
    $base_field_names = $mapping_type->getBaseFieldNames();

    $deleted_fields = [];
    $field_names = array_keys($mapping->getSchemaProperties());
    foreach ($field_names as $field_name) {
      // Never delete a base field and default fields
      // (i.e. user_picture, field_media_image).
      if (isset($base_field_definitions[$field_name])
        || isset($base_field_names[$field_name])) {
        continue;
      }

      $field_config = $field_config_storage->load($entity_type_id . '.' . $bundle . '.' . $field_name);
      $field_storage_config = $field_storage_config_storage->load($entity_type_id . '.' . $field_name);
      if ($field_storage_config && count($field_storage_config->getBundles()) <= 1) {
        $field_storage_config->delete();
        $deleted_fields[] = $field_name;
      }
      elseif ($field_config) {
        $field_config->delete();
        $deleted_fields[] = $field_name;
      }
    }
  }

}
