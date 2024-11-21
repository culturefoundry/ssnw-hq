<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org JSON-LD manager interface.
 */
interface SchemaDotOrgJsonLdManagerInterface {

  /**
   * Entity reference display url.
   */
  const ENTITY_REFERENCE_DISPLAY_URL = 'url';

  /**
   * Entity reference display label.
   */
  const ENTITY_REFERENCE_DISPLAY_LABEL = 'label';

  /**
   * Entity reference display entity.
   */
  const ENTITY_REFERENCE_DISPLAY_ENTITY = 'entity';

  /**
   * Entity reference display none.
   */
  const ENTITY_REFERENCE_DISPLAY_NONE = 'none';

  /**
   * Get an entity's canonical route match.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $rel
   *   The link relationship type, for example: canonical or edit-form.
   *
   * @return \Drupal\Core\Routing\RouteMatchInterface|null
   *   An entity's canonical route match.
   */
  public function getEntityRouteMatch(EntityInterface $entity, string $rel = 'canonical'): RouteMatchInterface|NULL;

  /**
   * Returns the entity of the current route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface|null $route_match
   *   A route match.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity or NULL if this is not an entity route.
   *
   * @see metatag_get_route_entity()
   */
  public function getRouteMatchEntity(?RouteMatchInterface $route_match = NULL): EntityInterface|NULL;

  /**
   * Sort Schema.org properties in specified order and then alphabetically.
   *
   * @param array $properties
   *   An associative array of Schema.org properties.
   *
   * @return array
   *   The Schema.org properties in specified order and then alphabetically.
   */
  public function sortProperties(array $properties): array;

  /**
   * Get Schema.org type properties from field items.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $item
   *   THe field items.
   *
   * @return array
   *   An array of Schema.org type properties.
   */
  public function getSchemaTypeProperties(FieldItemListInterface $item): array;

  /**
   * Get a Schema.org property's value for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return mixed
   *   A Schema.org property's value for a field item.
   */
  public function getSchemaPropertyValue(FieldItemInterface $item): mixed;

  /**
   * Get a Schema.org type property's value converted to the default Schema.org type.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param string|mixed $value
   *   The Schema.org property's value.
   *
   * @return array|string|int|bool|null
   *   The Schema.org property's value converted to the default Schema.org type.
   */
  public function getSchemaPropertyValueDefaultSchemaType(string $schema_type, string $schema_property, mixed $value): array|string|int|bool|NULL;

  /**
   * Get how an entity reference should be included in JSON-LD.
   *
   * @param \Drupal\Core\Entity\EntityInterface $source_entity
   *   The source entity.
   * @param string $schema_property
   *   The source entity's Schema.org property.
   * @param \Drupal\Core\Entity\EntityInterface $target_entity
   *   The target entity.
   *
   * @return string
   *   How an entity reference should be included in JSON-LD, which can be
   *   by 'label', 'url', or 'data'.
   */
  public function getSchemaTypeEntityReferenceDisplay(EntityInterface $source_entity, string $schema_property, EntityInterface $target_entity): string;

  /**
   * Determine if the Schema.org mapping has a URL.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return bool
   *   TRUE if the mapping has a URL, FALSE otherwise.
   */
  public function hasSchemaUrl(SchemaDotOrgMappingInterface $mapping): bool;

}
