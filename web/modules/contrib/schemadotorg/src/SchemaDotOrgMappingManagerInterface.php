<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

/**
 * Schema.org mapping manager interface.
 */
interface SchemaDotOrgMappingManagerInterface {

  /**
   * Gets ignored Schema.org properties.
   *
   * @return array
   *   Ignored Schema.org properties.
   */
  public function getIgnoredProperties(): array;

  /**
   * Get Schema.org mapping default values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $defaults
   *   Mapping defaults for the entity and properties.
   *
   * @return array
   *   Schema.org mapping default values.
   */
  public function getMappingDefaults(string $entity_type_id = '', ?string $bundle = NULL, string $schema_type = '', array $defaults = []): array;

  /**
   * Get Schema.org mapping default values using a type.
   *
   * @param string $type
   *   A mapping definition type which can be `entity_type_id:schema_type`
   *   or `entity_type_id:bundle:schema_type`.
   * @param array $defaults
   *   Mapping defaults for the entity and properties.
   *
   * @return array
   *   Schema.org mapping default values.
   */
  public function getMappingDefaultsByType(string $type, array $defaults = []): array;

  /**
   * Save a Schema.org mapping and create associate entity type and fields.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $values
   *   The entity, subtype, and property values.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface
   *   A Schema.org mapping.
   */
  public function saveMapping(string $entity_type_id, string $schema_type, array $values): SchemaDotOrgMappingInterface;

  /**
   * Validate create Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   */
  public function createTypeValidate(string $entity_type_id, string $schema_type): void;

  /**
   * Create Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $defaults
   *   Mapping defaults for the entity and properties.
   */
  public function createType(string $entity_type_id, string $schema_type, array $defaults = []): void;

  /**
   * Create default Schema.org for a specified entity type.
   *
   * This method is generally called with an Schema.org entity type specific
   * module is installed.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   *
   * @see schemadotorg_media_install()
   * @see schemadotorg_block_install()
   * @see \Drupal\Tests\schemadotorg_media\Kernel\SchemaDotOrgMediaInstallKernelTest
   * @see \Drupal\Tests\schemadotorg_block\Kernel\SchemaDotOrgBlockInstallTest
   */
  public function createDefaultTypes(string $entity_type_id): void;

  /**
   * Validate delete Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   */
  public function deleteTypeValidate(string $entity_type_id, string $schema_type): void;

  /**
   * Delete Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $options
   *   (optional) An array of options.
   */
  public function deleteType(string $entity_type_id, string $schema_type, array $options = []): void;

}
