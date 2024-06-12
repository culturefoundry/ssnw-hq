<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Entity\EntityInterface;

/**
 * Schema.org entity type builder interface.
 */
interface SchemaDotOrgEntityTypeBuilderInterface {

  /**
   * Add entity bundle.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $values
   *   The mapping values.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The bundle entity type.
   */
  public function addEntityBundle(string $entity_type_id, string $schema_type, array &$values): EntityInterface;

  /**
   * Add a field to an entity.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The name of the bundle.
   * @param array $field
   *   The field to be added to the entity.
   */
  public function addFieldToEntity(string $entity_type_id, string $bundle, array $field): void;

}
