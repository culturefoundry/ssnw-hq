<?php

declare(strict_types=1);

namespace Drupal\Tests\content_model_documentation\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\content_model_documentation\Traits\CMDocumentTestTrait;

/**
 * Defines a class for testing the revision controller.
 *
 * @group content_model_documentation
 */
final class CMDocumentControllerTest extends BrowserTestBase {

  use CMDocumentTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'content_model_documentation',
  ];

  /**
   * Tests revision overview.
   */
  public function testCmDocumentRevisions(): void {
    $random = $this->getRandomGenerator();
    $message1 = $random->sentences(10);
    $message2 = $random->sentences(10);
    $document = $this->createCMDocument();
    $document->setRevisionLogMessage($message1);
    $document->message->value = $random->sentences(10);
    $document->setNewRevision(TRUE);
    $document->save();

    $document->setRevisionLogMessage($message2);
    $document->message->value = $random->sentences(10);
    $document->setNewRevision(TRUE);
    $document->save();

    $this->drupalLogin($this->createUser([
      'administer content model document entities',
      'access content',
      'view all content model document revisions',
    ]));
    $this->drupalGet(Url::fromRoute('entity.cm_document.version_history', [
      'cm_document' => $document->id(),
    ]));
    $assert = $this->assertSession();
    $assert->pageTextContains($message1);
    $assert->pageTextContains($message2);
  }

}
