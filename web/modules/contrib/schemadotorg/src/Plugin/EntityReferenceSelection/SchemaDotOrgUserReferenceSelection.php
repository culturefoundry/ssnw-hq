<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Query\QueryInterface;

/**
 * User plugin implementation of the Schema.org Entity Selection plugin.
 *
 * @see \Drupal\user\Plugin\EntityReferenceSelection\UserSelection
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg:user",
 *   label = @Translation("Schema.org: Filter by Schema.org types"),
 *   entity_types = {"user"},
 *   group = "schemadotorg",
 *   weight = 1,
 * )
 */
class SchemaDotOrgUserReferenceSelection extends SchemaDotOrgEntityReferenceSelection {

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\user\Plugin\EntityReferenceSelection\UserSelection::buildEntityQuery
   */
  protected function buildEntityQuery(?string $match = NULL, string $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);

    // Filter out the Anonymous user.
    $query->condition('uid', 0, '<>');

    // The user entity doesn't have a label column.
    if (isset($match)) {
      $query->condition('name', $match, $match_operator);
    }

    // Adding the permission check is sadly insufficient for users: core
    // requires us to also know about the concept of 'blocked' and 'active'.
    if (!$this->currentUser->hasPermission('administer users')) {
      $query->condition('status', 1);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\user\Plugin\EntityReferenceSelection\UserSelection::createNewEntity
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $user = parent::createNewEntity($entity_type_id, $bundle, $label, $uid);

    // In order to create a referenceable user, it needs to be active.
    if (!$this->currentUser->hasPermission('administer users')) {
      /** @var \Drupal\user\UserInterface $user */
      $user->activate();
    }

    return $user;
  }

}
