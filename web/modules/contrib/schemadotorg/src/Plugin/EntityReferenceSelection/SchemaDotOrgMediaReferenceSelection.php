<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Query\QueryInterface;

/**
 * Media plugin implementation of the Schema.org Entity Selection plugin.
 *
 * @see \Drupal\media\Plugin\EntityReferenceSelection\MediaSelection
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg:media",
 *   label = @Translation("Schema.org: Filter by Schema.org types"),
 *   entity_types = {"media"},
 *   group = "schemadotorg",
 *   weight = 1,
 * )
 */
class SchemaDotOrgMediaReferenceSelection extends SchemaDotOrgEntityReferenceSelection {

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\media\Plugin\EntityReferenceSelection\MediaSelection::buildEntityQuery
   */
  protected function buildEntityQuery(?string $match = NULL, string $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);

    // Ensure that users with insufficient permission cannot see unpublished
    // entities.
    if (!$this->currentUser->hasPermission('administer media')) {
      $query->condition('status', 1);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\media\Plugin\EntityReferenceSelection\MediaSelection::createNewEntity
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $media = parent::createNewEntity($entity_type_id, $bundle, $label, $uid);

    // In order to create a referenceable media, it needs to published.
    /** @var \Drupal\media\MediaInterface $media */
    $media->setPublished();

    return $media;
  }

}
