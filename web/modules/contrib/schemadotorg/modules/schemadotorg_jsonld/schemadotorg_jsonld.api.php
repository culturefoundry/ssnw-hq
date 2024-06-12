<?php

/**
 * @file
 * Hooks to define and alter Schema.org JSON-LD.
 *
 * NOTE: Modules can move all Schema.org JSON-LD hooks into a dedicated
 * MODULE_NAME.schemadotorg.inc file.
 *
 * @see schemadotorg_jsonld.schemadotorg.inc
 */

declare(strict_types=1);

// phpcs:disable DrupalPractice.CodeAnalysis.VariableAnalysis.UnusedVariable

/**
 * @addtogroup hooks
 * @{
 */

/* ************************************************************************** */
// Custom data.
/* ************************************************************************** */

/**
 * Provide custom Schema.org JSON-LD data for a route.
 *
 * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
 *   The current route match.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 *
 * @return array|null
 *   Custom Schema.org JSON-LD data.
 */
function hook_schemadotorg_jsonld(\Drupal\Core\Routing\RouteMatchInterface $route_match, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): ?array {
  return [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'item' => [
          '@id' => 'https://example.com/dresses',
          'name' => 'Dresses',
        ],
      ],
      [
        '@type' => 'ListItem',
        'position' => 2,
        'item' => [
          '@id' => 'https://example.com/dresses/real',
          'name' => 'Real Dresses',
        ],
      ],
    ],
  ];
}

/* ************************************************************************** */
// Schema.org JSON-LD.
/* ************************************************************************** */

/**
 * Alter the Schema.org JSON-LD data for the current route.
 *
 * @param array $data
 *   The Schema.org JSON-LD data for the current route.
 * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
 *   The current route match.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 */
function hook_schemadotorg_jsonld_alter(array &$data, \Drupal\Core\Routing\RouteMatchInterface $route_match, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): void {
  /** @var \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface $manager */
  $manager = \Drupal::service('schemadotorg_jsonld.manager');
  $entity = $manager->getRouteMatchEntity($route_match);
  if (!$entity) {
    return;
  }

  $mapping_storage = \Drupal::entityTypeManager()->getStorage('schemadotorg_mapping');
  $mapping = $mapping_storage->loadByEntity($entity);
  // Make sure the mapping exists.
  if (!$mapping) {
    return;
  }

  $schema_type = $mapping->getSchemaType();
  $schema_properties = $mapping->getSchemaProperties();
}

/* ************************************************************************** */
// Entity data.
/* ************************************************************************** */

/**
 * Load the Schema.org type JSON-LD data for an entity.
 *
 * Modules can define custom JSON-LD data for any entity type.
 *
 * @param array $data
 *   The Schema.org JSON-LD data for an entity.
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   The entity.
 * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
 *   The entity's Schema.org mapping.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 */
function hook_schemadotorg_jsonld_schema_type_entity_load(array &$data, \Drupal\Core\Entity\EntityInterface $entity, \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): void {
  // Make sure this is a taxonomy term entity with a mapping.
  if (!$entity instanceof \Drupal\taxonomy\VocabularyInterface
    || !$mapping) {
    return;
  }

  $schema_type = $mapping->getSchemaType();
  $data['@type'] = "{$schema_type}Set";
  $data['name'] = $entity->label();
  if ($entity->getDescription()) {
    $data['description'] = $entity->getDescription();
  }
}

/**
 * Alter the Schema.org JSON-LD data for an entity.
 *
 * Besides, altering an existing Schema.org mapping's JSON-LD data, modules can
 * define custom JSON-LD data for any entity type.
 *
 * @param array $data
 *   The Schema.org JSON-LD data for an entity.
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   The entity.
 * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping
 *   The entity's Schema.org mapping.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 */
