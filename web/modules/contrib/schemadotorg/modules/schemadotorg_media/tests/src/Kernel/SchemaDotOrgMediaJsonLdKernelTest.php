<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_media\Kernel;

use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org media module JSON-LD integration.
 *
 * @covers schemadotorg_media_schemadotorg_jsonld_schema_type_entity_alter()
 * @group schemadotorg
 */
class SchemaDotOrgMediaJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
  }

  /**
   * Test Schema.org media JSON-LD.
   */
  public function testJsonLdMedia(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['administer media']));

    $this->createSchemaEntity('media', 'ImageObject');

    $media = Media::create([
      'bundle' => 'image',
      'name' => 'Some image',
    ]);
    $media->save();

    /** @var \Drupal\file\FileInterface $thumbnail */
    $thumbnail = $media->thumbnail->entity;
    /** @var \Drupal\image\ImageStyleInterface $image_style */
    $image_style = ImageStyle::load('thumbnail');

    $expected_value = [
      '@type' => 'ImageObject',
      'name' => 'Some image',
      'thumbnail' => $image_style->buildUrl($thumbnail->getFileUri()),
      'inLanguage' => 'en',
      'dateCreated' => $this->formatDateTime($media->getCreatedTime()),
      'dateModified' => $this->formatDateTime($media->getChangedTime()),
    ];
    $actual_value = $this->builder->buildEntity($media);
    $this->assertEquals($expected_value, $actual_value);
  }

}
