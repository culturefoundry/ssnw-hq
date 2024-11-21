<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_field_group;

use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org field group entity display builder interface.
 */
interface SchemaDotOrgFieldGroupEntityDisplayBuilderInterface {

  /**
   * Disabled field group patterns.
   */
  const PATTERNS = [
    ['entity_type_id'],
    ['entity_type_id', 'display_type'],
    ['entity_type_id', 'display_type', 'bundle'],
    ['entity_type_id', 'display_type', 'bundle', 'field_name'],
    ['entity_type_id', 'display_type', 'schema_type'],
    ['entity_type_id', 'display_type', 'schema_type', 'schema_property'],
    ['entity_type_id', 'display_type', 'schema_property'],
    ['entity_type_id', 'display_type', 'field_name'],
    ['entity_type_id', 'display_type', 'display_mode'],
    ['entity_type_id', 'display_type', 'display_mode', 'bundle'],
    ['entity_type_id', 'display_type', 'display_mode', 'bundle', 'field_name'],
    ['entity_type_id', 'display_type', 'display_mode', 'schema_type'],
    ['entity_type_id', 'display_type', 'display_mode', 'schema_type', 'schema_property'],
    ['entity_type_id', 'display_type', 'display_mode', 'schema_property'],
    ['entity_type_id', 'bundle'],
    ['entity_type_id', 'bundle', 'field_name'],
    ['entity_type_id', 'schema_type'],
    ['entity_type_id', 'schema_type', 'schema_property'],
    ['entity_type_id', 'schema_property'],
    ['entity_type_id', 'field_name'],
  ];

  /**
   * Pre-save function to process and save a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingPreSave(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Pre-save function to set field group on the entity display.
   *
   * @param \Drupal\Core\Entity\Display\EntityDisplayInterface $display
   *   The entity form or view display.
   */
  public function entityDisplayPreSave(EntityDisplayInterface $display): void;

}
