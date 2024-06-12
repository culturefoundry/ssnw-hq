<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Utility\Token;
use Drupal\file\FileInterface;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org JSON-LD builder.
 *
 * The Schema.org JSON-LD builder build and hook flow.
 * - Get custom data based on the current route match.
 * - Build mapped entity based on the current entity
 * - Load custom entity data on the current entity and related entities.
 * - Alter mapped entity data on the current entity and related entities.
 * - Alter all data based on the current route match.
 *
 * @see hook_schemadotorg_jsonld()
 * @see hook_schemadotorg_jsonld_schema_type_entity_load()
 * @see hook_schemadotorg_jsonld_schema_type_entity_alter()
 * @see hook_schemadotorg_jsonld_schema_type_field_alter()
 * @see hook_schemadotorg_jsonld_schema_property_alter()
 * @see hook_schemadotorg_jsonld_schema_properties_alter()
 * @see hook_schemadotorg_jsonld_alter()
 */
class SchemaDotOrgJsonLdBuilder implements SchemaDotOrgJsonLdBuilderInterface {

  /**
   * Constructs a SchemaDotOrgJsonLdBuilder object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager
   *   The Schema.org JSON-LD manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected RouteMatchInterface $routeMatch,
    protected Token $token,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(?RouteMatchInterface $route_match = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): ?array {
    $route_match = $route_match ?: $this->routeMatch;
    $bubbleable_metadata = $bubbleable_metadata ?: new BubbleableMetadata();
    $bubbleable_metadata->addCacheContexts(static::ROUTE_MATCH_CACHE_CONTEXTS);
    $bubbleable_metadata->addCacheTags(static::ROUTE_MATCH_CACHE_TAGS);

    $data = [];

    // Add custom data based on the route match.
    // @see hook_schemadotorg_jsonld()
    $this->moduleHandler->invokeAllWith('schemadotorg_jsonld', function (callable $hook, string $module) use (&$data, $route_match, $bubbleable_metadata): void {
      $module_data = $hook($route_match, $bubbleable_metadata);
      if ($module_data) {
        $data[$module . '_schemadotorg_jsonld'] = $module_data;
      }
    });

    // Add entity data.
    $entity = $this->schemaJsonLdManager->getRouteMatchEntity($route_match);
    $entity_data = $this->buildEntity(
      entity: $entity,
      bubbleable_metadata: $bubbleable_metadata,
    );
    if ($entity_data) {
      $data['schemadotorg_jsonld_entity'] = $entity_data;
    }

    // Alter Schema.org JSON-LD data for the current route.
    // @see hook_schemadotorg_jsonld_alter()
    $this->moduleHandler->alter('schemadotorg_jsonld', $data, $route_match, $bubbleable_metadata);

    // Return NULL if the data is empty.
    if (empty($data)) {
      return NULL;
    }

    $types = $this->getSchemaTypesFromData($data);
    return (count($types) === 1) ? reset($types) : $types;
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(?EntityInterface $entity = NULL, ?SchemaDotOrgMappingInterface $mapping = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): ?array {
    if (!$entity) {
      return [];
    }

    // If the mapping is not defined, load the entity's Schema.org mapping.
    $mapping = $mapping
      ?? SchemaDotOrgMapping::loadByEntity($entity);

    $bubbleable_metadata = $bubbleable_metadata ?? new BubbleableMetadata();
    $bubbleable_metadata->addCacheContexts(static::ENTITY_CACHE_CONTEXTS);
    $bubbleable_metadata->addCacheTags(static::ENTITY_CACHE_TAGS);

    // Track the entity via bubbleable metadata.
    $bubbleable_metadata->addCacheableDependency($entity);

    if (!$entity->access('view')) {
      return [];
    }

    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $data = $this->buildMappedEntity($entity, $mapping, $bubbleable_metadata);

    // Load Schema.org JSON-LD entity data.
    // @see schemadotorg_jsonld_schema_type_entity_load()
    $this->moduleHandler->invokeAllWith(
      'schemadotorg_jsonld_schema_type_entity_load',
      function (callable $hook) use (&$data, $entity, $mapping, $bubbleable_metadata): void {
        $hook($data, $entity, $mapping, $bubbleable_metadata);
      }
    );

    // Alter Schema.org type JSON-LD using the entity.
    // @see schemadotorg_jsonld_schema_type_entity_alter()
    $this->moduleHandler->invokeAllWith(
      'schemadotorg_jsonld_schema_type_entity_alter',
      function (callable $hook) use (&$data, $entity, $mapping, $bubbleable_metadata): void {
        $hook($data, $entity, $mapping, $bubbleable_metadata);
      }
    );

    // Sort Schema.org properties in specified order and then alphabetically.
    $data = $this->schemaJsonLdManager->sortProperties($data);

    // Return data if a Schema.org @type is defined.
    return (isset($data['@type']))
      ? $data
      : NULL;
  }

  /**
   * Build JSON-LD for an entity that is mapped to a Schema.org type.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping
   *   The entity Schema.org mapping. This is optional an only required
   *   if entity is not mapping to Schema.org type or needs to be mapped to
   *   second Schema.org type.
   * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array
   *   The JSON-LD for an entity that is mapped to a Schema.org type.
   */
  protected function buildMappedEntity(EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): array {
    // Only content entities can support a Schema.org (field) mapping.
    if (!$entity instanceof ContentEntityInterface) {
      return [];
    }

    // Load the entity's Schema.org mapping if it is not defined.
    if (!$mapping) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
      $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');
      $mapping = $mapping_storage->loadByEntity($entity);
      if (!$mapping) {
        return [];
      }
    }

