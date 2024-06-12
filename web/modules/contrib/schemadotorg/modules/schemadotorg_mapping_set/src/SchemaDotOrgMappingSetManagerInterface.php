<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mapping_set;

/**
 * Schema.org mapping set manager interface.
 */
interface SchemaDotOrgMappingSetManagerInterface {

  /**
   * Determine if a Schema.org mapping set is already setup.
   *
   * @param string $name
   *   The Schema.org mapping set name.
   *
   * @return bool
   *   If a Schema.org mapping set is already setup.
   */
  public function isSetup(string $name): bool;

  /**
   * Get mapping sets for an entity type and Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $schema_type
   *   The Schema.org type.
   * @param bool|null $is_setup
   *   Optional filter to return mapping sets that are set up (TRUE)
   *   or not set up (FALSE).
   *
   * @return array
   *   An associative array of mappings sets.
   */
  public function getMappingSets(string $entity_type_id, string $schema_type, ?bool $is_setup = NULL): array;

  /**
   * Get Schema.org types from mapping set name.
   *
   * @param string $name
   *   Schema.org mapping set name.
   * @param bool $required
   *   Include required types.
   *
   * @return array
   *   Schema.org types.
   */
  public function getTypes(string $name, bool $required = FALSE): array;

  /**
   * Setup the Schema.org mapping set.
   *
   * @param string $name
   *   The name of mapping set.
   *
   * @return array
   *   An array of messages.
   */
  public function setup(string $name): array;

  /**
   * Teardown the Schema.org mapping set.
   *
   * @param string $name
   *   The name of mapping set.
   *
   * @return array
   *   An array of messages.
   */
  public function teardown(string $name): array;

  /**
   * Generate the Schema.org mapping set.
   *
   * @param string $name
   *   The name of mapping set.
   */
  public function generate(string $name): void;

  /**
   * Kill the Schema.org mapping set.
   *
   * @param string $name
   *   The name of mapping set.
   */
  public function kill(string $name): void;

}
