<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_mappings;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;
use Drupal\schemadotorg_jsonld\Utility\SchemaDotOrgJsonLdHelper;

/**
 * Schema.org additional mappings JSON-LD manager.
 */
class SchemaDotOrgAdditionalMappingsJsonLdManager implements SchemaDotOrgAdditionalMappingsJsonLdManagerInterface {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgAdditionalMappingsJsonLdManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface|null $schemaJsonLdManager
   *   The Schema.org JSON-LD manager service.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface|null $schemaJsonLdBuilder
   *   The Schema.org JSON-LD builder service.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected ?SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager = NULL,
    protected ?SchemaDotOrgJsonLdBuilderInterface $schemaJsonLdBuilder = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function entityAlter(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping, BubbleableMetadata $bubbleable_metadata): void {
    // Make sure this is an entity with a mapping.
    if (!$mapping) {
      return;
    }

    $schema_type = $mapping->getSchemaType();
    $additional_mappings = $mapping->getAdditionalMappings();
    foreach ($additional_mappings as $additional_mapping_schema_type => $additional_mapping) {
      if ($this->isWebPage($additional_mapping_schema_type)) {
        continue;
      }

      $additional_data = $this->getAdditionalData($entity, $mapping, $additional_mapping) ?? [];

      // Move the PronounceableText entity to its corresponding Schema.org property.
      if ($additional_mapping_schema_type === 'PronounceableText' && $additional_data) {
        $additional_mapping_schema_property_mapping = array_flip($additional_mapping['schema_properties']);
        $text_value_field_name = $additional_mapping_schema_property_mapping['textValue'] ?? '';
        $text_value_schema_property = $mapping->getSchemaPropertyMapping($text_value_field_name);
        if ($text_value_schema_property) {
          $data[$text_value_schema_property] = $additional_data;
          continue;
        }
      }

      // Move the Person to Quotation--creator.
      if ($schema_type === 'Quotation' && $additional_mapping_schema_type === 'Person' && $additional_data) {
        SchemaDotOrgJsonLdHelper::appendValue($data, 'creator', $additional_data);
        continue;
      }

      // Append the @type.
      $data['@type'] = array_merge(
        (array) $data['@type'],
        (array) $additional_mapping_schema_type
      );
      // Append the additional data, which can be empty.
      $data += $additional_data;
    }

    $data = $this->schemaJsonLdManager->sortProperties($data);
  }

  /**
   * {@inheritdoc}
   */
  public function alter(array &$data, RouteMatchInterface $route_match, BubbleableMetadata $bubbleable_metadata): void {
    $entity = $this->schemaJsonLdManager->getRouteMatchEntity($route_match);
    if (!$entity) {
      return;
    }

    $mapping = $this->getMappingStorage()->loadByEntity($entity);
    if (!$mapping) {
      return;
    }

    $additional_mappings = $mapping->getAdditionalMappings();
    foreach ($additional_mappings as $schema_type => $additional_mapping) {
      if (!$this->isWebPage($schema_type)) {
        continue;
      }

      $additional_data = $this->getAdditionalData($entity, $mapping, $additional_mapping);
      $data['schemadotorg_jsonld_entity'] = $additional_data
        + ['mainEntity' => $data['schemadotorg_jsonld_entity']];

      $data['schemadotorg_jsonld_entity'] = $this->schemaJsonLdManager->sortProperties(
        $data['schemadotorg_jsonld_entity']
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function schemaPropertyAlter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void {
    // Check that this this is an entity reference field.
    if (!$item instanceof EntityReferenceItem) {
      return;
    }

    // Check that the entity reference value includes as a Schema.org type.
    if (!is_array($value) || !isset($value['@type'])) {
      return;
    }

    $field_storage = $item->getFieldDefinition()->getFieldStorageDefinition();
    $source_mapping = $this->getMappingStorage()->loadByEntity($item->getEntity());
    $source_field_name = $field_storage->getName();
    $source_schema_property = $source_mapping->getSchemaPropertyMapping($source_field_name);
    if (!$source_schema_property) {
      return;
    }

    // Get the target's entity, mapping, and types.
    $target_entity = $item->entity;
    $target_mapping = $this->getMappingStorage()->loadByEntity($target_entity);
    $target_additional_mappings = $target_mapping->getAdditionalMappings();
    $target_schema_types = (array) $value['@type'];
    if ($target_additional_mappings) {
      $target_schema_types = array_merge($target_schema_types, array_keys($target_additional_mappings));
    }

    // Remove target Schema.org types are not subtype of
    // the Schema.org property's range includes.
    $source_range_includes = $this->schemaTypeManager->getPropertyRangeIncludes($source_schema_property);
    foreach ($target_schema_types as $index => $target_schema_type) {
      if (!$this->schemaTypeManager->isSubTypeOf($target_schema_type, $source_range_includes)) {
        unset($target_schema_types[$index]);
      }
    }
    $target_schema_types = array_values($target_schema_types);

    // Update the @type to include the updated target Schema.org types.
    switch (count($target_schema_types)) {
      case 0;
        $value = NULL;
        break;

      case 1;
        $value['@type'] = reset($target_schema_types);
        break;

      default:
        $value['@type'] = $target_schema_types;
        break;
    }
  }

  /**
   * Determine if a Schema.org type is a WebPage.
   *
   * @param string $schema_type
   *   A Schema.org type.
   *
   * @return bool
   *   TRUE if the Schema.org type is a WebPage.
   */
  protected function isWebPage(string $schema_type): bool {
    return $this->schemaTypeManager->isSubTypeOf($schema_type, 'WebPage');
  }

  /**
   * Get the JSON-LD data for an additional Schema.org mapping.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org (main) mapping.
   * @param array $additional_mapping
   *   An additional Schema.org mapping.
   *
   * @return array|null
   *   The JSON-LD data for an additional Schema.org mapping.
   */
  protected function getAdditionalData(EntityInterface $entity, SchemaDotOrgMappingInterface $mapping, array $additional_mapping): ?array {
    $schema_properties = $additional_mapping['schema_properties'];

    // Create a temp additional mapping, which does not need to be saved.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $additional_mapping */
    $additional_mapping = $this->getMappingStorage()->create([
      'target_entity_type_id' => $mapping->getTargetEntityTypeId(),
      'target_bundle' => $mapping->getTargetBundle(),
      'schema_type' => $additional_mapping['schema_type'],
      'schema_properties' => $schema_properties,
    ]);

    return $this->schemaJsonLdBuilder->buildEntity($entity, $additional_mapping);
  }

}
