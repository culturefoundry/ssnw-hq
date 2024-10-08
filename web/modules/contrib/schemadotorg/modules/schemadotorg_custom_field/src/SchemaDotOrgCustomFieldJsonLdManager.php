<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_custom_field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;

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
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface|null $schemaJsonLdManager
   *   The Schema.org JSON-LD manager service.
   */
  public function __construct(
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgCustomFieldManagerInterface $schemaCustomFieldManager,
    protected ?SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager = NULL,
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
    $schema_property = $mapping->getSchemaPropertyMapping($field_name, TRUE);

    // Check to see if the property has custom field settings.
    $custom_field_settings = $this->schemaCustomFieldManager->getDefaultProperties(
      entity_type_id: $mapping->getTargetEntityTypeId(),
      bundle: $mapping->getTargetBundle(),
      schema_type: $mapping_schema_type,
      schema_property: $schema_property,
    );
    if (!$custom_field_settings) {
      return;
    }

    $custom_field_schema_type = $custom_field_settings['schema_type'];
    $data = [
      '@type' => $custom_field_schema_type,
    ];

    // Append custom field properties to the Schema.org data.
    $values = $item->getValue();
    foreach ($values as $item_key => $item_value) {
      $item_property = $this->schemaNames->snakeCaseToCamelCase($item_key);
      $has_value = ($item_value !== '' && $item_value !== NULL);
      $is_property = $this->schemaTypeManager->isProperty($item_property);
      if (!$has_value || !$is_property) {
        continue;
      }

      $prefix = $custom_field_settings['schema_properties'][$item_property]['prefix'] ?? NULL;
      if ($prefix) {
        $item_value = $prefix . $item_value;
      }

      $suffix = $custom_field_settings['schema_properties'][$item_property]['suffix'] ?? NULL;
      if ($suffix) {
        $item_value .= $suffix;
      }

      $data[$item_property] = $this->schemaJsonLdManager->getSchemaPropertyValueDefaultSchemaType(
        $custom_field_schema_type,
        $item_property,
        $item_value
      );
    }

    $value = $data;
  }

}
