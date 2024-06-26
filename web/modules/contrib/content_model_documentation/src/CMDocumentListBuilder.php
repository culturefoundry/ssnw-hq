<?php

namespace Drupal\content_model_documentation;

use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of CMDocument entities.
 *
 * @ingroup cm_document
 */
class CMDocumentListBuilder extends EntityListBuilder {
  // The list for CMDocuments is powered by a View.
  // Removing this class from "list_builder in CMDocument.php
  // breaks the operations field in the CMDocument View.
  // Therefore, this class must exist even though it does nothing.
}
