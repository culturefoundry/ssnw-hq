<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Traits;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface;

/**
 * Trait for access Schema.org mapping and mapping storage.
 *
 * The below code allow developer to access Schema.org mapping and mapping
 * storage and entities via type-safe code.
 */
trait SchemaDotOrgMappingStorageTrait {

  /**
   * Gets the Schema.org mapping storage.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface
   *   The Schema.org mapping storage
   */
  protected function getMappingStorage(): SchemaDotOrgMappingStorageInterface|ConfigEntityStorageInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $storage */
    $storage = method_exists($this, 'entityTypeManager')
      ? $this->entityTypeManager()->getStorage('schemadotorg_mapping')
      : $this->entityTypeManager->getStorage('schemadotorg_mapping');
    return $storage;
  }

  /**
   * Load a Schema.org mapping.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null
   *   A Schema.org mapping.
   */
  protected function loadMapping(string $entity_type_id, ?string $bundle): ?SchemaDotOrgMappingInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $this->getMappingStorage()->load("$entity_type_id.$bundle");
    return $mapping;
  }

  /**
   * Gets the Schema.org mapping type storage.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface
   *   The Schema.org mapping type storage
   */
  protected function getMappingTypeStorage(): SchemaDotOrgMappingTypeStorageInterface|ConfigEntityStorageInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface $storage */
    $storage = method_exists($this, 'entityTypeManager')
      ? $this->entityTypeManager()->getStorage('schemadotorg_mapping_type')
      : $this->entityTypeManager->getStorage('schemadotorg_mapping_type');
    return $storage;
  }

  /**
   * Load a Schema.org mapping type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null
   *   A Schema.org mapping type.
   */
  protected function loadMappingType(string $entity_type_id): ?SchemaDotOrgMappingTypeInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
    $mapping_type = $this->getMappingTypeStorage()->load($entity_type_id);
    return $mapping_type;
  }

}
