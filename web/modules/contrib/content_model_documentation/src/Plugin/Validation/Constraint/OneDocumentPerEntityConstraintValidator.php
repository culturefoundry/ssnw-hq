<?php

namespace Drupal\content_model_documentation\Plugin\Validation\Constraint;

use Drupal\content_model_documentation\Entity\CMDocument;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the OneDocumentPerEntityConstraint constraint.
 */
class OneDocumentPerEntityConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs an OneDocumentPerEntityConstraintValidator object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($cmDocument, Constraint $constraint) {
    $exempt_entities = array_keys(CMDocument::getOtherDocumentableTypes());
    if (!in_array($cmDocument->documented_entity->value, $exempt_entities)) {
      // This entity type is not exempt, check for a duplicate.
      // At a minimum, check for any existing documents of this type.
      $query = $this->database->select('cm_document', 'cm_document')
        ->fields('cm_document', ['documented_entity'])
        ->condition('documented_entity', $cmDocument->documented_entity->value, '=');
      if (!$cmDocument->isNew()) {
        // Saving existing document, the documented entity field may change.
        // Restrict results to entities other than this one.
        $query = $query->condition('id', $cmDocument->id(), '!=');
      }
      $count = $query->countQuery()->execute()->fetchField();
      if ($count) {
        $this->context->addViolation($constraint->entityDocumentExists, []);
      }
    }
  }

}
