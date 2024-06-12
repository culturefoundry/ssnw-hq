<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Schema.org schema data type manager interface.
 */
interface SchemaDotOrgSchemaTypeManagerInterface {

  /**
   * Schema.org type.
   */
  const SCHEMA_TYPES = 'types';

  /**
   * Schema.org property.
   */
  const SCHEMA_PROPERTIES = 'properties';

  /**
   * The Schema.org base URI.
   */
  const URI = 'https://schema.org/';

  /**
   * Gets Schema.org type or property URI.
   *
   * @param string $id
   *   The Schema.org type or property.
   *
   * @return string
   *   The Schema.org type or property URI.
   */
  public function getUri(string $id): string;

  /**
   * Determine if an ID is in a valid Schema.org table.
   *
   * @param string $table
   *   The Schema.org table.
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org type.
   */
  public function isId(string $table, string $id): bool;

  /**
   * Determine if an ID is a Schema.org type or property.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org type or property.
   */
  public function isItem(string $id): bool;

  /**
   * Determine if an ID is a Schema.org type.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org type.
   */
  public function isType(string $id): bool;

  /**
   * Determine if a Schema.org type is a subtype of another Schema.org type.
   *
   * @param string $type
   *   The Schema.org type.
   * @param string|array $subtype_of
   *   The Schema.org subtype of.
   *
   * @return bool
   *   TRUE if the Schema.org type is a subtype of another Schema.org type.
   */
  public function isSubTypeOf(string $type, string|array $subtype_of): bool;

  /**
   * Determine if an ID is a Schema.org Thing type.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org Thing type, excludes data types
   *   and enumerations.
   */
  public function isThing(string $id): bool;

  /**
   * Determine if an ID is a Schema.org data type.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org data type.
   */
  public function isDataType(string $id): bool;

  /**
   * Determine if an ID is a Schema.org Intangible.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org Intangible.
   */
  public function isIntangible(string $id): bool;

  /**
   * Determine if an ID is a Schema.org enumeration type.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org enumeration type.
   */
  public function isEnumerationType(string $id): bool;

  /**
   * Determine if an ID is a Schema.org enumeration value.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is the Schema.org enumeration value.
   */
  public function isEnumerationValue(string $id): bool;

  /**
   * Determine if an ID is a Schema.org property.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the ID is a Schema.org property.
   */
  public function isProperty(string $id): bool;

  /**
   * Determine if a Schema.org property is a sub property.
   *
   * @param string $property
   *   A Schema.org property.
   * @param string $subproperty_of
   *   A Schema.org property.
   *
   * @return bool
   *   TRUE if a Schema.org property is a sub property.
   */
  public function isSubPropertyOf(string $property, string $subproperty_of): bool;

  /**
   * Determine if Schema.org ID is superseded.
   *
   * @param string $id
   *   The Schema.org ID.
   *
   * @return bool
   *   TRUE if the Schema.org ID is superseded
   */
  public function isSuperseded(string $id): bool;

  /**
   * Determine if Schema.org property is use at the main entity.
   *
   * @param string $id
   *   The Schema.org property.
   *
   * @return bool
   *   TRUE if Schema.org property is use at the main entity.
   */
  public function isPropertyMainEntity(string $id): bool;

  /**
   * Parse Schema.org type or property IDs from a comma delimited list of URLs.
   *
   * @param string $text
   *   A comma delimited list of Schema.org URLs.
   *
   * @return string[]
   *   An array of Schema.org types.
   */
  public function parseIds(string $text): array;

  /**
   * Gets Schema.org type or property item.
   *
   * @param string $table
   *   The Schema.org table.
   * @param string $id
   *   The Schema.org type or property ID.
   * @param array $fields
   *   Optional. The fields to be returned.
   *
   * @return array|false
   *   An associative array containing Schema.org type or property item.
   *   or FALSE if there is no type found.
   */
  public function getItem(string $table, string $id, array $fields = []): array|FALSE;

  /**
   * Gets Schema.org type.
   *
   * @param string $type
   *   The Schema.org type.
   * @param array $fields
   *   Optional. Fields to be returned.
   *
   * @return array|false
   *   An associative array containing Schema.org type definition,
   *   or FALSE if there is no type found.
   */
  public function getType(string $type, array $fields = []): array|FALSE;

  /**
   * Gets Schema.org property.
   *
   * @param string $property
   *   The Schema.org property.
   * @param array $fields
   *   Optional. Fields to be returned.
   *
   * @return array|false
   *   An associative array containing Schema.org property definition,
   *   or FALSE if there is no property found.
   */
  public function getProperty(string $property, array $fields = []): array|FALSE;

  /**
   * Get a Schema.org property's range includes.
   *
   * @param string $property
   *   The Schema.org property.
   *
   * @return array|false
   *   The Schema.org property's range includes.
   */
  public function getPropertyRangeIncludes(string $property): array|FALSE;

  /**
   * Get a Schema.org property's default Schema.org type from range_includes.
   *
   * @param string $property
   *   The Schema.org property.
   *
   * @return string|null
   *   The Schema.org property's default Schema.org type from range_includes.
   */
  public function getPropertyDefaultType(string $property): ?string;

