<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_type_tray;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org type tray manager interface.
 */
interface SchemaDotOrgTypeTrayManagerInterface {

  /**
   * Add type tray category, icon, and thumbnail when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Sync grouped Schema.org types with type tray categories.
   *
   * Schema.org grouping will be prepended to the type tray categories.
   */
  public function syncCategories(): void;

}
