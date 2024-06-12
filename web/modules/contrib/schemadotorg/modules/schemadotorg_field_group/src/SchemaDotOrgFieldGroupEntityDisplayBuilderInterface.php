<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_field_group;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org field group entity display builder interface.
 */
interface SchemaDotOrgFieldGroupEntityDisplayBuilderInterface {

  /**
   * Set entity display field groups for Schema.org mapping's properties.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   * @param array $properties
   *   Customize mapping properties.
   */
  public function setFieldGroups(SchemaDotOrgMappingInterface $mapping, array $properties = []): void;

}
