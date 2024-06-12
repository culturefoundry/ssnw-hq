<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

/**
 * Tests the Schema.org mapping entity.
 *
 * @coversClass \Drupal\schemadotorg\Entity\SchemaDotOrgMappingType
 * @group schemadotorg
 */
class SchemaDotOrgMappingTypeEntityKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('media');
    $this->installEntitySchema('paragraph');

    $this->installConfig(['schemadotorg_media', 'schemadotorg_paragraphs']);
  }

  /**
   * Test Schema.org mapping entity.
   */
  public function testSchemaDotOrgMappingTypeEntity(): void {
    $user_mapping_type = $this->loadMappingType('user');
    $node_mapping_type = $this->loadMappingType('node');
    $media_mapping_type = $this->loadMappingType('media');
    $paragraph_mapping_type = $this->loadMappingType('paragraph');

    // Check getting the mapping type label.
    $this->assertEquals('User', $user_mapping_type->label());
    $this->assertEquals('Content', $node_mapping_type->label());
    $this->assertEquals('Paragraph', $paragraph_mapping_type->label());
    $this->assertEquals('Media', $media_mapping_type->label());

    // Check getting default bundle for a Schema.org type.
    $tests = [
      ['user', 'Person', ['user' => 'user']],
      ['media', 'AudioObject', ['audio' => 'audio']],
    ];
    foreach ($tests as $test) {
      $mapping_type = $this->loadMappingType($test[0]);
      $this->assertEquals($test[2], $mapping_type->getDefaultSchemaTypeBundles($test[1]));
    }

    // Check getting default Schema.org type for a bundle.
    $tests = [
      ['user', 'user', 'Person'],
      ['media', 'audio', 'AudioObject'],
    ];
    foreach ($tests as $test) {
      $mapping_type = $this->loadMappingType($test[0]);
      $this->assertEquals($test[2], $mapping_type->getDefaultSchemaType($test[1]));
    }

    // Check getting default Schema.org type's default properties.
    $expected_default_type_properties = [
      'dateCreated' => 'dateCreated',
      'dateModified' => 'dateModified',
      'image' => 'image',
      'name' => 'name',
      'thumbnail' => 'thumbnail',
      'inLanguage' => 'inLanguage',
    ];
    $actual_default_type_properties = $media_mapping_type->getDefaultSchemaTypeProperties('ImageObject');
    $this->assertEquals($expected_default_type_properties, $actual_default_type_properties);
    $this->assertArrayHasKey('step', $media_mapping_type->getDefaultSchemaTypeProperties('HowTo'));
    $this->assertArrayNotHasKey('step', $media_mapping_type->getDefaultSchemaTypeProperties('Recipe'));

    // Check getting common Schema.org types.
    $recommended_schema_types = $node_mapping_type->getRecommendedSchemaTypes();
    $this->assertEquals('Common', $recommended_schema_types['common']['label']);
    $this->assertEquals('Place', $recommended_schema_types['common']['types'][0]);

    // Check getting an entity type's base field mappings.
    $expected_base_field_mappings = [
      'email' => ['mail' => 'mail'],
      'name' => ['name' => 'name'],
      'image' => ['user_picture' => 'user_picture'],
      'inLanguage' => ['langcode' => 'langcode'],
    ];
    $actual_base_field_mappings = $user_mapping_type->getBaseFieldMappings();
    $this->assertEquals($expected_base_field_mappings, $actual_base_field_mappings);

    // Check getting an entity type's base fields names.
    $expected_base_field_names = [
      'uuid' => 'uuid',
      'revision_created' => 'revision_created',
      'revision_user' => 'revision_user',
      'uid' => 'uid',
      'name' => 'name',
      'thumbnail' => 'thumbnail',
      'created' => 'created',
      'changed' => 'changed',
      'path' => 'path',
      'langcode' => 'langcode',
      'field_media_audio_file' => 'field_media_audio_file',
      'field_media_document' => 'field_media_document',
      'field_media_image' => 'field_media_image',
      'field_media_oembed_video' => 'field_media_oembed_video',
      'field_media_video_file' => 'field_media_video_file',
    ];
    $actual_base_field_names = $media_mapping_type->getBaseFieldNames();
    $this->assertEquals($expected_base_field_names, $actual_base_field_names);
  }

}
