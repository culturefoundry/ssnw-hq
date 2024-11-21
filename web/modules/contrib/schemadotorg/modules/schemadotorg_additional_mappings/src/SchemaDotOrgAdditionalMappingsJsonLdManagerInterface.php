<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_mappings;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org additional mappings JSON-LD manager interface.
 */
interface SchemaDotOrgAdditionalMappingsJsonLdManagerInterface {

  /**
   * Alter the Schema.org JSON-LD data for an entity.
   *
   * @param array $data
   *   The Schema.org JSON-LD data for an entity.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping
   *   The entity's Schema.org mapping.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @see hook_schemadotorg_jsonld_schema_type_entity_alter()
   */
  public function entityAlter(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping, BubbleableMetadata $bubbleable_metadata): void;

  /**
   * Alter the Schema.org JSON-LD data for the current route.
   *
   * @param array $data
   *   The Schema.org JSON-LD data for the current route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @see hook_schemadotorg_jsonld_alter()
   */
  public function alter(array &$data, RouteMatchInterface $route_match, BubbleableMetadata $bubbleable_metadata): void;

  /**
   * Alter the Schema.org property JSON-LD values for an entity's field items.
   *
   * @param mixed $value
   *   Alter the Schema.org property JSON-LD value.
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The entity's field item.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   */
  public function schemaPropertyAlter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void;

}
