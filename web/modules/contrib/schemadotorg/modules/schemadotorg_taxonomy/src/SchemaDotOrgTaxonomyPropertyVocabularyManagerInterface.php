<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

/**
 * Schema.org taxonomy vocabulary property manager interface.
 */
interface SchemaDotOrgTaxonomyPropertyVocabularyManagerInterface {

  /**
   * Alter the field types for Schema.org property.
   *
   * @param array $field_types
   *   An array of field types.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   *
   * @see hook_schemadotorg_property_field_type_alter()
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
   *
   * @see hook_schemadotorg_property_field_alter()
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

}
