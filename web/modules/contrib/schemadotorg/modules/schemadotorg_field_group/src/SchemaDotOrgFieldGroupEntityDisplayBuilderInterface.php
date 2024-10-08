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