function hook_schemadotorg_jsonld_schema_type_entity_alter(array &$data, \Drupal\Core\Entity\EntityInterface $entity, ?\Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): void {
  if (!$entity instanceof \Drupal\taxonomy\TermInterface
    || !$mapping) {
    return;
  }

  // Check that the term is mapping to a DefinedTerm or CategoryCode.
  $schema_type = $mapping->getSchemaType();
  $is_defined_term = in_array($schema_type, ['DefinedTerm', 'CategoryCode']);
  if (!$is_defined_term) {
    return;
  }

  // Append isDefinedTermSet or isCategoryCodeSet data to the type data.
  $vocabulary = $entity->get('vid')->entity;
  /** @var \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface $jsonld_builder */
  $jsonld_builder = \Drupal::service('schemadotorg_jsonld.builder');
  $vocabulary_data = $jsonld_builder->buildEntity($vocabulary);
  $data["in{$schema_type}Set"] = $vocabulary_data;
}

/**
 * Alter the Schema.org JSON-LD data for a field item list.
 *
 * This hook allows a field to alter the other Schema.org properties outside
 * the field's Schema.org property mapping.
 *
 * @param array &$data
 *   The Schema.org JSON-LD data for an entity.
 * @param \Drupal\Core\Field\FieldItemListInterface $items
 *   A field item list.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 */
function hook_schemadotorg_jsonld_schema_type_field_alter(array &$data, \Drupal\Core\Field\FieldItemListInterface $items, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): void {
  $field_storage = $items->getFieldDefinition()->getFieldStorageDefinition();
  $field_type = $field_storage->getType();
  if ($field_type === 'text_with_summary') {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = \Drupal::entityTypeManager()->getStorage('schemadotorg_mapping');
    $mapping = $mapping_storage->loadByEntity($items->getEntity());
    $field_name = $field_storage->getName();
    $cardinality = $field_storage->getCardinality();
    $schema_property = $mapping->getSchemaPropertyMapping($field_name);
    // For text and articleBody properties set the description
    // to the summary.
    if (in_array($schema_property, ['text', 'articleBody']) && $cardinality === 1) {
      $data['description'] = (string) check_markup($items->summary, $items->format);
    }
  }
}

/* ************************************************************************** */
// Field items/item value.
/* ************************************************************************** */

/**
 * Alter the Schema.org property JSON-LD value for an entity's field item.
 *
 * @param mixed $value
 *   Alter the Schema.org property JSON-LD value.
 * @param \Drupal\Core\Field\FieldItemInterface $item
 *   The entity's field item.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 */
function hook_schemadotorg_jsonld_schema_property_alter(mixed &$value, \Drupal\Core\Field\FieldItemInterface $item, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): void {
  // Get entity information.
  $entity = $item->getEntity();
  $entity_type_id = $entity->getEntityTypeId();
  $bundle = $entity->bundle();

  // Get field information.
  $field_storage_definition = $item->getFieldDefinition()->getFieldStorageDefinition();
  $field_name = $item->getFieldDefinition()->getName();
  $field_type = $item->getFieldDefinition()->getType();
  $field_property_names = $item->getFieldDefinition()->getFieldStorageDefinition()->getPropertyNames();

  // Get main property information.
  $main_property_name = $field_storage_definition->getMainPropertyName();
  $main_property_definition = $field_storage_definition->getPropertyDefinition($main_property_name);
  $main_property_data_type = $main_property_definition->getDataType();

  // Get Schema.org mapping.
  /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
  $mapping_storage = \Drupal::entityTypeManager()->getStorage('schemadotorg_mapping');
  $mapping = $mapping_storage->loadByEntity($entity);
  $schema_type = $mapping->getSchemaType();
  $schema_property = $mapping->getSchemaPropertyMapping($field_name);

  // Massage the data.
  // ...
}

/**
 * Alter the Schema.org property JSON-LD values for an entity's field items.
 *
 * This hook is triggered after
 * hook_schemadotorg_jsonld_schema_property_alter().
 *
 * @param array $values
 *   Alter the Schema.org property JSON-LD values.
 * @param \Drupal\Core\Field\FieldItemListInterface $items
 *   The entity's field items.
 * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
 *   Object to collect JSON-LD's bubbleable metadata.
 */
function hook_schemadotorg_jsonld_schema_properties_alter(array &$values, \Drupal\Core\Field\FieldItemListInterface $items, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata): void {
  // @todo Provide some example code.
}

/**
 * @} End of "addtogroup hooks".
 */
