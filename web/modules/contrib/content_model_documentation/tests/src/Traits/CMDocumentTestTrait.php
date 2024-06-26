<?php

declare(strict_types=1);

namespace Drupal\Tests\content_model_documentation\Traits;

use Drupal\Component\Utility\Random;
use Drupal\content_model_documentation\Entity\CMDocument;
use Drupal\content_model_documentation\Entity\CMDocumentInterface;

/**
 * Defines a trait for content model document tests.
 */
trait CMDocumentTestTrait {

  /**
   * Creates a CMDocument.
   *
   * @param array $values
   *   Field values.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   Created CMDocument.
   */
  protected function createCmDocument(array $values = []): CMDocumentInterface {
    $random = new Random();
    $document = CMDocument::create($values + [
      'status' => 1,
      'user_id' => 1,
      'name' => $random->name(),
      'documented_entity' => 'node.page',
      'message' => [
        'value' => $random->sentences(10),
        'format' => 'plain_text',
      ],
    ]);
    // Support Drupal Testing traits users.
    if (method_exists($this, 'markEntityForCleanup')) {
      $this->markEntityForCleanup($document);
    }
    $document->save();
    assert($document instanceof CMDocumentInterface);
    return $document;
  }

}
