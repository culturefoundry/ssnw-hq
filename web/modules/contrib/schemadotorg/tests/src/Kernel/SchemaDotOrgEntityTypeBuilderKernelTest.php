<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Entity\Entity\EntityFormMode;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilderInterface;

/**
 * Tests the Schema.org entity type builder service.
 *
 * @coversClass \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilder
 * @group schemadotorg
 */
class SchemaDotOrgEntityTypeBuilderKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgEntityTypeBuilderInterface $schemaEntityTypeBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
    $this->schemaEntityTypeBuilder = $this->container->get('schemadotorg.entity_type_builder');

    // Create teaser display mode.
    EntityViewMode::create([
      'id' => 'node.teaser',
      'label' => 'Teaser',
      'targetEntityType' => 'node',
    ])->save();

    // Create custom display mode.
    EntityViewMode::create([
      'id' => 'node.custom',
      'label' => 'Custom',
      'targetEntityType' => 'node',
    ])->save();

    // Create custom form mode.
    EntityFormMode::create([
      'id' => 'node.custom',
      'label' => 'Custom',
      'targetEntityType' => 'node',
    ])->save();
  }

  /**
   * Test Schema.org entity type builder.
   */
  public function testEntityTypeBuilder(): void {
    // Check adding an entity bundle.
    $values = [
      'entity' => [
        'label' => 'Thing',
        'id' => 'thing',
      ],
    ];
    $bundle_entity = $this->schemaEntityTypeBuilder->addEntityBundle('node_type', 'Thing', $values);
    $this->assertEquals('thing', $bundle_entity->id());
    $this->assertEquals('Thing', $bundle_entity->label());
    $this->assertEquals('Thing', $bundle_entity->schemaDotOrgType);
    $this->assertEquals([], $this->entityDisplayRepository->getFormModeOptionsByBundle('node', 'thing'));
    $this->assertEquals(['teaser' => 'Teaser'], $this->entityDisplayRepository->getViewModeOptionsByBundle('node', 'thing'));

    // Check that a 'teaser' view display is created for the Thing node type.
    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'teaser');
    $this->assertFalse($view_display->isNew());

    // Check that a 'custom' view display is NOT created for the Thing node type.
    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'custom');
    $this->assertTrue($view_display->isNew());

    // Enable custom form and view displays for Thing node type.
    $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'custom')->save();
    $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'custom')->save();
    $this->assertEquals(['custom' => 'Custom'], $this->entityDisplayRepository->getFormModeOptionsByBundle('node', 'thing'));
    $this->assertEquals(['teaser' => 'Teaser', 'custom' => 'Custom'], $this->entityDisplayRepository->getViewModeOptionsByBundle('node', 'thing'));

    // Check adding an alternateName (string) field to an entity.
    $field = [
      'name' => SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD,
      'type' => 'string',
      'label' => 'Alternate names',
      'machine_name' => 'schema_alternate_name',
      'description' => '',
      'unlimited' => '1',
      'required' => '1',
      'max_length' => 50,
      'schema_type' => 'Thing',
      'schema_property' => 'alternateName',
    ];
    $this->schemaEntityTypeBuilder->addFieldToEntity('node', 'thing', $field);

    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = FieldStorageConfig::load('node.schema_alternate_name');
    $this->assertEquals(50, $field_storage->getSetting('max_length'));
    $this->assertEquals(-1, $field_storage->getCardinality());

    /** @var \Drupal\field\FieldConfigInterface $field */
    $field = FieldConfig::load('node.thing.schema_alternate_name');
    $this->assertEquals('Alternate names', $field->label());
    $this->assertEquals('schema_alternate_name', $field->getName());

    // Check the Thing default form display mode.
    $form_components = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default')->getComponents();
    $this->assertArrayHasKey('title', $form_components);
    $this->assertArrayHasKey('status', $form_components);
    $this->assertArrayHasKey('sticky', $form_components);
    $this->assertArrayHasKey('schema_alternate_name', $form_components);

    // Check the Thing custom form display mode.
    $form_components = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'custom')->getComponents();
    $this->assertArrayHasKey('schema_alternate_name', $form_components);

    // Check the Thing default view display mode.
    $view_components = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default')->getComponents();
    $this->assertArrayHasKey('title', $view_components);
    $this->assertArrayHasKey('schema_alternate_name', $view_components);

    // Check the Thing custom view display mode.
    $view_components = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'custom')->getComponents();
    $this->assertArrayNotHasKey('schema_alternate_name', $view_components);

    // Check the Thing teaser view display mode.
    $view_components = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'teaser')->getComponents();
    $this->assertArrayNotHasKey('schema_alternate_name', $view_components);

    // Check adding a description (body) field to an entity.
    $field = [
      'name' => SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD,
      'type' => 'text_with_summary',
      'label' => 'Body',
      'machine_name' => 'body',
      'description' => '',
      'unlimited' => '0',
      'required' => '0',
      'schema_type' => 'Thing',
      'schema_property' => 'description',
    ];
    $this->schemaEntityTypeBuilder->addFieldToEntity('node', 'thing', $field);

    // Check that body is included in all form modes.
    $form_components = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'default')->getComponents();
    $this->assertArrayHasKey('body', $form_components);
    $form_components = $this->entityDisplayRepository->getFormDisplay('node', 'thing', 'custom')->getComponents();
    $this->assertArrayHasKey('body', $form_components);

    // Check that body is included in default and teaser display modes.
    $view_components = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default')->getComponents();
    $this->assertArrayHasKey('body', $view_components);
    $view_components = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'teaser')->getComponents();
    $this->assertArrayHasKey('body', $view_components);
    $view_components = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'custom')->getComponents();
    $this->assertArrayNotHasKey('body', $view_components);

    // Check that the body displays the summary with the title hidden.
    $body_component = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'teaser')->getComponent('body');
    $this->assertEquals('text_summary_or_trimmed', $body_component['type']);
    $this->assertEquals('hidden', $body_component['label']);

    // Check adding an image field to an entity.
    $field = [
      'name' => SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD,
      'type' => 'image',
      'label' => ' Image',
      'machine_name' => 'schema_image',
      'description' => '',
      'unlimited' => '0',
      'required' => '1',
      'schema_type' => 'Thing',
      'schema_property' => 'image',
    ];
    $this->schemaEntityTypeBuilder->addFieldToEntity('node', 'thing', $field);

    // Check the field default format settings for image has the label hidden.
    $view_component = $this->entityDisplayRepository->getViewDisplay('node', 'thing', 'default')
      ->getComponent('schema_image');
    $this->assertEquals('hidden', $view_component['label']);

    // Check adding a additionalType field to an entity.
    $field = [
      'name' => SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD,
      'type' => 'list_string',
      'label' => 'Additional type',
      'machine_name' => 'schema_additional_type',
      'schema_type' => 'Thing',
      'schema_property' => 'additionalType',
      'default_value' => 'one',
      'allowed_values' => [
        'one' => 'One',
        'two' => 'Two',
        'three' => 'Three',
      ],
    ];
    $this->schemaEntityTypeBuilder->addFieldToEntity('node', 'thing', $field);

    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = FieldStorageConfig::load('node.schema_additional_type');
    $expected_allowed_values = [
      'one' => 'One',
      'two' => 'Two',
      'three' => 'Three',
    ];
    $this->assertEquals($expected_allowed_values, $field_storage->getSetting('allowed_values'));

    /** @var \Drupal\field\FieldConfigInterface $field */
    $field = FieldConfig::load('node.thing.schema_additional_type');
    $this->assertEquals(['value' => 'one'], $field->get('default_value'));
  }

}
