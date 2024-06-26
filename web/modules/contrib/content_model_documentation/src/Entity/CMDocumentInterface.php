<?php

namespace Drupal\content_model_documentation\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Content Model Document entities.
 *
 * @ingroup cm_document
 */
interface CMDocumentInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Gets the CMDocument name.
   *
   * @return string
   *   Name of the CMDocument.
   */
  public function getName(): string;

  /**
   * Sets the CMDocument name.
   *
   * @param string $name
   *   The CMDocument name.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   The called CMDocument entity.
   */
  public function setName(string $name): CMDocumentInterface;

  /**
   * Gets the CMDocument creation timestamp.
   *
   * @return int
   *   Creation timestamp of the CMDocument.
   */
  public function getCreatedTime();

  /**
   * Sets the CMDocument creation timestamp.
   *
   * @param int $timestamp
   *   The CMDocument creation timestamp.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   The called CMDocument entity.
   */
  public function setCreatedTime(int $timestamp): CMDocumentInterface;

  /**
   * Reads the documented_entity field and returns the requested parameter.
   *
   * @param string $element
   *   The name of the element to get (type, bundle, or field).
   *
   * @return string
   *   The value of the type, bundle or field. Empty string otherwise.
   */
  public function getDocumentedEntityParameter($element): string;

  /**
   * Loads and returns the entity that is being documented.
   *
   * @return object|null
   *   The object being documented (node, vocabulary, field...) NULL if none.
   */
  public function getDocumentedEntity(): object|null;

  /**
   * Calculates the intended alias for the CM Document.
   *
   * @return string
   *   The alias that will be assigned to the CM Document.
   */
  public function getAliasPattern(): string;

  /**
   * Gets the drupal uri for the CM Document.
   *
   * @return string
   *   URI for the current CM Document.
   */
  public function getUri(): string;

  /**
   * Gets a list of documentable things that are not entities.
   *
   * @return array
   *   An array with elements in the form of 'value' => 'Name' pairs.
   */
  public static function getOtherDocumentableTypes(): array;

  /**
   * Gets an array of entity types mapped to storage types.
   *
   * @return array
   *   An array with elements in the form of 'entity type' => 'storage' pairs.
   */
  public function getStorageMap(): array;

}
