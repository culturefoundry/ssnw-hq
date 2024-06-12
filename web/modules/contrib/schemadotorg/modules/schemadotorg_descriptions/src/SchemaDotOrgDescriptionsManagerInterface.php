<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_descriptions;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Schema.org descriptions manager interfaces.
 */
interface SchemaDotOrgDescriptionsManagerInterface {

  /**
   * Add description to entities loaded on their collection page..
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   The entities keyed by entity ID.
   * @param string $entity_type_id
   *   The type of entities being loaded (i.e. node, user, comment).
   */
  public function entityLoad(array $entities, string $entity_type_id): void;

  /**
   * Alter node form and a description.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function nodeFormAlter(array &$form, FormStateInterface $form_state): void;

  /**
   * Alter the Schema.org Blueprints UI mapping form.
   *
   * Add support for descriptions.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   A string that is the unique ID of the form.
   */
  public function mappingFormAlter(array &$form, FormStateInterface $form_state, string $form_id): void;

  /**
   * Acts on an entity object about to be shown on an entity form.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that is about to be shown on the form.
   * @param string $operation
   *   The current operation.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function entityPrepareForm(EntityInterface $entity, string $operation, FormStateInterface $form_state): void;

  /**
   * Perform alterations before a form is rendered.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   A string that is the unique ID of the form.
   */
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void;

  /**
   * Alter bundle entity type before it is created.
   *
   * @param array &$values
   *   The bundle entity type values.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $entity_type_id
   *   The entity type ID.
   */
  public function bundleEntityAlter(array &$values, string $schema_type, string $entity_type_id): void;

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

}
