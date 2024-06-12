<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_cer;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org Corresponding Entity Reference manager interface.
 */
interface SchemaDotOrgCorrespondingReferenceManagerInterface {

  /**
   * Alters Schema.org mapping entity defaults value to always enable correspond node references.
   *
   * @param array $defaults
   *   The Schema.org mapping entity default values.
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void;

  /**
   * Add corresponding entity references when a mapping is inserted or updated.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

}
