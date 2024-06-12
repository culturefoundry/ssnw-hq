<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_custom_field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org Custom Field JSON-LD manager.
 */
class SchemaDotOrgCustomFieldJsonLdManager implements SchemaDotOrgCustomFieldJsonLdManagerInterface {

  /**
   * Constructs a SchemaDotOrgCustomFieldJsonLdManager object.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg_custom_field\SchemaDotOrgCustomFieldManagerInterface $schemaCustomFieldManager
   *   The Schema.org Custom Field manager.
   */
  public function __construct(
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgCustomFieldManagerInterface $schemaCustomFieldManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function jsonLdSchemaPropertyAlter(mixed &$value, FieldItemInterface $item): void {
    $mapping = $this->schemaCustomFieldManager->getFieldItemSchemaMapping($item);
    if (!$mapping) {
      return;
    }

    $field_name = $item->getFieldDefinition()->getName();
    $mapping_schema_type = $mapping->getSchemaType();
    $schema_property = $mapping->getSchemaPropertyMapping($field_name);

    // Check to see if the property has custom field settings.
    $default_schema_properties = $this->schemaCustomFieldManager->getDefaultProperties($mapping_schema_type, $schema_property);
    if (!$default_schema_properties) {
      return;
    }

    $data = [
      '@type' => $default_schema_properties['type'],
    ];
    $values = $item->getValue();
    foreach ($values as $item_key => $item_value) {
      $item_property = $this->schemaNames->snakeCaseToCamelCase($item_key);
      $has_value = ($item_value !== '' && $item_value !== NULL);
      $is_property = $this->schemaTypeManager->isProperty($item_property);
      if (!$has_value || !$is_property) {
        continue;
      }

      $unit = $this->schemaTypeManager->getPropertyUnit($item_property, $item_value);
      if ($unit) {
        $item_value .= ' ' . $unit;
      }

      $data[$item_property] = $item_value;
    }
    $value = $data;
  }

}
