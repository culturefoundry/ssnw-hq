<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_custom;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org JSON-LD custom manager interface.
 */
interface SchemaDotOrgJsonLdCustomInterface {

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
   *
   * @see hook_schemadotorg_mapping_defaults_alter()
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void;

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
   *
   * @see hook_schemadotorg_jsonld_schema_type_entity_load()
   */
  public function jsonLdSchemaTypeEntityLoad(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping, BubbleableMetadata $bubbleable_metadata): void;

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
   *
   * @see hook_schemadotorg_jsonld_custom_schemadotorg_jsonld()
   */
  public function buildRouteMatchJsonLd(RouteMatchInterface $route_match, BubbleableMetadata $bubbleable_metadata): ?array;

}
