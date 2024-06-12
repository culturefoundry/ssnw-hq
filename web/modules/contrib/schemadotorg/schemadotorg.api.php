<?php

/**
 * @file
 * Hooks to define and alter mappings, entity types and fields.
 */

declare(strict_types=1);

// phpcs:disable DrupalPractice.CodeAnalysis.VariableAnalysis.UnusedVariable

/**
 * @addtogroup hooks
 * @{
 */

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
function hook_schemadotorg_property_field_type_alter(array &$field_types, string $schema_type, string $schema_property): void {
  // Use SmartDate for startDate and endDate.
  if (in_array($schema_property, ['startDate', 'endData'])
    || \Drupal::moduleHandler()->moduleExists('smartdate')) {
    $field_types = ['smartdate' => 'smartdate'] + $field_types;
  }
}

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
function hook_schemadotorg_property_field_prepare(array &$default_field, string $schema_type, string $schema_property): void {
  // Programmatically update the name field for an Event Schema.org type.
  if ($schema_type === 'Event' && $schema_property === 'name') {
    $default_field['name']['label'] = t('Event title');
  }
}

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
function hook_schemadotorg_bundle_entity_alter(array &$values, string $schema_type, string $entity_type_id): void {
  $entity_values =& $values['entity'];

  // Remove the description from the bundle entity before it is created.
  // @see schemadotorg_descriptions_schemadotorg_bundle_entity_alter()
  /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
  $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
  /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface $schema_type_builder */
  $schema_type_builder = \Drupal::service('schemadotorg.schema_type_builder');

  $definition = $schema_type_manager->getType($schema_type);
  $description = $schema_type_builder->formatComment($definition['comment'], ['base_path' => 'https://schema.org/']);
  if ($entity_values['description'] === $description) {
    $entity_values['description'] = '';
  }
}

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
function hook_schemadotorg_property_field_alter(
  string $schema_type,
  string $schema_property,
  array &$field_storage_values,
  array &$field_values,
  ?string &$widget_id,
  array &$widget_settings,
  ?string &$formatter_id,
  array &$formatter_settings,
): void {
  // @todo Provide example code.
}

/**
 * Alter Schema.org mapping entity default values.
 *
 * @param array $defaults
 *   The Schema.org mapping entity default values.
 * @param string $entity_type_id
 *   The entity type ID.
 * @param string|null $bundle
 *   The bundle.
 * @param string $schema_type
 *   The Schema.org type.
 */
function hook_schemadotorg_mapping_defaults_alter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void {
  // @todo Provide example code.
}

/**
 * Respond to inserts/updates to an entity of a particular type.
 *
 * This hook runs after an entity insert or update.
 * Get the original entity object from $entity->original.
 *
 * The below hook is used to add additional Schema.org mappings after a mapping
 * has been inserted or updated.
 *
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   The entity object.
 *
 * @ingroup entity_crud
 * @see schemadotorg_additional_mappings_schemadotorg_mapping_postsave()
 *
 * @see hook_entity_postsave()
 * @see https://www.drupal.org/project/drupal/issues/2221347
 */
function hook_ENTITY_TYPE_postsave(\Drupal\Core\Entity\EntityInterface $entity): void {
  // @todo Provide some example code.
}

/**
 * @} End of "addtogroup hooks".
 */
