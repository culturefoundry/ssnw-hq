<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit;

/**
 * Schema.org starter kit manager interface.
 */
interface SchemaDotOrgStarterkitManagerInterface {

  /**
   * Determine if a module is Schema.org Blueprints Starter Kit.
   *
   * @param string $module
   *   A module.
   *
   * @return bool
   *   TRUE if a module is Schema.org Blueprints Starter Kit.
   */
  public function isStarterkit(string $module): bool;

  /**
   * Get a list of Schema.org starter kits.
   *
   * @param bool $installed
   *   Return only installed starter kits.
   *
   * @return array
   *   A list of Schema.org starter kits.
   */
  public function getStarterkits(bool $installed = FALSE): array;

  /**
   * Get a Schema.org starter kit's module info.
   *
   * @param string $module
   *   A module name.
   *
   * @return array|null
   *   A Schema.org starter kit's module info.
   */
  public function getStarterkit(string $module): ?array;

  /**
   * Get a module's Schema.org Blueprints starter kit settings.
   *
   * @param string $module
   *   A module name.
   *
   * @return false|array
   *   A module's Schema.org Blueprints starter kit settings.
   *   FALSE if the module is not a Schema.org Blueprints starter kit
   */
  public function getStarterkitSettings(string $module): FALSE|array;

  /**
   * Install a Schema.org starter kit.
   *
   * @param string $module
   *   A Schema.org starter kit module name.
   */
  public function install(string $module): void;

  /**
   * Update a Schema.org starter kit.
   *
   * @param string $module
   *   A Schema.org starter kit module name.
   */
  public function update(string $module): void;

  /**
   * Generate a Schema.org starter kit's content.
   *
   * @param string $module
   *   A Schema.org starter kit module name.
   */
  public function generate(string $module): void;

  /**
   * Kill a Schema.org starter kit's content.
   *
   * @param string $module
   *   A Schema.org starter kit module name.
   */
  public function kill(string $module): void;

  /**
   * Preinstall a Schema.org Blueprints starter kit.
   *
   * @param string $module
   *   A module.
   */
  public function preinstall(string $module): void;

  /**
   * Install a Schema.org Blueprints starter kits.
   *
   * @param array $modules
   *   An array of modules being installed.
   */
  public function installed(array $modules): void;

}
