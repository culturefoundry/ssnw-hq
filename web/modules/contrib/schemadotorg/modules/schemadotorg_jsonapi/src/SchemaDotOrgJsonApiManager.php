<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\jsonapi\ResourceType\ResourceType;
use Drupal\jsonapi_extras\Entity\JsonapiResourceConfig;
use Drupal\jsonapi_extras\ResourceType\ConfigurableResourceTypeRepository;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;

/**
 * Schema.org JSON:API manager.
 */
class SchemaDotOrgJsonApiManager implements SchemaDotOrgJsonApiManagerInterface {

  /**
   * Constructs a SchemaDotOrgJsonApiManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirectDestination
   *   The redirect destination service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $fieldManager
   *   The entity field manager.
   * @param \Drupal\jsonapi_extras\ResourceType\ConfigurableResourceTypeRepository $resourceTypeRepository
   *   The JSON:API configurable resource type repository.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected RedirectDestinationInterface $redirectDestination,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityFieldManagerInterface $fieldManager,
    protected ConfigurableResourceTypeRepository $resourceTypeRepository,
    protected SchemaDotOrgNamesInterface $schemaNames,
  ) {}

  /* ************************************************************************ */
  // Resource includes methods.
  /* ************************************************************************ */

  /**
   * {@inheritdoc}
   */
  public function getResourceIncludes(ResourceType $resource_type): array {
    return $this->getResourceIncludesRecursive($resource_type);
  }

  /**
   * Get resource type's entity reference fields as an array of includes.
   *
   * @param \Drupal\jsonapi\ResourceType\ResourceType $resource_type
   *   The resource type.
   * @param int $level
   *   The level of includes.
   *
   * @return array
   *   An array of entity reference field public names to be used as includes.
   */
  protected function getResourceIncludesRecursive(ResourceType $resource_type, int $level = 0): array {
    $entity_type_id = $resource_type->getEntityTypeId();
    $bundle = $resource_type->getBundle();

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $this->entityTypeManager
      ->getStorage('schemadotorg_mapping')
      ->load("$entity_type_id.$bundle");
    if (!$mapping) {
      return [];
    }

    $includes = [];

    $relationships = $this->getResourceTypeRelationships($resource_type);
    $field_definitions = $this->fieldManager->getFieldDefinitions($entity_type_id, $bundle);
    foreach ($field_definitions as $field_name => $field_definition) {
      $field = $resource_type->getFieldByInternalName($field_name);
      if (!$field) {
        continue;
      }

      $public_name = $field->getPublicName();
      if (!isset($relationships[$public_name])) {
        continue;
      }

      // Append field's public name to includes.
      $includes[$public_name] = $public_name;

      // Get nested includes for entity references.
      // @todo Determine how many include levels should be returned.
      if ($level < 1) {
        $field_type = $field_definition->getType();
        if (str_starts_with($field_type, 'entity_reference')) {
          $settings = $field_definition->getSettings();
          $target_type = $settings['target_type'];
          $target_bundles = NestedArray::getValue($settings, ['handler_settings', 'target_bundles']) ?? [];
          foreach ($target_bundles as $target_bundle) {
            $target_resource_id = "$target_type--$target_bundle";
            $target_resource_type = $this->resourceTypeRepository->getByTypeName($target_resource_id);
            $target_includes = $this->getResourceIncludesRecursive($target_resource_type, $level + 1);
            foreach ($target_includes as $target_include) {
              // Append target bundle's field's public name to includes.
              $includes["$public_name.$target_include"] = "$public_name.$target_include";
            }
          }
        }
      }
    }

    return $includes;
  }

  /* ************************************************************************ */
  // Schema.org mapping insert and update resource methods.
  /* ************************************************************************ */

  /**
   * {@inheritdoc}
   */
  public function insertMappingResourceConfig(SchemaDotOrgMappingInterface $mapping): void {
    $resource_config = $this->loadResourceConfig($mapping);
    if ($resource_config) {
      $this->updateMappingResourceConfig($mapping);
      return;
    }

    $resource_fields = [];

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $field_definitions = $this->fieldManager->getFieldDefinitions(
      $entity_type_id,
      $bundle
    );
    $field_names = array_keys($field_definitions);
    foreach ($field_names as $field_name) {
      $resource_fields[$field_name] = [
        'fieldName' => $field_name,
        'publicName' => $this->getResourceFieldPublicName($mapping, $field_name),
        'disabled' => $this->isResourceFieldDisabled($mapping, $field_name),
        'enhancer' => ['id' => ''],
      ];
    }

    ksort($resource_fields);
    $this->getResourceConfigStorage()->create([
      'id' => $this->getResourceId($mapping),
      'path' => $this->getResourcePath($mapping),
      'resourceType' => $this->getResourceType($mapping),
      'resourceFields' => $resource_fields,
      'disabled' => FALSE,
    ])->save();
  }

