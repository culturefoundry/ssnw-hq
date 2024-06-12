<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_custom_field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org Custom Field manager interface.
 */
interface SchemaDotOrgCustomFieldManagerInterface {

  /**
   * Alter the field types for Schema.org property.
   *
   * @param array $field_types
   *   An array of field types.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   */
  public function propertyFieldTypeAlter(array &$field_types, string $schema_type, string $schema_property): void;

  /**
   * Alter field storage and field values before they are created.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param array $field_storage_values
   *   Field storage config values.
   * @param array $field_values
   *   Field config values.
   * @param string|null $widget_id
   *   The plugin ID of the widget.
   * @param array $widget_settings
   *   An array of widget settings.
   * @param string|null $formatter_id
   *   The plugin ID of the formatter.
   * @param array $formatter_settings
   *   An array of formatter settings.
   */
  public function propertyFieldAlter(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void;

  /**
   * Get a custom field's Schema.org default properties.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   *
   * @return array|null
   *   A custom field's Schema.org default properties.
   */
  public function getDefaultProperties(string $schema_type, string $schema_property): ?array;

  /**
   * Prepare a property's field data before the Schema.org mapping form.
   *
   * @param array &$default_field
   *   The default values used in the Schema.org mapping form.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   */
  public function propertyFieldPrepare(array &$default_field, string $schema_type, string $schema_property): void;

  /**
   * Get a custom field's Schema.org mapping.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface|\Drupal\Core\Field\FieldItemInterface $item
   *   A custom field item or custom field items.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null
   *   A Schema.org mapping.
   */
  public function getFieldItemSchemaMapping(FieldItemListInterface|FieldItemInterface $item): ?SchemaDotOrgMappingInterface;

}
