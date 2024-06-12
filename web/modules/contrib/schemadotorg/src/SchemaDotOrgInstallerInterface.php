<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

/**
 * Schema.org installer interface.
 */
interface SchemaDotOrgInstallerInterface {

  /**
   * Check installation requirements.
   *
   * @param string $phase
   *   The phase in which requirements are checked.
   *
   * @return array
   *   An associative array containing installation requirements.
   */
  public function requirements(string $phase): array;

  /**
   * Installs the Schema.org module's properties and types.
   */
  public function install(): void;

  /**
   * Gets Schema.org properties and types database schema.
   *
   * @return array
   *   A schema definition structure array.
   */
  public function schema(): array;

  /**
   * Download and cleanup Schema.org CSV data.
   */
  public function downloadCsvData(): void;

  /**
   * Extract translatable strings Schema.org CSV data.
   */
  public function translateCsvData(): void;

  /**
   * Import Schema.org types and properties tables.
   */
  public function importTables(): void;

  /**
   * Validate Schema.org data file path or URL.
   *
   * @param string $file
   *   The Schema.org data file path or URL.
   *
   * @return bool
   *   TRUE if the Schema.org data file path or URL is valid.
   */
  public function validateFileName(string $file): bool;

}
