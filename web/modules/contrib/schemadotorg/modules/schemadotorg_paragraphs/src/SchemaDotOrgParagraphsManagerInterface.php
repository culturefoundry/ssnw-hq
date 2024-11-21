<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_paragraphs;

use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org paragraphs manager interface.
 */
interface SchemaDotOrgParagraphsManagerInterface {

  /**
   * Implements hook_ENTITY_TYPE_presave().
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
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
   * Update Schema.org paragraph field config before it is saved.
   *
   * @param \Drupal\Core\Field\FieldConfigInterface $field_config
   *   The field config.
   */
  public function fieldConfigPresave(FieldConfigInterface $field_config): void;

  /**
   * Alter the complete form for field widgets.
   *
   * @param array $field_widget_complete_form
   *   The field widget form element as constructed by
   *   \Drupal\Core\Field\WidgetBaseInterface::form().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $context
   *   An associative array. See hook_field_widget_complete_form_alter()
   *   for the structure and content of the array.
   *
   * @see hook_field_widget_complete_form_alter()
   */
  public function fieldWidgetCompleteFormAlter(array &$field_widget_complete_form, FormStateInterface $form_state, array $context): void;

  /**
   * Set paragraphs type icon before it is saved..
   *
   * @param \Drupal\paragraphs\ParagraphsTypeInterface $paragraphs_type
   *   The paragraphs type.
   *
   * @see \Drupal\paragraphs\Form\ParagraphsTypeForm::validateForm
   */
  public function paragraphsTypePresave(ParagraphsTypeInterface $paragraphs_type): void;

}