  /**
   * {@inheritdoc}
   */
  public function updateMappingResourceConfig(SchemaDotOrgMappingInterface $mapping): void {
    $resource_config = $this->loadResourceConfig($mapping);
    if (!$resource_config) {
      $this->insertMappingResourceConfig($mapping);
      return;
    }

    $resource_fields = $resource_config->get('resourceFields');

    $schema_properties = $mapping->getSchemaProperties();
    foreach ($schema_properties as $field_name => $schema_property) {
      // Never update an existing resource field.
      // Ensures that an API field is never changed after it has been created.
      if (isset($resource_fields[$field_name])) {
        continue;
      }

      $resource_fields[$field_name] = [
        'disabled' => $this->isResourceFieldDisabled($mapping, $field_name),
        'fieldName' => $field_name,
        'publicName' => $this->getResourceFieldPublicName($mapping, $field_name),
        'enhancer' => ['id' => ''],
      ];
    }

    ksort($resource_fields);
    $resource_config
      ->set('resourceFields', $resource_fields)
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function insertFieldConfigResource(FieldConfigInterface $field_config): void {
    // Do not insert field into JSON:API resource config if the
    // Scheme.org entity type builder is adding it.
    // @see \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilder::addFieldToEntity
    if (!empty($field_config->schemaDotOrgType) && !empty($field_config->schemaDotOrgProperty)) {
      return;
    }

    $entity_type_id = $field_config->getTargetEntityTypeId();
    $bundle = $field_config->getTargetBundle();
    $field_name = $field_config->getName();

    $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $mapping_storage->load("$entity_type_id.$bundle");
    if (!$mapping) {
      return;
    }

    $resource_config = $this->loadResourceConfig($mapping);
    if (!$resource_config) {
      return;
    }

    // Never update an existing resource field.
    // Ensures that an API field is never changed after it has been created.
    $resource_fields = $resource_config->get('resourceFields');
    if (isset($resource_fields[$field_name])) {
      return;
    }

    $resource_fields[$field_name] = [
      'disabled' => $this->isResourceFieldDisabled($mapping, $field_name),
      'fieldName' => $field_name,
      'publicName' => $this->getResourceFieldPublicName($mapping, $field_name),
      'enhancer' => ['id' => ''],
    ];

    ksort($resource_fields);
    $resource_config
      ->set('resourceFields', $resource_fields)
      ->save();
  }

  /* ************************************************************************ */
  // Schema.org resource storage methods.
  /* ************************************************************************ */

  /**
   * Get JSON:API resource config storage.
   *
   * @return \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   *   JSON:API resource config storage.
   */
  protected function getResourceConfigStorage(): ConfigEntityStorageInterface {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $storage */
    $storage = $this->entityTypeManager->getStorage('jsonapi_resource_config');
    return $storage;
  }

  /**
   * Load JSON:API resource config id for a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return \Drupal\jsonapi_extras\Entity\JsonapiResourceConfig|null
   *   A JSON:API resource config id.
   */
  protected function loadResourceConfig(SchemaDotOrgMappingInterface $mapping): ?JsonapiResourceConfig {
    $target_entity_type_id = $mapping->getTargetEntityTypeId();
    $target_bundle = $mapping->getTargetBundle();
    $resource_id = $target_entity_type_id . '--' . $target_bundle;

    /** @var \Drupal\jsonapi_extras\Entity\JsonapiResourceConfig|null $resource_config */
    $resource_config = $this->getResourceConfigStorage()->load($resource_id);
    return $resource_config;
  }

  /* ************************************************************************ */
  // Schema.org resource type methods.
  /* ************************************************************************ */

  /**
   * Get a resource type's relationships without internal resources.
   *
   * @param \Drupal\jsonapi\ResourceType\ResourceType $resource_type
   *   The resource type.
   *
   * @return array
   *   An array containing a resource type's relationships without
   *   internal resources.
   */
  protected function getResourceTypeRelationships(ResourceType $resource_type): array {
    $relationships = $resource_type->getRelatableResourceTypes();

    // Remove internal relationships.
    foreach ($relationships as $relationship => $relationship_resources) {
      /** @var \Drupal\jsonapi_extras\ResourceType\ConfigurableResourceType[] $relationship_resources */
      foreach ($relationship_resources as $index => $relationship_resource) {
        if ($relationship_resource->isInternal()) {
          unset($relationship_resources[$index]);
        }
      }

      if (empty($relationship_resources)) {
        unset($relationships[$relationship]);
      }
    }

    return $relationships;
  }

  /* ************************************************************************ */
  // Schema.org resource property methods.
  /* ************************************************************************ */

  /**
   * Get JSON:API resource id.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return string
   *   A JSON:API resource id.
   */
  protected function getResourceId(SchemaDotOrgMappingInterface $mapping): string {
    $target_entity_type_id = $mapping->getTargetEntityTypeId();
    $target_bundle = $mapping->getTargetBundle();
    return $target_entity_type_id . '--' . $target_bundle;
  }

  /**
   * Get JSON:API resource type.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   * @param string $delimiter
   *   The delimiter used to separate the entity type from the bundle.
   *
   * @return string
   *   JSON:API resource type.
   */
  protected function getResourceType(SchemaDotOrgMappingInterface $mapping, string $delimiter = '--'): string {
    $resource_type_schemadotorg = $this->configFactory
      ->get('schemadotorg_jsonapi.settings')
      ->get('resource_type_schemadotorg');

    $target_entity_type_id = $mapping->getTargetEntityTypeId();
    if ($resource_type_schemadotorg) {
      $schema_type = $mapping->getSchemaType();
      return $target_entity_type_id . $delimiter . $this->schemaNames->camelCaseToSnakeCase($schema_type);
    }
    else {
      $target_bundle = $mapping->getTargetBundle();
      return $target_entity_type_id . $delimiter . $target_bundle;
    }
  }

  /**
   * Get JSON:API resource path.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return string
   *   JSON:API resource path.
   */
  protected function getResourcePath(SchemaDotOrgMappingInterface $mapping): string {
    return $this->getResourceType($mapping, '/');
  }

  /**
   * Get a resource field's public name.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   * @param string $field_name
   *   The field name.
   *
   * @return string
   *   The resource field's public name,
   */
  protected function getResourceFieldPublicName(SchemaDotOrgMappingInterface $mapping, string $field_name): string {
    $config = $this->configFactory
      ->get('schemadotorg_jsonapi.settings');

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $property = $mapping->getSchemaPropertyMapping($field_name);

    $is_base_field = $this->isBaseField($entity_type_id, $field_name);
    $resource_base_field_schemadotorg = $config->get('resource_base_field_schemadotorg');
    $resource_field_schemadotorg = $config->get('resource_field_schemadotorg');

    $custom_public_names = $config->get('custom_public_names');
    $custom_public_name = $custom_public_names[$field_name] ?? $custom_public_names[$property] ?? NULL;
    if ($custom_public_name) {
      return $custom_public_name;
    }
    elseif (($is_base_field && $resource_base_field_schemadotorg)
      || (!$is_base_field && $resource_field_schemadotorg)) {
      return ($property) ? $this->schemaNames->camelCaseToSnakeCase($property) : $field_name;
    }
    else {
      return $field_name;
    }
  }

  /**
   * Determine if a resource field is disabled.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   * @param string $field_name
   *   The field name.
   *
   * @return bool
   *   TRUE if a resource field is disabled.
   */
  protected function isResourceFieldDisabled(SchemaDotOrgMappingInterface $mapping, string $field_name): bool {
    if ($mapping->getSchemaPropertyMapping($field_name)) {
      return FALSE;
    }
    $default_base_fields = $this->configFactory
      ->get('schemadotorg_jsonapi.settings')
      ->get('default_base_fields');
    if (empty($default_base_fields)) {
      return FALSE;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();

    $base_field_names = [
      "$entity_type_id--$bundle--$field_name",
      "$entity_type_id--$field_name",
      $field_name,
    ];
    return empty(array_intersect($base_field_names, $default_base_fields));
  }

  /* ************************************************************************ */
  // Field helper methods.
  /* ************************************************************************ */

  /**
   * Determine if a field is a base field.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $field_name
   *   The field name.
   *
   * @return bool
   *   TRUE if the field is a base field.
   */
  protected function isBaseField(string $entity_type_id, string $field_name): bool {
    $field_base_definitions = $this->fieldManager->getBaseFieldDefinitions($entity_type_id);
    return isset($field_base_definitions[$field_name]);
  }

}
