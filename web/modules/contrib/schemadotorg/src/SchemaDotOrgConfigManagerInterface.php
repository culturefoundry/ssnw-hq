<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

/**
 * Schema.org config manager interface.
 */
interface SchemaDotOrgConfigManagerInterface {

  /**
   * Set Schema.org type's default properties.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param array|string $properties
   *   Schema.org properties to be set.
   */
  public function setSchemaTypeDefaultProperties(string $schema_type, array|string $properties): void;

  /**
   * Unset Schema.org type's default properties.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param array|string $properties
   *   Schema.org properties to be unset.
   */
  public function unsetSchemaTypeDefaultProperties(string $schema_type, array|string $properties): void;

  /**
   * Set Schema.org mapping type's default properties.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array|string $properties
   *   Schema.org properties to be set.
   */
  public function setMappingTypeSchemaTypeDefaultProperties(string $entity_type_id, string $schema_type, array|string $properties): void;

  /**
   * Unset Schema.org mapping type's default properties.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array|string $properties
   *   Schema.org properties to be unset.
   */
  public function unsetMappingTypeSchemaTypeDefaultProperties(string $entity_type_id, string $schema_type, array|string $properties): void;

  /**
   * Repair configuration.
   */
  public function repair(): void;

  /**
   * Check schema compliance in configuration object.
   *
   * @param string $config_name
   *   Configuration name.
   * @param string $key
   *   A string that maps to a key within the configuration data.
   * @param mixed $value
   *   Value to associate with the key.
   *
   * @return array|bool
   *   FALSE if no schema found. List of errors if any found. TRUE if fully
   *   valid.
   *
   * @throws \Drupal\Core\Config\Schema\SchemaIncompleteException
   */
  public function checkConfigValue(string $config_name, string $key, mixed $value): bool|array;

}
