<?php

namespace Drupal\content_model_documentation;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the storage handler class for CMDocument entities.
 *
 * This extends the base storage class, adding required special handling for
 * CMDocument entities.
 *
 * @ingroup cm_document
 */
interface CMDocumentStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of CMDocument revision IDs for a specific CMDocument.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $entity
   *   The Content Model Document entity.
   *
   * @return int[]
   *   Content Model Document revision IDs (in ascending order).
   */
  public function revisionIds(CMDocumentInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as CMDocument author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   CMDocument revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $entity
   *   The CMDocument entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CMDocumentInterface $entity);

  /**
   * Unsets the language for all CMDocuments with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
