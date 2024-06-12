<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_content_model_documentation\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org content model documentation.
 *
 * @group schemadotorg
 */
class SchemaDotOrgContentModelDocumentationKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the schema has been fixed.
   *
   * @see https://www.drupal.org/project/epp/issues/3348759
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'content_model_documentation',
    'path_alias',
    'markup',
    'schemadotorg_content_model_documentation',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['content_model_documentation', 'schemadotorg_content_model_documentation']);
    $this->installConfig(['content_model_documentation', 'filter']);
    $this->installEntitySchema('path_alias');
    $this->installEntitySchema('cm_document');

    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Basic HTML',
    ])->save();
  }

  /**
   * Test Schema.org content model documentation.
   */
  public function testContentModelDocumentation(): void {
    /** @var \Drupal\content_model_documentation\CMDocumentStorageInterface $cm_document_storage */
    $cm_document_storage = $this->entityTypeManager->getStorage('cm_document');

    // Check that Schema.org mapping type target entity types are documentable.
    $this->assertEquals(['node' => 1], $this->config('content_model_documentation.settings')->getRawData());

    // Check that Schema.org Event content model documentation is created.
    $this->createSchemaEntity('node', 'Event');
    $cm_documents = $cm_document_storage->loadByProperties(['documented_entity' => 'node.event']);
    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document */
    $cm_document = reset($cm_documents);
    $this->assertTrue($cm_document->isPublished());
    $this->assertEquals(0, $cm_document->getOwnerId());
    $this->assertEquals('Event', $cm_document->label());
    $this->assertEquals('node.event', $cm_document->documented_entity->value);
    $this->assertStringContainsString('<p>An event happening at a certain time and location, such as a concert, lecture, or festival.</p>', $cm_document->notes->value);
    $this->assertStringContainsString('<h2>Introduction</h2>', $cm_document->notes->value);
    $this->assertEquals('full_html', $cm_document->notes->format);

    // Check that content model documentation field storage and instance is created.
    $field_storage_config = FieldStorageConfig::load('node.schema_cm_documentation');
    $this->assertNotNull($field_storage_config);
    $field_config = FieldConfig::load('node.event.schema_cm_documentation');
    $this->assertNotNull($field_config);

    // Check content model documentation notes value.
    $settings = $field_config->getSettings();
    $this->assertEquals('<p>An event happening at a certain time and location, such as a concert, lecture, or festival. <a href="' . $cm_document->toUrl()->toString() . '" target="_blank" hreflang="en">Read documentation</a></p>', $settings['markup']['value']);

    // Create https://schema.org/Place documentation before the mapping is created.
    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document */
    $cm_document = $cm_document_storage->create([
      'name' => 'https://schema.org/Place',
      'documented_entity' => "site.note",
      'notes' => [
        'value' => '<p>Custom documentation</p>',
        'format' => 'full_html',
      ],
    ]);
    $cm_document->save();

    // Check manually created Schema.org mapping documentation's name and entity.
    $this->assertEquals('https://schema.org/Place', $cm_document->getName());
    $this->assertEquals('site.note', $cm_document->documented_entity->value);
    $this->assertEquals('<p>Custom documentation</p>', $cm_document->notes->value);
    $this->assertEquals('full_html', $cm_document->notes->format);

    // Create the Place Schema.org mapping.
    $this->createSchemaEntity('node', 'Place');

    // Reload the Schema.org mapping documentation.
    $cm_document_storage->resetCache();
    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document */
    $cm_document = $cm_document_storage->load($cm_document->id());

    // Check that pre-existing Schema.org mapping documentation is updated as expected.
    $this->assertNotEquals('https://schema.org/Place', $cm_document->getName());
    $this->assertNotEquals('site.note', $cm_document->documented_entity->value);
    $this->assertEquals('Place', $cm_document->getName());
    $this->assertEquals('node.place', $cm_document->documented_entity->value);
    $this->assertEquals('<p>Custom documentation</p>', $cm_document->notes->value);
    $this->assertEquals('full_html', $cm_document->notes->format);
  }

}