  /**
   * Gets Schema.org property's unit.
   *
   * @param string $property
   *   The Schema.org property.
   * @param int|string|null $value
   *   A numeric value.
   *
   * @return string|\Drupal\Core\StringTranslation\TranslatableMarkup|null
   *   The Schema.org property's unit.
   */
  public function getPropertyUnit(string $property, int|string|null $value = 0): string|TranslatableMarkup|NULL;

  /**
   * Gets Schema.org type or property items.
   *
   * @param string $table
   *   The Schema.org table.
   * @param array $ids
   *   An array of Schema.org type or property IDs.
   * @param array $fields
   *   Optional. The fields to be returned.
   *
   * @return array
   *   An array containing Schema.org type or property items.
   */
  public function getItems(string $table, array $ids, array $fields = []): array;

  /**
   * Gets Schema.org types.
   *
   * @param array $types
   *   The Schema.org types.
   * @param array $fields
   *   Optional. Fields to be returned.
   *
   * @return array
   *   An array containing Schema.org types.
   */
  public function getTypes(array $types, array $fields = []): array;

  /**
   * Gets Schema.org properties.
   *
   * @param array $properties
   *   The Schema.org properties.
   * @param array $fields
   *   Optional. Fields to be returned.
   *
   * @return array
   *   An array containing Schema.org types.
   */
  public function getProperties(array $properties, array $fields = []): array;

  /**
   * Gets a Schema.org type's properties.
   *
   * @param string $type
   *   The Schema.org type.
   * @param array $fields
   *   An array of Schema.org property fields.
   *
   * @return array
   *   An associative array of a Schema.org type's properties.
   */
  public function getTypeProperties(string $type, array $fields = []): array;

  /**
   * Gets all child Schema.org types below a specified type.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   An associative array of Schema.org types keyed by type.
   */
  public function getTypeChildren(string $type): array;

  /**
   * Gets all child Schema.org types below a specified type.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   An associative array of Schema.org types as options
   */
  public function getTypeChildrenAsOptions(string $type): array;

  /**
   * Gets all child Schema.org types below a specified type.
   *
   * @param string $type
   *   The Schema.org type.
   * @param array $ignored_types
   *   An array of ignored Schema.org type ids.
   *
   * @return array
   *   An associative array of Schema.org types as options
   */
  public function getAllTypeChildrenAsOptions(string $type, array $ignored_types = []): array;

  /**
   * Gets Schema.org subtypes.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   An array containing Schema.org subtypes.
   */
  public function getSubtypes(string $type): array;

  /**
   * Gets Schema.org enumerations.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   An array containing Schema.org enumerations.
   */
  public function getEnumerations(string $type): array;

  /**
   * Gets Schema.org data types.
   *
   * @return array|string[]
   *   An array of data types.
   */
  public function getDataTypes(): array;

  /**
   * Get parent Schema.org types for specified Schema.org type.
   *
   * @param string $type
   *   A Schema.org type.
   *
   * @return array
   *   An array parent Schema.org types for the specified Schema.org type.
   */
  public function getParentTypes(string $type): array;

  /**
   * Gets all Schema.org subtypes below specified Schema.org types.
   *
   * @param array $types
   *   An array of Schema.org types.
   *
   * @return array
   *   An array of Schema.org subtypes which includes the specified
   *   Schema.org types.
   */
  public function getAllSubTypes(array $types): array;

  /**
   * Gets all Schema.org types below a specified type.
   *
   * @param string $type
   *   The Schema.org type.
   * @param array $fields
   *   An array of Schema.org type fields.
   * @param array $ignored_types
   *   An array of ignored Schema.org type ids.
   *
   * @return array
   *   An associative array of Schema.org types keyed by type.
   */
  public function getAllTypeChildren(string $type, array $fields = [], array $ignored_types = []): array;

  /**
   * Gets Schema.org type hierarchical tree.
   *
   * @param string|array $type
   *   The Schema.org type.
   * @param array $ignored_types
   *   An array of ignored Schema.org types.
   *
   * @return array
   *   An associative nested array containing Schema.org type hierarchical tree.
   */
  public function getTypeTree(string|array $type, array $ignored_types = []): array;

  /**
   * Gets Schema.org type breadcrumbs.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   An associative nested array containing Schema.org type breadcrumbs.
   */
  public function getTypeBreadcrumbs(string $type): array;

  /**
   * Determine if a Schema.org type has a Schema.org property.
   *
   * @param string $type
   *   The Schema.org type.
   * @param string $property
   *   The Schema.org property.
   *
   * @return bool
   *   TRUE if the Schema.org type has a Schema.org property.
   */
  public function hasProperty(string $type, string $property): bool;

  /**
   * Determine if a Schema.org type has subtypes.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return bool
   *   TRUE if the Schema.org type has subtypes.
   */
  public function hasSubtypes(string $type): bool;

  /**
   * Get setting from an associative array of settings.
   *
   * @param array $settings
   *   An associative array of settings.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface|array $parts
   *   A Schema.org mapping or an associative array of setting name parts
   *   which includes.
   *   - schema_type: The Schema.org type.
   *   - entity_type_id: The entity type id.
   *   - bundle: The entity bundle.
   *   - schema_property: The Schema.org property.
   *   - field_name: The field name.
   * @param bool $multiple
   *   Return multiple matches.
   *   Defaults to FALSE and returns the first match.
   *
   * @return mixed
   *   A setting from an associative array of settings.
   */
  public function getSetting(array $settings, SchemaDotOrgMappingInterface|array $parts, bool $multiple = FALSE): mixed;

}
