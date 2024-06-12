<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi_preview;

use Drupal\Core\Entity\EntityInterface;

/**
 * Schema.org JSON:API preview builder interface.
 */
interface SchemaDotOrgJsonApiPreviewBuilderInterface {

  /**
   * Build JSON:API preview for an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   *
   * @return array[]|null
   *   A renderable array containing a JSON:API preview
   *   for an entity.
   */
  public function build(EntityInterface $entity): ?array;

}
