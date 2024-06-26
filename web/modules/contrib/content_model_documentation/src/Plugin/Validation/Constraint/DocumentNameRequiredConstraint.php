<?php

namespace Drupal\content_model_documentation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted CM Document name property is not empty.
 *
 * @Constraint(
 *   id = "DocumentNameRequiredConstraint",
 *   label = @Translation("Document Name Required", context = "Validation"),
 *   type = "string"
 * )
 */
class DocumentNameRequiredConstraint extends Constraint {

  /**
   * The message to show when name is empty.
   *
   * @var string
   */
  public $cantBeEmpty = 'The Name of a Content Model Document can not be empty';

}
