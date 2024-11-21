<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

/**
 * Tests the functionality of the Schema.org Entity Browser integration.
 *
 * @covers entity_browser_schemadotorg_property_field_alter()
 * @group MskPHPUnit
 */
class SchemaDotOrgEntityBrowserKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_browser',
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
    $this->installConfig('entity_browser');

    \Drupal::entityTypeManager()
      ->getStorage('entity_browser')
      ->create([
        'name' => 'browse_content',
        'label' => 'browse_content',
        'display' => 'modal',
        'selection_display' => 'no_display',
        'widget_selector' => 'tabs',
      ])
      ->save();

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
  }

  /**
   * TestSchema.org Entity Browser integration.
   */
  public function testEntityBrowser(): void {
    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.relatedLink.type', 'field_ui:entity_reference:node')
      ->save();

    // Create a WebPage content type with a 'relatedLink' entity reference field.
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

    // Create a AboutPage content type with a 'relatedLink' entity reference field.
    $defaults = [
      'properties' => [
        'relatedLink' => [
          'widget_id' => 'entity_reference_autocomplete',
        ],
      ],
    ];
    $this->createSchemaEntity('node', 'AboutPage', $defaults);

    // Check that the content browser component's type and settings are defined as expected.
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'about_page');
    $component = $form_display->getComponent('schema_related_link');
    $this->assertEquals('entity_reference_autocomplete', $component['type']);
  }

}
