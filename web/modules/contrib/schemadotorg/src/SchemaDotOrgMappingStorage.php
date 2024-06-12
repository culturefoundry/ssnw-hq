<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Storage controller class for "schemadotorg_mapping" configuration entities.
 *
 * The Schema.org mapping storage makes is easier to load and examine
 * Schema.org mappings for types and properties to bundles and fields.
 *
 * This storage service also makes it possible to look up related entity
 * bundles based on Schema.org types.
 */
class SchemaDotOrgMappingStorage extends ConfigEntityStorage implements SchemaDotOrgMappingStorageInterface {

  /**
   * The entity type manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Schema.org names service.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org entity display builder.
   */
  protected SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaEntityDisplayBuilder = $container->get('schemadotorg.entity_display_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function isEntityMapped(EntityInterface $entity): bool {
    return $this->isBundleMapped($entity->getEntityTypeId(), $entity->bundle());
  }

  /**
   * {@inheritdoc}
   */
  public function isBundleMapped(string $entity_type_id, string $bundle): bool {
    return (boolean) $this->getQuery()
      ->condition('target_entity_type_id', $entity_type_id)
      ->condition('target_bundle', $bundle)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaType(string $entity_type_id, string $bundle): ?string {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $entity */
    $entity = $this->load($entity_type_id . '.' . $bundle);
    if (!$entity) {
      return NULL;
    }
    return $entity->getSchemaType();
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyName(string $entity_type_id, string $bundle, string $field_name): ?string {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $entity */
    $entity = $this->load($entity_type_id . '.' . $bundle);
    if (!$entity) {
      return NULL;
    }
    return $entity->getSchemaPropertyMapping($field_name) ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyRangeIncludes(string $schema_type, string $schema_property): array {
    $schema_properties_range_includes = $this->configFactory
      ->get('schemadotorg.settings')
      ->get("schema_properties.range_includes");
    $range_includes = $this->schemaTypeManager->getSetting(
      $schema_properties_range_includes,
      ['schema_type' => $schema_type, 'schema_property' => $schema_property]
    ) ?? $this->schemaTypeManager->getPropertyRangeIncludes($schema_property);
    return array_combine($range_includes, $range_includes);
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyTargetBundles(string $target_type, string $schema_type, string $schema_property): array {
    $range_includes = $this->getSchemaPropertyRangeIncludes($schema_type, $schema_property);
    return $this->getRangeIncludesTargetBundles($target_type, $range_includes);
  }

  /**
   * {@inheritdoc}
   */
  public function getRangeIncludesTargetBundles(string $target_type, array $range_includes, $ignore_thing = TRUE): array {
    // Ignore 'Thing' because it is too generic.
    if ($ignore_thing) {
      unset($range_includes['Thing']);
    }

    // If the range includes Thing, we can return all the mapping
    // target bundles.
    if (isset($range_includes['Thing'])) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
      $mappings = $this->loadByProperties(['target_entity_type_id' => $target_type]);
      $target_bundles = [];
      foreach ($mappings as $mapping) {
        $target_bundle = $mapping->getTargetBundle();
        $target_bundles[$target_bundle] = $target_bundle;
      }
      return $target_bundles;
    }

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
    $mappings = $this->loadMultipleBySchemaType($target_type, $range_includes);

    $target_bundles = [];
    foreach ($mappings as $mapping) {
      $target_bundle = $mapping->getTargetBundle();
      $target_bundles[$target_bundle] = $target_bundle;
    }
    return $target_bundles;
  }

  /**
   * {@inheritdoc}
   */
  public function isSchemaTypeMapped(?string $entity_type_id, ?string $schema_type): bool {
    if (empty($entity_type_id) || empty($schema_type)) {
      return FALSE;
    }

    return (boolean) $this->getQuery()
      ->condition('target_entity_type_id', $entity_type_id)
      ->condition('schema_type', $schema_type)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function isValidType(string $type): bool {
    if (!str_contains($type, ':')) {
      return FALSE;
    }

    [$entity_type_id, , $schema_type] = $this->parseType($type);
    return $this->entityTypeManager->hasDefinition($entity_type_id)
      && $this->schemaTypeManager->isType($schema_type);
  }

  /**
   * {@inheritdoc}
   */
  public function parseType(string $type): array {
    $args = explode(':', $type);
    if (count($args) <= 1) {
      throw new \Exception('Type must contain an entity_type_id and a Schema.org. (i.e, node:WebPage)');
    }

    if (count($args) == 2) {
      return [$args[0], NULL, $args[1]];
    }
    else {
      return [$args[0], $args[1], $args[2]];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadByType(string $type): ?SchemaDotOrgMappingInterface {
    [$entity_type_id, $bundle, $schema_type] = $this->parseType($type);
    return ($bundle)
      ? $this->loadByBundle($entity_type_id, $bundle)
      : $this->loadBySchemaType($entity_type_id, $schema_type);
  }

  /**
   * {@inheritdoc}
   */
  public function loadByBundle(string $entity_type_id, string $bundle): ?SchemaDotOrgMappingInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $entity */
    $entity = $this->load("$entity_type_id.$bundle");
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function loadBySchemaType(string $entity_type_id, string $schema_type): ?SchemaDotOrgMappingInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $entities */
    $entities = $this->loadByProperties([
      'target_entity_type_id' => $entity_type_id,
      'schema_type' => $schema_type,
    ]);
    return ($entities) ? reset($entities) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultipleBySchemaType(string $entity_type_id, array|string $schema_type): array {
    $type_children = $this->schemaTypeManager->getAllSubTypes((array) $schema_type);

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
    $mappings = $this->loadByProperties(['target_entity_type_id' => $entity_type_id]);
    foreach ($mappings as $mapping_id => $mapping) {
      $is_subtype = array_key_exists($mapping->getSchemaType(), $type_children);
      $has_additional_mapping = (bool) array_intersect_key($mapping->getAdditionalMappings(), $type_children);
      if (!$is_subtype && !$has_additional_mapping) {
        unset($mappings[$mapping_id]);
      }
    }

    return $mappings;
  }

  /**
   * {@inheritdoc}
   */
  public function loadByEntity(EntityInterface $entity): ?SchemaDotOrgMappingInterface {
    if (!$this->isEntityMapped($entity)) {
      return NULL;
    }

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $entities */
    $entities = $this->loadByProperties([
      'target_entity_type_id' => $entity->getEntityTypeId(),
      'target_bundle' => $entity->bundle(),
    ]);
    return ($entities) ? reset($entities) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  protected function doPostSave(EntityInterface $entity, $update): void {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $entity */
    parent::doPostSave($entity, $update);

    // Issue #2221347: Add hook_entity_postsave hook.
    //
    // The below hook is used to add additional Schema.org mappings after
    // a mapping has been inserted or updated.
    //
    // @see https://www.drupal.org/project/drupal/issues/2221347
    // @see schemadotorg_additional_mappings_schemadotorg_mapping_postsave()
    $this->moduleHandler->invokeAll($this->entityTypeId . '_postsave', [$entity]);

    if (!$update) {
      $this->schemaEntityDisplayBuilder->setComponentWeights($entity);
    }
  }

}
