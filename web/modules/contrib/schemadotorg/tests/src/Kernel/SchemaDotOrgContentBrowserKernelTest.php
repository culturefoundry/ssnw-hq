<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

/**
 * Tests the functionality of the Schema.org Content Browser integration.
 *
 * @covers content_browser_schemadotorg_property_field_alter()
 * @group MskPHPUnit
 */
class SchemaDotOrgContentBrowserKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_browser',
    'content_browser',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
  }

  /**
   * TestSchema.org Content Browser integration.
   */
  public function testContentBrowser(): void {
    // Create a WebPage content type with a 'relatedLink' entity reference field.
    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.relatedLink.type', 'field_ui:entity_reference:node')
      ->save();
    $this->createSchemaEntity('node', 'WebPage');

    // Check that the content browser component's type and settings are defined as expected.
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'page');
    $component = $form_display->getComponent('schema_related_link');
    $this->assertEquals('entity_browser_entity_reference', $component['type']);
    $expected_settings = [
      'entity_browser' => 'browse_content',
      'field_widget_display' => 'label',
      'field_widget_edit' => TRUE,
      'field_widget_remove' => TRUE,
      'field_widget_replace' => TRUE,
      'open' => FALSE,
      'field_widget_display_settings' => [],
      'selection_mode' => 'selection_append',
    ];
    $this->assertEquals($expected_settings, $component['settings']);
  }

}
