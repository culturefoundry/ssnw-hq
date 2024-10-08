<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Entity\Display\EntityDisplayInterface;

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
   *   The entity type id.
   * @param string $bundle
   *   The Schema.org property.
   * @param string $field_name
   *   The field name.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   *
   * @return int
   *   The default field weight for Schema.org property.
   */
  public function getSchemaPropertyDefaultFieldWeight(string $entity_type_id, string $bundle, string $field_name, string $schema_type, string $schema_property): int;

  /**
   * Initialize all form and view displays for a new Schema.org mapping.
   *
   * This method saves all form and view displays for a new Schema.org mapping
   * with a $display->schemaDotOrgType = 'SchemaType'; defined;
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A new Schema.org mapping.
   */
  public function initializeDisplays(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Set the display settings for a field.
   *
   * @param array $field
   *   The field definition.
   * @param string|null $widget_id
   *   The widget ID.
   * @param array $widget_settings
   *   The settings for the widget.
   * @param string|null $formatter_id
   *   The formatter ID.
   * @param array $formatter_settings
   *   The settings for the formatter.
   */
  public function setFieldDisplays(
    array $field,
    ?string $widget_id,
    array $widget_settings,
    ?string $formatter_id,
    array $formatter_settings,
  ): void;

  /**
   * Set the default component weights for a Schema.org mapping entity.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function setComponentWeights(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Get display modes for a specific entity display.
   *
   * @param \Drupal\Core\Entity\Display\EntityDisplayInterface $display
   *   The entity display.
   *
   * @return array
   *   An array of display modes.
   */
  public function getModes(EntityDisplayInterface $display): array;

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
