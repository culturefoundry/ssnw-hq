<?php

/**
 * @file
 * Schema.org JSON-LD hook implementations for contributed modules.
 */

declare(strict_types=1);

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;

/* ************************************************************************** */
// Datetime range.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_jsonld_schema_type_field_alter() for the datetime_range.module.
 *
 * Sets the endDate property for a single startDate property that is
 * using a daterange.
 */
function datetime_range_schemadotorg_jsonld_schema_type_field_alter(array &$data, FieldItemListInterface $items): void {
  $field_definition = $items->getFieldDefinition();

  // Only alter the Schema.org type data that have a daterange field.
  if ($field_definition->getType() !== 'daterange') {
    return;
  }

  // Only alter the Schema.org type data that have a single daterange field.
  $cardinality = $field_definition->getFieldStorageDefinition()->getCardinality();
  if ($cardinality !== 1) {
    return;
  }

  // Get Schema.org mapping.
  $mapping = SchemaDotOrgMapping::loadByEntity($items->getEntity());

  // Check that the field is mapped to startDate.
  $schema_property = $mapping->getSchemaPropertyMapping($field_definition->getName());
  if ($schema_property !== 'startDate') {
    return;
  }

  // Check that the mapping's Schema.org type has an endDate property.
  /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
  $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
  $schema_type = $mapping->getSchemaType();

  // Set the endDate property.
  if ($schema_type_manager->hasProperty($schema_type, 'endDate')
    && empty($data['endDate'])
    && !empty($items->end_value)) {
    $data['endDate'] = $items->end_value;
  }

  // @todo Set the duration property.
}

/* ************************************************************************** */
// Range.
/* ************************************************************************** */

/**
 * Implements hook_schemadotorg_jsonld_schema_property_alter() for the range.module.
 */
function range_schemadotorg_jsonld_schema_property_alter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void {
  $field_definition = $item->getFieldDefinition();

  // Only alter the Schema.org type data that have a daterange field.
  if (!in_array($field_definition->getType(), ['range_decimal', 'range_integer', 'range_float'])) {
    return;
  }

  // Get Schema.org mapping.
  $mapping = SchemaDotOrgMapping::loadByEntity($item->getEntity());
  $schema_property = $mapping->getSchemaPropertyMapping($field_definition->getName());

  // Get the Schema.org type via the property's range includes.
  // @see https://schema.org/minValue
  // @see https://schema.org/maxValue
  /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
  $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
  $range_includes = $schema_type_manager->getPropertyRangeIncludes($schema_property);
  $schema_types_with_min_max_value = ['MonetaryAmount'];
  $found_schema_types = array_intersect($range_includes, $schema_types_with_min_max_value);
  $schema_type = ($found_schema_types) ? reset($found_schema_types) : NULL;

  if ($schema_type) {
    $value = [
      '@type' => $schema_type,
      'minValue' => $item->from,
      'maxValue' => $item->to,
    ];
    $value += \Drupal::config('schemadotorg.settings')
      ->get("schema_types.default_property_values.$schema_type") ?? [];
  }
  elseif ($item->from === $item->to) {
    $value = $item->from;
  }
  else {
    $value = ($item->from + $item->to) / 2;
  }
}
