<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_type_tray\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org type tray.
 *
 * @group schemadotorg
 */
class SchemaDotOrgTypeTrayKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'type_tray',
    'schemadotorg_type_tray',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['type_tray', 'schemadotorg_type_tray']);

  }

  /**
   * Test Schema.org type tray.
   */
  public function testTypeTray(): void {
    global $base_path;
    // Get type tray icon path.
    $module_path = \Drupal::service('extension.list.module')->getPath('schemadotorg_type_tray');
    $icon_path = $base_path . $module_path . '/images/schemadotorg_type_tray/icon';

    // Check syncing grouped Schema.org types with type tray categories.
    // @covers _schemadotorg_type_tray_sync_schema_types_with_categories()
    $this->assertNull($this->config('type_tray.settings')->get('categories'));
    \Drupal::moduleHandler()->loadInclude('schemadotorg_type_tray', 'install');
    schemadotorg_type_tray_install(FALSE);
    $expected_categories = [
      'common' => 'Common',
      'web' => 'Web',
      'content' => 'Content',
      'organization' => 'Organization',
      'hospitality' => 'Hospitality',
      'education' => 'Education',
      'food' => 'Food',
      'podcast' => 'Podcast',
      'tv' => 'TV',
      'medical_organization' => 'Medical organization',
      'medical_information' => 'Medical information',
    ];
    $this->assertEquals($expected_categories, $this->config('type_tray.settings')->get('categories'));

    // Check that type tray settings are added to the person node type.
    $mapping = $this->createSchemaEntity('node', 'Person');
    $expected_settings = [
      'type_category' => 'common',
      'type_thumbnail' => '',
      'type_icon' => "$icon_path/person.png",
      'existing_nodes_link_text' => 'View existing <em class="placeholder">Person</em> content',
      'type_weight' => -19,
    ];
    $node_type = $mapping->getTargetEntityBundleEntity();
    $this->assertEquals($expected_settings, $node_type->getThirdPartySettings('type_tray'));

    // Check that type tray settings w/o existing link are added to the
    // event node type.
    $this->config('schemadotorg_type_tray.settings')
      ->set('existing_nodes_link_text', '')
      ->save();
    $mapping = $this->createSchemaEntity('node', 'Event');
    $expected_settings = [
      'type_category' => 'common',
      'type_thumbnail' => '',
      'type_icon' => "$icon_path/event.png",
      'existing_nodes_link_text' => '',
      'type_weight' => -18,
    ];
    $node_type = $mapping->getTargetEntityBundleEntity();
    $this->assertEquals($expected_settings, $node_type->getThirdPartySettings('type_tray'));
  }

}
