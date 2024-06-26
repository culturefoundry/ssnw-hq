<?php

namespace Drupal\content_model_documentation;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the CMDocument entity.
 *
 * @see \Drupal\content_model_documentation\Entity\CMDocument.
 */
class CMDocumentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished content model document entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view content model documentation');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit content model document entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete content model document entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add content model document entities');
  }

}
