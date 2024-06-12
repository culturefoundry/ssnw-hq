<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests the functionality of the Schema.org inline entity form.
 *
 * @covers schemadotorg_media_library_media_modify_schemadotorg_property_field_type_alter()
 * @covers schemadotorg_media_library_media_modify_schemadotorg_property_field_alter()
 * @group schemadotorg
 */
class SchemaDotOrgMediaLibraryMediaModifyKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media_library_media_modify',
    'views',
    'media_library',
  ];

  /**
   * Test Schema.org inline entity form.
   */
  public function testMediaLibraryMediaModify(): void {
    $this->createSchemaEntity('node', 'WebPage');

    /* ********************************************************************** */

    // Check that the image field is using
    // the 'entity_reference_entity_modify' field type.
    /** @var \Drupal\field\FieldStorageConfigInterface $field_storage_config */
    $field_storage_config = FieldStorageConfig::loadByName('node', 'schema_image');
    $this->assertEquals('entity_reference_entity_modify', $field_storage_config->getType());

    // Check that image is using the 'entity_reference_entity_modify' field type.
    /** @var \Drupal\field\FieldConfigInterface $field_config */
    $field_config = FieldConfig::loadByName('node', 'page', 'schema_image');
    $expected_settings = [
      'handler' => 'schemadotorg:media',
      'handler_settings' => [
        'target_type' => 'media',
        'schema_types' => [
          'ImageObject' => 'ImageObject',
        ],
        'target_bundles' => [],
      ],
      'target_type' => 'media',
    ];
    $this->assertEquals($expected_settings, $field_config->getSettings());

    // Check that image field form display widget is using the
    // 'media_library' form module.
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity_form_display */
    $entity_form_display = EntityFormDisplay::load('node.page.default');
    $components = $entity_form_display->getComponents();
    $this->assertEquals('media_library', $components['schema_image']['settings']['form_mode']);

  }

}
