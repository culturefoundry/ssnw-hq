<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org taxonomy JSON-LD manager interface.
 */
interface SchemaDotOrgTaxonomyJsonLdManagerInterface {

  /**
   * Load Schema.org JSON-LD for an entity.
   *
   * @param array $data
   *   Schema.org type data.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The entity's Schema.org mapping.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @see hook_schemadotorg_jsonld_schema_type_entity_load()
   */
  public function load(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping, BubbleableMetadata $bubbleable_metadata): void;

  /**
   * Alter Schema.org JSON-LD for an entity.
   *
   * @param array $data
   *   Schema.org type data.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The entity's Schema.org mapping.
   *
   * @see hook_schemadotorg_jsonld_schema_type_entity_alter()
   */
  public function alter(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Preprocess block alter JSON-LD preview Term endpoint.
   *
   * @param array $variables
   *   An array of variables.
   */
  public function preprocessBlock(array &$variables): void;

}
