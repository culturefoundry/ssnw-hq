<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi;

use Drupal\field\FieldConfigInterface;
use Drupal\jsonapi\ResourceType\ResourceType;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org JSON:API manager interface.
 */
interface SchemaDotOrgJsonApiManagerInterface {

  /**
   * Get resource type's entity reference fields as an array of includes.
   *
   * @param \Drupal\jsonapi\ResourceType\ResourceType $resource_type
   *   The resource type.
   *
   * @return array
   *   An array of entity reference field public names to be used as includes.
   */
  public function getResourceIncludes(ResourceType $resource_type): array;

  /**
   * Insert Schema.org mapping JSON:API resource config.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function insertMappingResourceConfig(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Update Schema.org mapping JSON:API resource config.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function updateMappingResourceConfig(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Insert field into JSON:API resource config.
   *
   * @param \Drupal\field\FieldConfigInterface $field_config
   *   The field config.
   */
  public function insertFieldConfigResource(FieldConfigInterface $field_config): void;

}
