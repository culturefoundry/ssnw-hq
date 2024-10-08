<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_entity_reference_override;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Schema.org entity reference override interface.
 */
interface SchemaDotOrgEntityReferenceOverrideManagerInterface {

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

  /**
   * Single element entity reference override autocomplete form alter.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array $context
   *   The alter context.
   *
   * @see hook_field_widget_single_element_WIDGET_TYPE_form_alter()
   */
  public function singleElementEntityReferenceOverrideFormAlter(array &$element, FormStateInterface $form_state, array $context): void;

  /**
   * Alter the Schema.org property JSON-LD value for an entity's field item.
   *
   * @param mixed $value
   *   Alter the Schema.org property JSON-LD value.
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The entity's field item.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @see hook_schemadotorg_jsonld_schema_property_alter()
   */
  public function jsonLdSchemaPropertyAlter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void;

}
