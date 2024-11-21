<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit;

/**
 * Schema.org starter kit converter interface.
 */
interface SchemaDotOrgStarterkitConverterInterface {

  /**
   * Convert a Schema.org starter kit's to a recipe.
   *
   * @param string $module_name
   *   A Schema.org starter kit module name.
   */
  public function convert(string $module_name): void;

}
