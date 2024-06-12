<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Config\Entity\ImportableEntityStorageInterface;

/**
 * Provides an interface for 'schemadotorg_mapping_type' storage.
 */
interface SchemaDotOrgMappingTypeStorageInterface extends ConfigEntityStorageInterface, ImportableEntityStorageInterface {

  /**
   * Gets entity types that implement Schema.org.
   *
   * @return array<int|string>
   *   Entity types that implement Schema.org.
   */
  public function getEntityTypes(): array;

  /**
   * Gets entity types with bundles that implement Schema.org.
   *
   * @return array<int|string>
   *   Entity types with bundles that implement Schema.org.
   */
  public function getEntityTypesWithBundles(): array;

  /**
   * Get entity type bundles. (i.e node)
   *
   * @return array<\Drupal\Core\Config\Entity\ConfigEntityTypeInterface>
   *   Entity type bundles.
   */
  public function getEntityTypeBundles(): array;

  /**
   * Get entity type bundle definitions. (i.e. node_type)
   *
   * @return array<\Drupal\Core\Config\Entity\ConfigEntityTypeInterface>
   *   Entity type bundle definitions.
   */
  public function getEntityTypeBundleDefinitions(): array;

}
