<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_metatag;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org meta tag interface.
 */
interface SchemaDotOrgMetatagManagerInterface {

  /**
   * Creates metatag field when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Add metatag defaults when a mapping is saved.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   */
  public function mappingPresave(SchemaDotOrgMappingInterface $mapping): void;

}
