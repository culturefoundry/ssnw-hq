<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_media\Functional;

use Drupal\media\Entity\MediaType;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org media module.
 *
 * @group schemadotorg
 */
class SchemaDotOrgMediaTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_ui',
    'schemadotorg_media',
  ];

  /**
   * Test Schema.org media UI.
   */
  public function testMedia(): void {
    $assert = $this->assertSession();

    /* ********************************************************************** */
    // Mapping defaults.
    // @see schemadotorg_media_schemadotorg_mapping_defaults_alter()
    /* ********************************************************************** */

    // Check mapping defaults for Schema media type that include source.
    $defaults = $this->getMappingDefaults(
      entity_type_id: 'media',
      schema_type: 'ImageObject',
    );
    $this->assertEquals('image', $defaults['entity']['source']);
    $defaults = $this->getMappingDefaults(
      entity_type_id: 'media',
      schema_type: 'VideoObject',
    );
    $this->assertEquals('oembed:video', $defaults['entity']['source']);

    /* ********************************************************************** */
    // Schema.org mapping UI form alter.
    // @see schemadotorg_media_form_schemadotorg_mapping_form_alter()
    /* ********************************************************************** */

    $this->drupalLogin($this->rootUser);

    // Check that media source field is added to the add media form.
    $this->drupalGet('admin/structure/media/schemadotorg', ['query' => ['type' => 'ImageObject']]);
    $assert->responseContains('Media source');
    $assert->elementExists('css', 'select[name="mapping[entity][source]"]');

    /* ********************************************************************** */
    // Schema.org mapping media type create.
    // @see schemadotorg_media_schemadotorg_bundle_entity_alter()
    // @see schemadotorg_media_media_type_insert()
    // @see schemadotorg_media_schemadotorg_property_field_alter(()
    /* ********************************************************************** */

    $this->drupalGet('admin/structure/media/schemadotorg', ['query' => ['type' => 'ImageObject']]);
    $this->submitForm([], 'Save');

    // Check that media is created with default settings as expected.
    $media_type = MediaType::load('image_object');
    $this->assertTrue($media_type->get('status'));
    $this->assertFalse($media_type->get('new_revision'));
    $this->assertFalse($media_type->get('queue_thumbnail_downloads'));
    $this->assertEquals('image', $media_type->get('source'));
    $this->assertEquals('image', $media_type->get('source'));
    $this->assertEquals(['name' => 'name'], $media_type->get('field_map'));
    $source = $media_type->getSource();
    $this->assertEquals(['name' => 'name'], $media_type->get('field_map'));
    $source_field_definition = $source->getSourceFieldDefinition($media_type);
    $this->assertEquals('field_media_image', $source_field_definition->getName());

    // Check that the default form display is set for media entity reference fields.
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository */
    $entity_display_repository = \Drupal::service('entity_display.repository');

    $form_display = $entity_display_repository->getFormDisplay('media', 'image_object');
    $form_components = $form_display->getComponents();
    $this->assertEquals(0, $form_components['field_media_image']['weight']);

    $view_display = $entity_display_repository->getViewDisplay('media', 'image_object');
    $view_components = $view_display->getComponents();
    $this->assertEquals(0, $view_components['field_media_image']['weight']);

    // Check default source mapping.
    $mapping = SchemaDotOrgMapping::load('media.image_object');
    $this->assertEquals('ImageObject', $mapping->getSchemaType());
    $expected_value = [
      'created' => 'dateCreated',
      'changed' => 'dateModified',
      'langcode' => 'inLanguage',
      'name' => 'name',
      'thumbnail' => 'thumbnail',
      'field_media_image' => 'image',
    ];
    $this->assertEquals($expected_value, $mapping->getSchemaProperties());
  }

  /**
   * Get the mapping defaults for a Schema.org mapping.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   The mapping defaults.
   */
  protected function getMappingDefaults(string $entity_type_id = '', ?string $bundle = NULL, string $schema_type = ''): array {
    $defaults = [];
    schemadotorg_media_schemadotorg_mapping_defaults_alter($defaults, $entity_type_id, $bundle, $schema_type);
    return $defaults;
  }

}
