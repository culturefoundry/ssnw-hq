<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_paragraphs\Kernel;

use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org paragraphs type icon file.
 *
 * @covers schemadotorg_paragraphs_paragraphs_type_presave()
 * @group schemadotorg
 */
class SchemaDotOrgParagraphsTypeIconFileKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * Schema.org JSON-LD builder.
   */
  protected SchemaDotOrgJsonLdBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'file',
    'schemadotorg_paragraphs',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('file', ['file_usage']);

    $this->installEntitySchema('file');
    $this->installConfig(['schemadotorg_paragraphs']);
  }

  /**
   * Test Schema.org paragraphs type icon file.
   */
  public function testParagraphsTypeIconFile(): void {
    // Check that icon file is assigned to questions paragraph type.
    $this->createSchemaEntity('paragraph', 'Question');
    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraphs_type */
    $paragraphs_type = ParagraphsType::load('question');
    $this->assertNotNull($paragraphs_type->getIconFile());
    $this->assertEquals(
      'public://paragraphs_type_icon/question.svg',
      $paragraphs_type->getIconFile()->getFileUri()
    );
    $this->assertEquals(
      'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tISBGb250IEF3ZXNvbWUgUHJvIDYuMi4wIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlIChDb21tZXJjaWFsIExpY2Vuc2UpIENvcHlyaWdodCAyMDIyIEZvbnRpY29ucywgSW5jLiAtLT48cGF0aCBkPSJNMjU2IDBDMTE0LjYgMCAwIDExNC42IDAgMjU2czExNC42IDI1NiAyNTYgMjU2czI1Ni0xMTQuNiAyNTYtMjU2UzM5Ny40IDAgMjU2IDB6TTI1NiA0NjRjLTExNC43IDAtMjA4LTkzLjMxLTIwOC0yMDhTMTQxLjMgNDggMjU2IDQ4czIwOCA5My4zMSAyMDggMjA4UzM3MC43IDQ2NCAyNTYgNDY0ek0yNTYgMzM2Yy0xOCAwLTMyIDE0LTMyIDMyczEzLjEgMzIgMzIgMzJjMTcuMSAwIDMyLTE0IDMyLTMyUzI3My4xIDMzNiAyNTYgMzM2ek0yODkuMSAxMjhoLTUxLjFDMTk5IDEyOCAxNjggMTU5IDE2OCAxOThjMCAxMyAxMSAyNCAyNCAyNHMyNC0xMSAyNC0yNEMyMTYgMTg2IDIyNS4xIDE3NiAyMzcuMSAxNzZoNTEuMUMzMDEuMSAxNzYgMzEyIDE4NiAzMTIgMTk4YzAgOC00IDE0LjEtMTEgMTguMUwyNDQgMjUxQzIzNiAyNTYgMjMyIDI2NCAyMzIgMjcyVjI4OGMwIDEzIDExIDI0IDI0IDI0UzI4MCAzMDEgMjgwIDI4OFYyODZsNDUuMS0yOGMyMS0xMyAzNC0zNiAzNC02MEMzNjAgMTU5IDMyOSAxMjggMjg5LjEgMTI4eiIvPjwvc3ZnPg==',
      $paragraphs_type->get('icon_default')
    );

    // Check that icon file is assigned to med_* paragraph type.
    $this->createSchemaEntity('paragraph', 'MedicalAudience');
    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraphs_type */
    $paragraphs_type = ParagraphsType::load('medical_audience');
    $this->assertNotNull($paragraphs_type->getIconFile());
    $this->assertEquals(
      'public://paragraphs_type_icon/medical.svg',
      $paragraphs_type->getIconFile()->getFileUri()
    );
  }

}
