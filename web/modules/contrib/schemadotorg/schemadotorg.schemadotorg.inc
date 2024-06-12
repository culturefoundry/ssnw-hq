<?php

/**
 * @file
 * Schema.org hook implementations for contributed modules.
 */

declare(strict_types=1);

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\field\Entity\FieldConfig;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/* ************************************************************************** */
// Duration field.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_property_field_alter() for the duration_field.module.
 */
function duration_field_schemadotorg_property_field_alter(
  string $schema_type,
  string $schema_property,
  array &$field_storage_values,
  array &$field_values,
  ?string &$widget_id,
  array &$widget_settings,
  ?string &$formatter_id,
  array &$formatter_settings,
): void {
  // Make sure the field type is set to 'duration'.
  if ($field_storage_values['type'] !== 'duration') {
    return;
  }

  // Set duration granularity to hours and minutes.
  $field_values['settings'] = ['granularity' => 'h:i'];
}

/**
 * Implements hook_schemadotorg_jsonld_schema_property_alter() for the duration_field.module.
 */
function duration_field_schemadotorg_jsonld_schema_property_alter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void {
  $field_type = $item->getFieldDefinition()->getType();
  if ($field_type !== 'duration') {
    return;
  }

  $value = $item->duration;
}

/* ************************************************************************** */
// Content browser.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_property_field_alter() for the content_browser.module.
 */
function content_browser_schemadotorg_property_field_alter(
  string $schema_type,
  string $schema_property,
  array &$field_storage_values,
  array &$field_values,
  ?string &$widget_id,
  array &$widget_settings,
  ?string &$formatter_id,
  array &$formatter_settings,
): void {
  // Make sure the field type is set to 'entity_reference'.
  if (!in_array($field_storage_values['type'], ['entity_reference', 'entity_reference_revisions'])) {
    return;
  }

  // Use the content browser for node entity references.
  $target_type = $field_storage_values['settings']['target_type'];
  if ($target_type === 'node') {
    $widget_id = 'entity_browser_entity_reference';
    $widget_settings = [
      'entity_browser' => 'browse_content',
      'field_widget_display' => 'label',
      'field_widget_edit' => TRUE,
      'field_widget_remove' => TRUE,
      'field_widget_replace' => TRUE,
      'open' => FALSE,
      'field_widget_display_settings' => [],
      'selection_mode' => 'selection_append',
    ];
  }
}

/* ************************************************************************** */
// Focal point.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_mapping_insert() for the focal_point.module.
 *
 * Ensure that existing entity image fields use focal point.
 * (i.e. media:image and node:article)
 */
function focal_point_schemadotorg_mapping_insert(SchemaDotOrgMappingInterface $mapping): void {
  if ($mapping->isSyncing()) {
    return;
  }

  /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
  $entity_field_manager = \Drupal::service('entity_field.manager');
  /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository */
  $entity_display_repository = \Drupal::service('entity_display.repository');

  $entity_type_id = $mapping->getTargetEntityTypeId();
  $bundle = $mapping->getTargetBundle();

  $field_definitions = array_intersect_key(
    $entity_field_manager->getFieldDefinitions($entity_type_id, $bundle),
    $mapping->getSchemaProperties()
  );
  foreach ($field_definitions as $field_name => $field_definition) {
    if ($field_definition->getType() !== 'image'
      || !$field_definition instanceof FieldConfig) {
      continue;
    }

    $form_display = $entity_display_repository->getFormDisplay($entity_type_id, $bundle);
    $component = $form_display->getComponent($field_name);
    if ($component['type'] !== 'image_focal_point') {
      $component['type'] = 'image_focal_point';
      $component['settings'] = [
        'progress_indicator' => 'throbber',
        'preview_image_style' => 'medium',
        'preview_link' => TRUE,
        'offsets' => '50,50',
      ];
      $form_display
        ->setComponent($field_name, $component)
        ->save();
    }
  }
}

/**
 * Implements hook_schemadotorg_property_field_alter() for the focal_point.module.
 */
function focal_point_schemadotorg_property_field_alter(
  string $schema_type,
  string $schema_property,
  array &$field_storage_values,
  array &$field_values,
  ?string &$widget_id,
  array &$widget_settings,
  ?string &$formatter_id,
  array &$formatter_settings,
): void {
  // Set an image field's default form widget settings to use focal point.
  if ($field_storage_values['type'] === 'image') {
    $widget_id = 'image_focal_point';
    $widget_settings = [
      'progress_indicator' => 'throbber',
      'preview_image_style' => 'medium',
      'preview_link' => TRUE,
      'offsets' => '50,50',
    ];
  }
}

/* ************************************************************************** */
// Linkit.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_property_field_alter() for the linkit.module.
 */
function linkit_schemadotorg_property_field_alter(
  string $schema_type,
  string $schema_property,
  array &$field_storage_values,
  array &$field_values,
  ?string &$widget_id,
  array &$widget_settings,
  ?string &$formatter_id,
  array &$formatter_settings,
): void {
  // Make sure the field type is set to 'link'.
  if ($field_storage_values['type'] !== 'link') {
    return;
  }

  // Use linkit for the link URL autocompletion.
  $widget_id = 'linkit';
  $widget_settings = ['linkit_auto_link_text' => TRUE];
}

/* ************************************************************************** */
// Media Library Media Modify.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_property_field_type_alter() for the media_library_media_modify.module.
 *
 * Defaults all 'Media' (reference) fields to use the
 * 'Media with contextual modifications' field type.
 */
function media_library_media_modify_schemadotorg_property_field_type_alter(array &$field_types, string $schema_type, string $schema_property): void {
  if (isset($field_types['field_ui:entity_reference:media'])) {
    $field_types = ['entity_reference_entity_modify' => t('Media with contextual modifications')]
      + $field_types;
  }
}

/**
 * Implements hook_schemadotorg_property_field_alter() for the media_library_media_modify.module.
 *
 * Alters the 'Media with contextual modifications' field entity reference
 * handler settings and form display.
 *
 * @see \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilder::setDefaultFieldValues
 */
function media_library_media_modify_schemadotorg_property_field_alter(
  string $schema_type,
  string $schema_property,
  array &$field_storage_values,
  array &$field_values,
  ?string &$widget_id,
  array &$widget_settings,
  ?string &$formatter_id,
  array &$formatter_settings,
): void {

  // Only alter the 'Media with contextual modifications' field storage type.
  if ($field_storage_values['type'] !== 'entity_reference_entity_modify') {
    return;
  }

  /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
  $mapping_storage = \Drupal::entityTypeManager()
    ->getStorage('schemadotorg_mapping');

  /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
  $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');

  // The target type is hard-coded.
  // @see \Drupal\media_library_media_modify\Plugin\Field\FieldType\EntityReferenceEntityModifyItem
  $target_type = 'media';
  $range_includes = $mapping_storage->getSchemaPropertyRangeIncludes($schema_type, $schema_property);

  // Make sure that the ranges includes only includes Things
  // and not DataTypes or Enumerations.
  foreach ($range_includes as $range_include_type) {
    if (!$schema_type_manager->isThing($range_include_type)) {
      unset($range_includes[$range_include_type]);
    }
  }

  $handler_settings = [];
  $handler_settings['target_type'] = $target_type;
  $handler_settings['schema_types'] = $range_includes;

  $field_values['settings'] = [
    'handler' => 'schemadotorg:' . $target_type,
    'handler_settings' => $handler_settings,
  ];

  // Set the widget's form mode to media library.
  $widget_settings['form_mode'] = 'media_library';
}
