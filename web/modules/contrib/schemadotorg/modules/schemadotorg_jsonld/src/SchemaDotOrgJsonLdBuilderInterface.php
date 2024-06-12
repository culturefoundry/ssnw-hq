<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org JSON-LD builder interface.
 */
interface SchemaDotOrgJsonLdBuilderInterface {

  /**
   * Cache contexts when building JSON-LD from a route match.
   */
  public const ROUTE_MATCH_CACHE_CONTEXTS = ['user.permissions', 'route'];

  /**
   * Cache tags when building JSON-LD from a route match.
   */
  public const ROUTE_MATCH_CACHE_TAGS = ['config:schemadotorg_jsonld.settings'];

  /**
   * Cache contexts when building JSON-LD for an entity.
   */
  public const ENTITY_CACHE_CONTEXTS = ['user.permissions'];

  /**
   * Cache contexts when building JSON-LD for an entity.
   */
  public const ENTITY_CACHE_TAGS = ['config:schemadotorg_jsonld.settings', 'config:schemadotorg_mapping_list'];

  /**
   * Build JSON-LD for a route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface|null $route_match
   *   A route match.
   * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
   *   (optional) Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array|null
   *   The JSON-LD for a route or NULL if the route does not return JSON-LD.
   */
  public function build(?RouteMatchInterface $route_match = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): ?array;

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
   *   (optional) Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array|null
   *   The JSON-LD for an entity that is mapped to a Schema.org type
   *   or NULL if the entity is not mapped to a Schema.org type.
   */
  public function buildEntity(?EntityInterface $entity = NULL, ?SchemaDotOrgMappingInterface $mapping = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): ?array;

  /**
   * Get Schema.org property values from field items.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field items.
   * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
   *   (optional) Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array
   *   An array of Schema.org property values.
   */
  public function getSchemaPropertyFieldItems(string $schema_type, string $schema_property, FieldItemListInterface $items, ?BubbleableMetadata $bubbleable_metadata = NULL): array;

}