    $type_data = [];
    $schema_type = $mapping->getSchemaType();
    $schema_properties = $mapping->getSchemaProperties();
    foreach ($schema_properties as $field_name => $schema_property) {
      // Make sure the entity has the field
      // and the current user has access to the field.
      if (!$entity->hasField($field_name)
        || !$entity->get($field_name)->access('view')) {
        continue;
      }

      // Get property values from field items.
      /** @var \Drupal\Core\Field\FieldItemListInterface $field_items */
      $field_items = $entity->get($field_name);
      $property_values = $this->getSchemaPropertyFieldItems($schema_type, $schema_property, $field_items, $bubbleable_metadata);

      if ($property_values) {
        // If the cardinality is 1, return the first property data item.
        $cardinality = $field_items->getFieldDefinition()
          ->getFieldStorageDefinition()
          ->getCardinality();
        $type_data[$schema_property] = ($cardinality === 1) ? reset($property_values) : $property_values;
      }
    }

    // Prepend the @type to the returned data.
    $default_data = [];
    $default_data['@type'] = $mapping->getSchemaType();

    // Prepend the @url to the returned data.
    if ($this->schemaJsonLdManager->hasSchemaUrl($entity)
      && $entity->access('view')) {
      $default_data['@url'] = $entity->toUrl('canonical')->setAbsolute()->toString();
    }

    $data = $default_data + $type_data;

    // Alter Schema.org type JSON-LD using the entity.
    // @see schemadotorg_jsonld_schema_type_entity_alter()
    foreach ($schema_properties as $field_name => $schema_property) {
      // Make sure the entity has the field and the current user has
      // access to the field.
      if (!$entity->hasField($field_name) || !$entity->get($field_name)->access('view')) {
        continue;
      }

      /** @var \Drupal\Core\Field\FieldItemListInterface $field_items */
      $field_items = $entity->get($field_name);
      $this->alterSchemaTypeFieldItems($data, $field_items, $bubbleable_metadata);
    }

