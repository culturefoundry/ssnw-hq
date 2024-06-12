<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_field_group\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface;
use Drupal\schemadotorg_field_group\SchemaDotOrgFieldGroupEntityDisplayBuilderInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the Schema.org entity display field group builder service.
 *
 * @coversClass \Drupal\schemadotorg_field_group\SchemaDotOrgFieldGroupEntityDisplayBuilder
 * @group schemadotorg
 */
class SchemaDotOrgFieldGroupEntityDisplayBuilderKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_group',
    'schemadotorg_field_group',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * The Schema.org entity display builder.
   */
  protected SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder;

  /**
   * The Schema.org field group entity display builder.
   */
  protected SchemaDotOrgFieldGroupEntityDisplayBuilderInterface $schemaFieldGroupEntityDisplayBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_field_group']);

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
    $this->schemaEntityDisplayBuilder = $this->container->get('schemadotorg.entity_display_builder');
    $this->schemaFieldGroupEntityDisplayBuilder = $this->container->get('schemadotorg_field_group.entity_display_builder');
  }

  /**
   * Test Schema.org entity display builder.
   */
  public function testEntityDisplayBuilder(): void {
    // Allow Schema.org Thing to have default properties.
    $this->config('schemadotorg.settings')
      ->set('schema_types.default_properties.Thing', ['name', 'disambiguatingDescription'])
      ->set('schema_properties.default_field_weights', ['name', 'disambiguatingDescription', 'description'])
      ->save();

    // Create node.thing.
    $mapping = $this->createSchemaEntity('node', 'Thing');

    // Check that default view display is created for Thing.
    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default');

    $field_group = $view_display->getThirdPartySettings('field_group');
    $this->assertEquals(['schema_disambiguating_desc'], $field_group['group_thing']['children']);
    $this->assertEquals('Thing', $field_group['group_thing']['label']);
    $this->assertEquals('fieldset', $field_group['group_thing']['format_type']);

    $component = $view_display->getComponent('schema_disambiguating_desc');
    $this->assertEquals('text_default', $component['type']);
    $this->assertEquals('above', $component['label']);
    $this->assertEquals(2, $component['weight']);

    $component = $view_display->getComponent('links');
    $this->assertEquals(200, $component['weight']);

    // Check that default form display is created for Thing.
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default');

    $field_group = $form_display->getThirdPartySettings('field_group');
    $this->assertEquals(['schema_disambiguating_desc'], $field_group['group_thing']['children']);
    $this->assertEquals('Thing', $field_group['group_thing']['label']);
    $this->assertEquals('details', $field_group['group_thing']['format_type']);

    $component = $form_display->getComponent('schema_disambiguating_desc');
    $this->assertEquals('text_textarea', $component['type']);
    $this->assertEquals(2, $component['weight']);

    $component = $form_display->getComponent('status');
    $this->assertEquals(220, $component['weight']);

    // Add body field to node.thing.
    // @see node_add_body_field()
    $field_storage = FieldStorageConfig::loadByName('node', 'body');
    $field_storage_values = [
      'field_storage' => $field_storage,
      'bundle' => 'thing',
      'label' => 'Body',
      'settings' => ['display_summary' => TRUE],
    ];
    $field = FieldConfig::create($field_storage_values);
    $field->save();
    $mapping
      ->setSchemaPropertyMapping('body', 'description')
      ->save();

    // Check settings entity displays for a field.
    $field_values = [
      'field_name' => 'body',
      'entity_type' => 'node',
      'bundle' => 'thing',
      'label' => 'Description',
    ];
    $widget_id = 'text_textarea_with_summary';
    $widget_settings = [
      'placeholder' => 'This is a placeholder',
      'show_summary' => TRUE,
    ];
    $formatter_id = 'text_default';
    $formatter_settings = [];
    $this->schemaEntityDisplayBuilder->setFieldDisplays(
      'Thing',
      'description',
      $field_storage_values,
      $field_values,
      $widget_id,
      $widget_settings,
      $formatter_id,
      $formatter_settings
    );

    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default');
    $component = $view_display->getComponent('body');
    $this->assertEquals('text_default', $component['type']);
    // Check that fields added to an existing view display is appended last
    // (after the links component).
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingManager::saveMapping
    $this->assertEquals(201, $component['weight']);

    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default');
    $component = $form_display->getComponent('body');
    $this->assertEquals('text_textarea_with_summary', $component['type']);
    $this->assertEquals('This is a placeholder', $component['settings']['placeholder']);
    $this->assertTrue($component['settings']['show_summary']);
    // Check that fields added to an existing form display is appended last
    // (after the status component).
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingManager::saveMapping
    $this->assertEquals(221, $component['weight']);

    // Check (re)settings entity display field weights for Schema.org properties.
    $this->schemaEntityDisplayBuilder->setFieldWeights($mapping, $mapping->getSchemaProperties());
    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default');
    $this->assertEquals(2, $view_display->getComponent('schema_disambiguating_desc')['weight']);
    $this->assertEquals(3, $view_display->getComponent('body')['weight']);
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default');
    $this->assertEquals(2, $form_display->getComponent('schema_disambiguating_desc')['weight']);
    $this->assertEquals(3, $form_display->getComponent('body')['weight']);

    // Check settings entity display field groups for Schema.org properties.
    $this->config('schemadotorg_field_group.settings')
      ->set('default_field_groups.node', [
        'general' => [
          'label' => 'General',
          // Note: Switching the order of disambiguatingDescription and description.
          'properties' => ['title', 'description', 'disambiguatingDescription'],
        ],
      ])->save();

    // Reset original schema.org properties so that all properties are
    // considered new.
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingInterface::getNewSchemaProperties
    $mapping->setOriginalSchemaProperties([]);
    $this->schemaFieldGroupEntityDisplayBuilder->setFieldGroups($mapping);

    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default');
    $this->assertEquals(2, $view_display->getComponent('schema_disambiguating_desc')['weight']);
    $this->assertEquals(1, $view_display->getComponent('body')['weight']);

    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default');
    $this->assertEquals(2, $form_display->getComponent('schema_disambiguating_desc')['weight']);
    $this->assertEquals(1, $form_display->getComponent('body')['weight']);

    $field_group = $view_display->getThirdPartySettings('field_group');
    $this->assertEquals([], $field_group['group_thing']['children']);
    $this->assertEquals(['title', 'schema_disambiguating_desc', 'body'], $field_group['group_general']['children']);
    $this->assertEquals('General', $field_group['group_general']['label']);
    $this->assertEquals('fieldset', $field_group['group_general']['format_type']);
  }

}
