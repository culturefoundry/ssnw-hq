<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\file\FileInterface;

/**
 * File plugin implementation of the Schema.org Entity Selection plugin.
 *
 * @see \Drupal\file\Plugin\EntityReferenceSelection\FileSelection
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg:file",
 *   label = @Translation("Schema.org: Filter by Schema.org types"),
 *   entity_types = {"file"},
 *   group = "schemadotorg",
 *   weight = 1,
 * )
 */
class SchemaDotOrgFileReferenceSelection extends SchemaDotOrgEntityReferenceSelection {

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\file\Plugin\EntityReferenceSelection\FileSelection::buildEntityQuery
   */
  protected function buildEntityQuery(?string $match = NULL, string $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);
    // Allow referencing :
    // - files with status "permanent"
    // - or files uploaded by the current user (since newly uploaded files only
    //   become "permanent" after the containing entity gets validated and
    //   saved.)
    $query->condition($query->orConditionGroup()
      ->condition('status', FileInterface::STATUS_PERMANENT)
      ->condition('uid', $this->currentUser->id()));
    return $query;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\comment\Plugin\EntityReferenceSelection\FileSelection::createNewEntity
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $file = parent::createNewEntity($entity_type_id, $bundle, $label, $uid);

    // In order to create a referenceable file, it needs to have a "permanent"
    // status.
    /** @var \Drupal\file\FileInterface $file */
    $file->setPermanent();

    return $file;
  }

}
