<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mercury_editor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * The Schema.org Mercury Editor manager interface.
 */
interface SchemaDotOrgMercuryEditorManagerInterface {

  /**
   * Alter the Schema.org Blueprints UI mapping form.
   *
   * Appends (via the Mercury Editor) to Layout Paragraphs widgets.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function mappingFormAlter(array &$form, FormStateInterface &$form_state): void;

  /**
   * Enables Mercury Editor for Schema.org mapping before it is saved.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingPresave(SchemaDotOrgMappingInterface $mapping): void;

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
   * @see schemadotorg_paragraphs_schemadotorg_property_field_alter()
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
