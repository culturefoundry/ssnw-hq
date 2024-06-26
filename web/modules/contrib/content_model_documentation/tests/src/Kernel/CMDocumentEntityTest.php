<?php

declare(strict_types=1);

namespace Drupal\Tests\content_model_documentation\Kernel;

/**
 * Defines a class for testing the CMDocument entity.
 *
 * @group cm_document
 * @coversDefaultClass \Drupal\content_model_documentation\Entity\CMDocument
 */
final class CMDocumentEntityTest extends CMDocumentKernelTestBase {

  /**
   * Covers ::isPublished.
   *
   * @covers ::isPublished
   */
  public function testIsPublished(): void {
    $document = $this->createCMDocument();
    $this->assertTrue($document->isPublished());

    $document = $this->createCMDocument([
      'status' => FALSE,
    ]);
    $this->assertFalse($document->isPublished());
  }

  /**
   * Test basic crud.
   *
   * Tests basic entity crud.
   */
  public function testEntityCrud(): void {
    $name = $this->randomMachineName();
    $document = $this->createCMDocument([
      'name' => $name,
    ]);
    \Drupal::entityTypeManager()->getStorage('cm_document')->loadUnchanged($document->id());
    $this->assertEquals($name, $document->label());
  }

}
