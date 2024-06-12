<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Defines the Schema.org mapping entity.
 *
 * @ConfigEntityType(
 *   id = "schemadotorg_mapping",
 *   label = @Translation("Schema.org mapping"),
 *   label_collection = @Translation("Schema.org mappings"),
 *   label_singular = @Translation("Schema.org mapping"),
 *   label_plural = @Translation("Schema.org mappings"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Schema.org mapping",
 *     plural = "@count Schema.org mappings",
 *   ),
 *   handlers = {
 *     "storage" = "\Drupal\schemadotorg\SchemaDotOrgMappingStorage",
 *     "list_builder" = "Drupal\schemadotorg\SchemaDotOrgMappingListBuilder",
 *     "form" = {
 *       "add" = "Drupal\schemadotorg\Form\SchemaDotOrgMappingForm",
 *       "edit" = "Drupal\schemadotorg\Form\SchemaDotOrgMappingForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "schemadotorg_mapping",
 *   admin_permission = "administer schemadotorg",
 *   links = {
 *     "collection" = "/admin/config/schemadotorg/mappings",
 *     "add-form" = "/admin/config/schemadotorg/mappings/add",
 *     "edit-form" = "/admin/config/schemadotorg/mappings/{schemadotorg_mapping}",
 *     "delete-form" = "/admin/config/schemadotorg/mappings/{schemadotorg_mapping}/delete"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   config_export = {
 *     "id",
 *     "target_entity_type_id",
 *     "target_bundle",
 *     "schema_type",
 *     "schema_properties",
 *     "additional_mappings",
 *   }
 * )
 *
 * @see \Drupal\Core\Entity\Entity\EntityViewDisplay
 */
class SchemaDotOrgMapping extends ConfigEntityBase implements SchemaDotOrgMappingInterface {

  /**
   * Unique ID for the config entity.
   */
  protected string $id;

  /**
   * Entity type to be mapped.
   */
  protected string $target_entity_type_id;

  /**
   * Bundle to be mapped.
   */
  protected string $target_bundle;

  /**
   * Schema.org type.
   */
  protected ?string $schema_type;

  /**
   * List of Schema.org property mappings, keyed by field name.
   */
  protected array $schema_properties = [];

  /**
   * List of original Schema.org property mappings.
   */
  protected array $original_schema_properties = [];

  /**
   * List of additional Schema.org mappings.
   */
  protected array $additional_mappings = [];

  /**
   * The Schema.org mapping defaults.
   */
  protected array $mappingDefaults;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);
    $this->setOriginalSchemaProperties($this->getSchemaProperties());
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE): void {
    parent::postSave($storage, $update);
    $this->setOriginalSchemaProperties($this->getSchemaProperties());
  }

  /**
   * {@inheritdoc}
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    $duplicate->setOriginalSchemaProperties($this->getSchemaProperties());
    return $duplicate;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->target_entity_type_id . '.' . $this->target_bundle;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->isTargetEntityTypeBundle()
      ? $this->getTargetEntityBundleEntity()->label()
      : $this->getTargetEntityTypeDefinition()->getLabel();
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId(): string {
    return $this->target_entity_type_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetBundle(): string {
    return $this->isTargetEntityTypeBundle()
      ? $this->target_bundle
      : $this->getTargetEntityTypeId();
  }

  /**
   * {@inheritdoc}
   */
  public function setTargetBundle($bundle): SchemaDotOrgMappingInterface {
    $this->set('target_bundle', $bundle);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeDefinition(): ?EntityTypeInterface {
    return $this->entityTypeManager()->getDefinition($this->getTargetEntityTypeId());
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeBundleId(): ?string {
    return $this->getTargetEntityTypeDefinition()->getBundleEntityType();
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeBundleDefinition(): ?EntityTypeInterface {
    $bundle_entity_type = $this->getTargetEntityTypeBundleId();
    return $bundle_entity_type ? $this->entityTypeManager()->getDefinition($bundle_entity_type) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityBundleEntity(): ?ConfigEntityBundleBase {
    if (!$this->isTargetEntityTypeBundle()) {
      return NULL;
    }

    $bundle = $this->getTargetBundle();
    $bundle_entity_type_id = $this->getTargetEntityTypeBundleId();
    $entity_storage = $this->entityTypeManager()->getStorage($bundle_entity_type_id);
    /** @var \Drupal\Core\Config\Entity\ConfigEntityBundleBase|null $entity */
    $entity = $bundle ? $entity_storage->load($bundle) : NULL;
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function isTargetEntityTypeBundle(): bool {
    return (boolean) $this->getTargetEntityTypeBundleId();
  }

  /**
   * {@inheritdoc}
   */
  public function isNewTargetEntityTypeBundle(): bool {
    return ($this->isTargetEntityTypeBundle() && !$this->getTargetEntityBundleEntity());
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaType(): ?string {
    return $this->schema_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllSchemaTypes(): array {
    $schema_types = [$this->schema_type => $this->schema_type];
    $additional_mappings = $this->getAdditionalMappings();
    if ($additional_mappings) {
      $additional_schema_types = array_keys($additional_mappings);
      $schema_types += array_combine($additional_schema_types, $additional_schema_types);
    }
    return $schema_types;
  }

  /**
   * {@inheritdoc}
   */
  public function setSchemaType($type): SchemaDotOrgMappingInterface {
    $this->schema_type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaProperties(): array {
    return $this->schema_properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalSchemaProperties(): array {
    return $this->original_schema_properties;
  }

  /**
   * {@inheritdoc}
   */
  public function setOriginalSchemaProperties(array $properties): void {
    $this->original_schema_properties = $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getNewSchemaProperties(): array {
    return array_diff_key($this->schema_properties, $this->original_schema_properties);
  }

  /**
   * {@inheritdoc}
   */
  public function getAllSchemaProperties(): array {
    return $this->getSchemaProperties() + $this->getAdditionalMappingsSchemaProperties();
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyMapping($name): ?string {
    return $this->schema_properties[$name] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setSchemaPropertyMapping(string $name, string $property): SchemaDotOrgMappingInterface {
    $schema_type = $this->getSchemaType();

    /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
    $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
    if (!$schema_type_manager->hasProperty($schema_type, $property)) {
      throw new \Exception("The '$property' property does not exist in Schema.org type '$schema_type'.");
    }

    $this->schema_properties[$name] = $property;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeSchemaProperty(string $name): SchemaDotOrgMappingInterface {
    unset($this->schema_properties[$name]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyFieldName(string $property): ?string {
    // Get the field name from the main mapping's Schema.org properties.
    $schema_properties = array_flip($this->schema_properties);
    if (isset($schema_properties[$property])) {
      return $schema_properties[$property];
    }

    // Get the field name from the additional mappings' Schema.org properties.
    $additional_mappings = $this->getAdditionalMappings();
    foreach ($additional_mappings as $additional_mapping) {
      $additional_schema_properties = array_flip($additional_mapping['schema_properties']);
      if (isset($additional_schema_properties[$property])) {
        return $additional_schema_properties[$property];
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function hasSchemaPropertyMapping(string $property): bool {
    return in_array($property, $this->schema_properties);
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalMappings(): array {
    return $this->additional_mappings;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalMappingsSchemaProperties(): array {
    $properties = [];
    foreach ($this->additional_mappings as $additional_mapping) {
      $properties += $additional_mapping['schema_properties'];
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function setAdditionalMapping(string $schema_type, array $schema_properties): static {
    /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
    $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
    foreach ($schema_properties as $schema_property) {
      if (!$schema_type_manager->hasProperty($schema_type, $schema_property)) {
        throw new \Exception("The '$schema_property' property does not exist in Schema.org type '$schema_type'.");
      }
    }

    $this->additional_mappings[$schema_type] = [
      'schema_type' => $schema_type,
      'schema_properties' => $schema_properties,
    ];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalMapping(string $schema_type): ?array {
    return $this->additional_mappings[$schema_type] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAdditionalMapping(string $schema_type): static {
    unset($this->additional_mappings[$schema_type]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMappingDefaults(): array {
    if (empty($this->mappingDefaults)) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $mapping_manager */
      $mapping_manager = \Drupal::service('schemadotorg.mapping_manager');
      $this->mappingDefaults = $mapping_manager->getMappingDefaults(
        $this->getTargetEntityTypeId(),
        $this->getTargetBundle(),
        $this->getSchemaType()
      );
    }
    return $this->mappingDefaults;
  }

  /**
   * {@inheritdoc}
   */
  public function setMappingDefaults(array $mapping_defaults): SchemaDotOrgMappingInterface {
    $this->mappingDefaults = $mapping_defaults;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies(): SchemaDotOrgMappingInterface {
    parent::calculateDependencies();
    $target_entity_type = $this->entityTypeManager()->getDefinition($this->target_entity_type_id);

    // Add provider module as a dependency.
    $this->addDependency('module', $target_entity_type->getProvider());

    // Create dependency on the bundle.
    $bundle_config_dependency = $target_entity_type->getBundleConfigDependency($this->getTargetBundle());
    $this->addDependency($bundle_config_dependency['type'], $bundle_config_dependency['name']);

    // If field.module is enabled, add dependencies on 'field_config' entities
    // for both displayed and hidden fields. We intentionally leave out base
    // field overrides, since the field still exists without them.
    if (\Drupal::moduleHandler()->moduleExists('field')) {
      $properties = $this->schema_properties;
      $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($this->getTargetEntityTypeId(), $this->getTargetBundle());
      foreach (array_intersect_key($field_definitions, $properties) as $field_definition) {
        if ($field_definition instanceof ConfigEntityInterface && $field_definition->getEntityTypeId() === 'field_config') {
          $this->addDependency('config', $field_definition->getConfigDependencyName());
        }
      }
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function onDependencyRemoval(array $dependencies): bool {
    $changed = parent::onDependencyRemoval($dependencies);
    foreach ($dependencies['config'] as $entity) {
      if ($entity->getEntityTypeId() === 'field_config') {
        /** @var \Drupal\field\FieldConfigInterface $entity */
        // Remove properties for fields that are being deleted.
        if ($this->getSchemaPropertyMapping($entity->getName())) {
          $this->removeSchemaProperty($entity->getName());
          $changed = TRUE;
        }
      }
    }
    return $changed;
  }

  /**
   * Returns the plugin dependencies being removed.
   *
   * The function recursively computes the intersection between all plugin
   * dependencies and all removed dependencies.
   *
   * Note: The two arguments do not have the same structure.
   *
   * @param array[] $plugin_dependencies
   *   A list of dependencies having the same structure as the return value of
   *   ConfigEntityInterface::calculateDependencies().
   * @param array[] $removed_dependencies
   *   A list of dependencies having the same structure as the input argument of
   *   ConfigEntityInterface::onDependencyRemoval().
   *
   * @return array
   *   A recursively computed intersection.
   *
   * @see \Drupal\Core\Config\Entity\ConfigEntityInterface::calculateDependencies()
   * @see \Drupal\Core\Config\Entity\ConfigEntityInterface::onDependencyRemoval()
   */
  protected function getPluginRemovedDependencies(array $plugin_dependencies, array $removed_dependencies): array {
    $intersect = [];
    foreach ($plugin_dependencies as $type => $dependencies) {
      if ($removed_dependencies[$type]) {
        // Config and content entities have the dependency names as keys while
        // module and theme dependencies are indexed arrays of dependency names.
        // @see \Drupal\Core\Config\ConfigManager::callOnDependencyRemoval()
        if (in_array($type, ['config', 'content'])) {
          $removed = array_intersect_key($removed_dependencies[$type], array_flip($dependencies));
        }
        else {
          $removed = array_values(array_intersect($removed_dependencies[$type], $dependencies));
        }
        if ($removed) {
          $intersect[$type] = $removed;
        }
      }
    }
    return $intersect;
  }

  /**
   * {@inheritdoc}
   */
  public static function loadByEntity(EntityInterface $entity): ?SchemaDotOrgMappingInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = \Drupal::entityTypeManager()->getStorage('schemadotorg_mapping');
    return $mapping_storage->loadByEntity($entity);
  }

}
