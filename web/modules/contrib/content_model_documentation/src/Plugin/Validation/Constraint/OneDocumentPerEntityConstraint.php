<?php

namespace Drupal\content_model_documentation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique integer.
 *
 * @Constraint(
 *   id = "OneDocumentPerEntityConstraint",
 *   label = @Translation("One Document Per Entity", context = "Validation"),
 *   type = "string"
 * )
 */
class OneDocumentPerEntityConstraint extends Constraint {

  /**
   * The message to show when a duplicate entity document is saved.
   *
   * @var string
   */
  public $entityDocumentExists = 'A content model document for this entity type already exists';

}
