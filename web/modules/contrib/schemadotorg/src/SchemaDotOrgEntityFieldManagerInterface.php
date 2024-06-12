<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Entity\EntityInterface;

/**
 * Schema.org entity field manager interface.
 */
interface SchemaDotOrgEntityFieldManagerInterface {

  /**
   * Add new field mapping option.
   */
  const ADD_FIELD = '_add_';

  /**
   * Determine if a field exists.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The name of the bundle.
   * @param string $field_name
   *   The field name.
   *
   * @return bool
   *   TRUE if a field exists.
   */
  public function fieldExists(string $entity_type_id, string $bundle, string $field_name): bool;

  /**
   * Determine if a field storage exists.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $field_name
   *   THe field name.
   *
   * @return bool
   *   TRUE if a field storage exists\.
   */
  public function fieldStorageExists(string $entity_type_id, string $field_name): bool;

  /**
   * Gets an existing field instance.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $field_name
   *   The field name.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   An existing field instance.
   */
  public function getField(string $entity_type_id, string $field_name): ?EntityInterface;

  /**
   * Get a Schema.org property's default field settings.
   *
   * @param string $type
   *   The Schema.org type.
   * @param string $property
   *   The Schema.org property.
   *
   * @return array
   *   A Schema.org property's default field settings.
   */
  public function getPropertyDefaultField(string $type, string $property): array;

  /**
   * Gets a Schema.org type's property's available field types as options.
   *
   * @param string $type
   *   The Schema.org type.
   * @param string $property
   *   The Schema.org property.
   *
   * @return array[]
   *   A property's available field types as options.
   */
  public function getPropertyFieldTypeOptions(string $type, string $property): array;

  /**
   * Gets available fields as options.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The name of the bundle.
   *
   * @return array
   *   Available fields as options.
   */
  public function getFieldOptions(string $entity_type_id, string $bundle): array;

  /**
   * Gets field types for Schema.org property.
   *
   * Field types are determined via the following order.
   * - Schema.org property specific field types.
   * - Schema.org entity reference.
   * - Schema.org enumerations.
   * - Drupal allowed values.
   * - Schema.org (data) type specific field types.
   * - String or entity reference.
   * - Alter field types.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   *
   * @return array
   *   Field types for Schema.org property.
   *
   * @see hook_schemadotorg_property_field_type_alter()
   */
  public function getSchemaPropertyFieldTypes(string $schema_type, string $schema_property): array;

}
