<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Storage controller class for "schemadotorg_mapping_type" configuration entities.
 *
 * The Schema.org mapping type storage manages which and how Drupal entity
 * type can be mapped to Schema.org types.
 */
class SchemaDotOrgMappingTypeStorage extends ConfigEntityStorage implements SchemaDotOrgMappingTypeStorageInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypes(): array {
    $entity_type_ids = array_keys($this->loadMultiple());
    $entity_types = [];
    foreach ($entity_type_ids as $entity_type_id) {
      if ($this->entityTypeManager->hasDefinition($entity_type_id)) {
        $entity_types[$entity_type_id] = $entity_type_id;
      }
    }
    return $entity_types;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypesWithBundles(): array {
    $entity_type_ids = array_keys($this->loadMultiple());
    $entity_types = [];
    foreach ($entity_type_ids as $entity_type_id) {
      if ($this->entityTypeManager->hasDefinition($entity_type_id)
        && $this->entityTypeManager->getDefinition($entity_type_id)->getBundleEntityType()) {
        $entity_types[$entity_type_id] = $entity_type_id;
      }
    }
    return $entity_types;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeBundles(): array {
    $entity_types = $this->getEntityTypes();

    $items = [];
    foreach ($entity_types as $entity_type_id) {
      // Make sure the entity is supported.
      if (!$this->entityTypeManager->hasDefinition($entity_type_id)) {
        continue;
      }

      /** @var \Drupal\Core\Config\Entity\ConfigEntityTypeInterface $entity_type */
      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

      // Make sure the entity has a field UI.
      $route_name = $entity_type->get('field_ui_base_route');
      if (!$route_name) {
        continue;
      }

      // Make sure the bundle entity exists.
      $bundle_entity_type_id = $entity_type->getBundleEntityType();
      if (!$bundle_entity_type_id) {
        continue;
      }

      $items[$entity_type_id] = $entity_type;
    }
    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeBundleDefinitions(): array {
    $items = [];
    $entity_types = $this->getEntityTypeBundles();
    foreach ($entity_types as $entity_type_id => $entity_type) {
      $bundle_entity_type_id = $entity_type->getBundleEntityType();

      /** @var \Drupal\Core\Config\Entity\ConfigEntityTypeInterface $bundle_entity_type */
      $bundle_entity_type = $this->entityTypeManager->getDefinition($bundle_entity_type_id);

      $items[$entity_type_id] = $bundle_entity_type;
    }
    return $items;
  }

}
