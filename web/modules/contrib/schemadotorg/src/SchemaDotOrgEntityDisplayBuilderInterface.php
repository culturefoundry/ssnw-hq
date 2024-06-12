<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

/**
 * Schema.org entity display builder interface.
 */
interface SchemaDotOrgEntityDisplayBuilderInterface {

  /**
   * Hide component from entity display.
   */
  const COMPONENT_HIDDEN = 'schemadotorg_component_hidden';

  /**
   * Gets default field weights.
   *
   * @return array
   *   An array containing default field weights.
   */
  public function getDefaultFieldWeights(): array;

  /**
   * Get the default field weight for Schema.org property.
   *
   * @param string $entity_type_id
   *   The Schema.org property.
   * @param string $field_name
   *   The entity type.
   * @param string $schema_property
   *   The field name.
   *
   * @return int
   *   The default field weight for Schema.org property.
   */
  public function getSchemaPropertyDefaultFieldWeight(string $entity_type_id, string $field_name, string $schema_property): int;

  /**
   * Set entity displays for a field.
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
  public function setFieldDisplays(
    string $schema_type,
    string $schema_property,
    array $field_storage_values,
    array $field_values,
    ?string $widget_id,
    array $widget_settings,
    ?string $formatter_id,
    array $formatter_settings,
  ): void;

  /**
   * Set entity display field weights for Schema.org properties.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   * @param array $properties
   *   The Schema.org properties to be weighted.
   */
  public function setFieldWeights(SchemaDotOrgMappingInterface $mapping, array $properties = []): void;

  /**
   * Set the default component weights for a Schema.org mapping entity.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function setComponentWeights(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Get display form modes for a specific entity type.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle
   *   The bundle.
   *
   * @return array
   *   An array of display form modes.
   */
  public function getFormModes(string $entity_type_id, string $bundle): array;

  /**
   * Get display view modes for a specific entity type.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle
   *   The bundle.
   *
   * @return array
   *   An array of display view modes.
   */
  public function getViewModes(string $entity_type_id, string $bundle): array;

}