    return $data;
  }

  /**
   * Alter the Schema.org JSON-LD data for a field item list.
   *
   * @param array $data
   *   The Schema.org JSON-LD data for an entity.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   A field item list.
   * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   */
  protected function alterSchemaTypeFieldItems(array &$data, FieldItemListInterface $items, ?BubbleableMetadata $bubbleable_metadata = NULL): void {
    $data += $this->schemaJsonLdManager->getSchemaTypeProperties($items);
    $this->moduleHandler->alter('schemadotorg_jsonld_schema_type_field', $data, $items, $bubbleable_metadata);
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyFieldItems(string $schema_type, string $schema_property, FieldItemListInterface $items, ?BubbleableMetadata $bubbleable_metadata = NULL): array {
    $total_items = $items->count();

    $position = 1;
    $property_values = [];
    foreach ($items as $item) {
      $property_value = $this->getSchemaPropertyFieldItem($schema_type, $schema_property, $item, $bubbleable_metadata);

      // Alter the Schema.org property's individual value.
      $this->moduleHandler->alter(
        'schemadotorg_jsonld_schema_property',
        $property_value,
        $item,
        $bubbleable_metadata
      );

      // If there is more than 1 item, see if we need to its position.
      if ($total_items > 1) {
        $property_type = (is_array($property_value))
          ? $property_value['@type'] ?? NULL
          : NULL;
        if ($property_type
          && $this->schemaTypeManager->hasProperty($property_type, 'position')) {
          $property_value['position'] = $position;
          $position++;
        }
      }

      if ($property_value !== NULL) {
        $property_values[] = $property_value;
      }
    }

    // Alter the Schema.org property's values.
    $this->moduleHandler->alter(
      'schemadotorg_jsonld_schema_properties',
      $property_values,
      $items,
      $bubbleable_metadata
    );

    return $property_values;
  }

  /**
   * Get Schema.org type property data type from field item.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param \Drupal\Core\Field\FieldItemInterface|null $item
   *   The field item.
   * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @return mixed
   *   A data type.
   */
  protected function getSchemaPropertyFieldItem(string $schema_type, string $schema_property, ?FieldItemInterface $item = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): mixed {
    if (is_null($item)) {
      return NULL;
    }

    // Make sure mapped and unmapped entities are tracked
    // via bubbleable metadata.
    if ($item->entity
      && $item->entity instanceof EntityInterface
      && $bubbleable_metadata) {
      $bubbleable_metadata->addCacheableDependency($item->entity);
    }

    // Handle entity reference except for files (and images).
    // Returns the label for unmapped entity references.
    if ($item->entity
      && $item->entity instanceof EntityInterface
      && !$item->entity instanceof FileInterface) {
      $target_entity = $item->entity;
      if (!$target_entity->access('view')) {
        return NULL;
      }

      // For Schema.org properties that can only contain a URL,
      // we need to return the entity's absolute URL.
      // @see https://schema.org/URL
      // @see https://schema.org/relatedLink
      // @see https://schema.org/significantLink
      $range_includes = $this->schemaTypeManager->getPropertyRangeIncludes($schema_property);
      if (isset($range_includes['URL']) && count($range_includes) === 1) {
        return $target_entity->toUrl('canonical')->setAbsolute()->toString();
      }

      $entity_reference_display = $this->schemaJsonLdManager->getSchemaTypeEntityReferenceDisplay($target_entity);
      switch ($entity_reference_display) {
        case SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_NONE:
          return NULL;

        case SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_ENTITY:
          return $this->buildEntity(
            entity: $item->entity,
            bubbleable_metadata: $bubbleable_metadata,
          ) ?: NULL;

        case SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_URL:
          /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
          $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');
          $mapping = $mapping_storage->loadByEntity($target_entity);
          $mapping_schema_property = ($mapping)
            ? $mapping->getSchemaPropertyMapping('title') ?? 'name'
            : 'name';
          $data = [
            '@type' => ($mapping) ? $mapping->getSchemaType() : 'Thing',
            $mapping_schema_property => $target_entity->label(),
          ];
          if ($this->schemaJsonLdManager->hasSchemaUrl($target_entity)) {
            $data['@url'] = $target_entity->toUrl('canonical')->setAbsolute()->toString();
          }
          return $data;

        case SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_LABEL:
        default:
          if (str_starts_with($entity_reference_display, '[')
            && str_ends_with($entity_reference_display, ']')) {
            $data = [$target_entity->getEntityTypeId() => $target_entity];
            return $this->token->replace($entity_reference_display, $data, [], $bubbleable_metadata);
          }
          else {
            return $target_entity->label();
          }
      }
    }
    else {
      $property_value = $this->schemaJsonLdManager->getSchemaPropertyValue($item);
      return $this->schemaJsonLdManager->getSchemaPropertyValueDefaultType($schema_type, $schema_property, $property_value);
    }
  }

  /**
   * Get Schema.org types from data.
   *
   * @param array $data
   *   An array of Schema.org data.
   *
   * @return array
   *   Schema.org types.
   */
  protected function getSchemaTypesFromData(array $data): array {
    $types = [];
    foreach ($data as $item) {
      if (is_array($item)) {
        if (isset($item['@type'])) {
          // Make sure all Schema.org types have @context.
          $types[] = ['@context' => 'https://schema.org'] + $item;
        }
        else {
          $types = array_merge($types, $this->getSchemaTypesFromData($item));
        }
      }
    }
    return $types;
  }

}
