<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_smart_date;

/**
 * Schema.org Smart Date installer interface.
 */
interface SchemadotorgSmartDateInstallerInterface {

  /**
   * Perform set up tasks when the Schema.org Smart Date module is installed.
   *
   * @param bool $is_syncing
   *   TRUE if the module is being installed as part of a configuration import.
   */
  public function install(bool $is_syncing): void;

  /**
   * Remove any information that the Schema.org Smart Date module sets.
   *
   * @param bool $is_syncing
   *   TRUE if the module is being uninstalled as part of a configuration import.
   */
  public function uninstall(bool $is_syncing): void;

}
