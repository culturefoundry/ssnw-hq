<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_scheduler;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * The Schema.org scheduler manager interface.
 */
interface SchemaDotOrgSchedulerManagerInterface {

  /**
   * Add scheduler settings  when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

}
