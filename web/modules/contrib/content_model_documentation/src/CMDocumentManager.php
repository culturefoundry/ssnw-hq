<?php

namespace Drupal\content_model_documentation;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Manager for working with content model document entities.
 */
class CMDocumentManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Time of current request.
   *
   * @var \DateTimeInterface
   */
  private $requestDateTime;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, TimeInterface $time) {
    $this->entityTypeManager = $entityTypeManager;
    $this->time = $time;
  }

  /**
   * Returns all active Content Model Documents.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface[]
   *   Array of active Content Model Documents indexed by their ids.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getCmDocuments(): array {
    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface[] $cmDocuments */
    // @todo This probably is not needed and should be handled with a View.
    $cmDocuments = $this->entityTypeManager
      ->getStorage('cm_document')
      ->loadByProperties(['status' => 1]);
    return $cmDocuments;
  }

}
