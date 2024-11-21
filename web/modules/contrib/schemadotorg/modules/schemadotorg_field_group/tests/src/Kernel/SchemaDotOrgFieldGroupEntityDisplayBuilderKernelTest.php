<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_field_group\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface;
use Drupal\schemadotorg_field_group\SchemaDotOrgFieldGroupEntityDisplayBuilderInterface;

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

    $this->config('schemadotorg_field_group.settings')
      ->set('default_field_groups.node.general.description', 'Enter general information')
      ->save();

    // Add custom_b field to the general field group's properties.
    $properties = $this->config('schemadotorg_field_group.settings')
      ->get('default_field_groups.node.general.properties');
    $properties[] = 'custom_b';
    $this->config('schemadotorg_field_group.settings')
      ->set('default_field_groups.node.general.properties', $properties)
      ->save();

    // Create node.thing with a custom field.
    $defaults = [
      'properties' => [
        'custom_a' => [
          'name' => 'custom_a',
          'type' => 'string',
          'label' => 'Custom A',
          'group' => 'general',
          'group_field_weight' => -100,
        ],
        'custom_b' => [
          'name' => 'custom_b',
          'type' => 'string',
          'label' => 'Custom B',
        ],
      ],
    ];
    $mapping = $this->createSchemaEntity('node', 'Thing', $defaults);

    // Check that default view display is created for Thing.
    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default');

    $field_group = $view_display->getThirdPartySettings('field_group');

    // Check the general field group.
    $this->assertEquals(['custom_a', 'custom_b', 'title'], $field_group['group_general']['children']);
    $this->assertEquals('General', $field_group['group_general']['label']);
    $this->assertEquals(-20, $field_group['group_general']['weight']);
    $this->assertEquals([], $field_group['group_general']['format_settings']);

    // Check the custom component.
    $component = $view_display->getComponent('custom_a');
    $this->assertEquals('string', $component['type']);
    $this->assertEquals('above', $component['label']);
    $this->assertEquals(-100, $component['weight']);

    // Check the thing field group.
    $this->assertEquals(['schema_disambiguating_desc'], $field_group['group_thing']['children']);
    $this->assertEquals('Thing', $field_group['group_thing']['label']);
    $this->assertEquals(0, $field_group['group_thing']['weight']);
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

    // Check the general field group.
    $this->assertEquals(['custom_a', 'custom_b', 'title'], $field_group['group_general']['children']);
    $this->assertEquals('General', $field_group['group_general']['label']);
    $this->assertEquals(-20, $field_group['group_general']['weight']);
    $this->assertEquals('Enter general information', $field_group['group_general']['format_settings']['description']);

    // Check the thing field group.
    $this->assertEquals(['schema_disambiguating_desc'], $field_group['group_thing']['children']);
    $this->assertEquals('Thing', $field_group['group_thing']['label']);
    $this->assertEquals(0, $field_group['group_thing']['weight']);
    $this->assertEquals('details', $field_group['group_thing']['format_type']);

    // Check the title component.
    $component = $form_display->getComponent('title');
    $this->assertEquals('string_textfield', $component['type']);
    $this->assertEquals(1, $component['weight']);

    // Check the disambiguating_description component.
    $component = $form_display->getComponent('schema_disambiguating_desc');
    $this->assertEquals('text_textarea', $component['type']);
    $this->assertEquals(2, $component['weight']);

    // Check that status weight.
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
    $field = [
      'field_name' => 'body',
      'entity_type' => 'node',
      'bundle' => 'thing',
      'label' => 'Description',
      'schema_type' => 'Thing',
      'schema_property' => 'description',
    ];
    $widget_id = 'text_textarea_with_summary';
    $widget_settings = [
      'placeholder' => 'This is a placeholder',
      'show_summary' => TRUE,
    ];
    $formatter_id = 'text_default';
    $formatter_settings = [];
    $this->schemaEntityDisplayBuilder->setFieldDisplays(
      $field,
      $widget_id,
      $widget_settings,
      $formatter_id,
      $formatter_settings
    );

    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default');
    $component = $view_display->getComponent('body');
    $this->assertEquals('text_default', $component['type']);
    $this->assertEquals(18, $component['weight']);

    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default');
    $component = $form_display->getComponent('body');
    $this->assertEquals('text_textarea_with_summary', $component['type']);
    $this->assertEquals('This is a placeholder', $component['settings']['placeholder']);
    $this->assertTrue($component['settings']['show_summary']);
    $this->assertEquals(18, $component['weight']);
  }

}
