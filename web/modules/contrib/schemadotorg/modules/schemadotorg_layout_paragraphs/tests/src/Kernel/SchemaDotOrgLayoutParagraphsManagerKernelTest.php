<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_layout_paragraphs\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org Layout Paragraphs manager.
 *
 * @covers \Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManager
 *
 * @group schemadotorg
 */
class SchemaDotOrgLayoutParagraphsManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_layout_paragraphs',
  ];

  /**
   * The Schema.org Layout Paragraphs manager.
   */
  protected SchemaDotOrgLayoutParagraphsManagerInterface $layoutParagraphsManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig('schemadotorg_layout_paragraphs');

    /** @var \Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManagerInterface $layout_paragraphs_manager */
    $layout_paragraphs_manager = $this->container->get('schemadotorg_layout_paragraphs.manager');
    $this->layoutParagraphsManager = $layout_paragraphs_manager;
  }

  /**
   * Test Schema.org Blueprints Layout Paragraphs manager.
   */
  public function testManager(): void {
    // Check altering Schema.org mapping defaults to support layout paragraphs.
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'WebPage',
    );
    $expected_defaults = [
      'name' => '_add_',
      'type' => 'field_ui:entity_reference_revisions:paragraph',
      'label' => 'Layout',
      'machine_name' => 'main_entity',
      'unlimited' => TRUE,
      'required' => FALSE,
      'description' => 'A layout built using paragraphs. Layout paragraphs allows site builders to construct a multi-column landing page using Schema.org related paragraphs types.',
    ];
    $this->assertEquals($expected_defaults, $defaults['properties']['mainEntity']);
    $this->assertEquals(SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD, $defaults['properties']['mainEntity']['name']);

    // Check specifying a default type as bundle works as expected.
    $this->config('schemadotorg_layout_paragraphs.settings')
      ->set('default_types', ['advanced_page'])
      ->save();
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'WebPage',
    );
    $this->assertEquals('', $defaults['properties']['mainEntity']['name']);
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      bundle: 'advanced_page',
      schema_type: 'WebPage',
    );
    $this->assertEquals(SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD, $defaults['properties']['mainEntity']['name']);

    // Check that altering field storage and field values works as expected.
    $this->mappingManager->createType(
      entity_type_id: 'node',
      schema_type: 'WebPage',
      defaults: ['entity' => ['id' => 'advanced_page']],
    );
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = FieldConfig::loadByName('node', 'advanced_page', 'schema_main_entity');
    $this->assertEquals('Layout', $field_config->label());
  }

}
