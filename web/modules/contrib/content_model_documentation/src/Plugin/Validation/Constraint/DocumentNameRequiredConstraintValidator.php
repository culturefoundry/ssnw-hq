<?php

namespace Drupal\content_model_documentation\Plugin\Validation\Constraint;

use Drupal\content_model_documentation\Entity\CMDocument;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the DocumentNameRequiredConstraint constraint.
 */
class DocumentNameRequiredConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($cmDocument, Constraint $constraint) {
    $documentable_entites_without_titles = array_keys(CMDocument::getOtherDocumentableTypes());
    $title_required = in_array($cmDocument->documented_entity->value, $documentable_entites_without_titles);
    if ($title_required && empty(trim($cmDocument->name->value))) {
      // No name has been provided and must be.
      $this->context->addViolation($constraint->cantBeEmpty, []);
    }
  }

}
