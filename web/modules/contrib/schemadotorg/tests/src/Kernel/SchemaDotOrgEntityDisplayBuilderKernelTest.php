<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface;

/**
 * Tests the Schema.org entity display builder service.
 *
 * @coversClass \Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilder
 * @group schemadotorg
 */
class SchemaDotOrgEntityDisplayBuilderKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * The Schema.org entity display builder.
   */
  protected SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->schemaEntityDisplayBuilder = $this->container->get('schemadotorg.entity_display_builder');
  }

  /**
   * Test Schema.org entity display builder.
   */
  public function testEntityDisplayBuilder(): void {
    $mapping = $this->createSchemaEntity('node', 'Thing');

    // Check getting default field weights.
    $default_field_weights = $this->schemaEntityDisplayBuilder->getDefaultFieldWeights();
    $this->assertEquals(2, $default_field_weights['name']);
    $this->assertEquals(3, $default_field_weights['title']);
    $this->assertEquals(4, $default_field_weights['alternateName']);
    $this->assertEquals(12, $default_field_weights['description']);

    // Check setting entity displays for a field.
    $this->schemaEntityDisplayBuilder->setFieldDisplays(
      '',
      '',
      [],
      [
        'entity_type' => 'node',
        'bundle' => 'thing',
        'field_name' => 'name',
      ],
      NULL,
      [],
      NULL,
      []
    );
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $entity_view_display */
    $entity_view_display = EntityViewDisplay::load('node.thing.default');
    $expected_value = [
      'settings' => [],
      'third_party_settings' => [],
      'weight' => 201,
      'region' => 'content',
    ];
    $this->assertEquals($expected_value, $entity_view_display->getComponent('name'));

    // Check setting entity display field weights for Schema.org properties.
    $this->schemaEntityDisplayBuilder->setFieldWeights($mapping, ['name' => 'name']);
    \Drupal::entityTypeManager()->getStorage('entity_view_display')->resetCache();
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $entity_view_display */
    $entity_view_display = EntityViewDisplay::load('node.thing.default');
    $expected_value = [
      'settings' => [],
      'third_party_settings' => [],
      'weight' => 2,
      'region' => 'content',
    ];
    $this->assertEquals($expected_value, $entity_view_display->getComponent('name'));

    // Check settings the default component weights.
    $this->schemaEntityDisplayBuilder->setComponentWeights($mapping);
    \Drupal::entityTypeManager()->getStorage('entity_form_display')->resetCache();
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $entity_view_display */
    $entity_form_display = EntityFormDisplay::load('node.thing.default');
    $components = $entity_form_display->getComponents();
    $this->assertEquals(200, $components['uid']['weight']);
    $this->assertEquals(210, $components['promote']['weight']);

    // Check getting display form modes for a specific entity type.
    $this->assertEquals(['default' => 'default'], $this->schemaEntityDisplayBuilder->getFormModes('node', 'page'));

    // Check getting display view modes for a specific entity type.
    $this->assertEquals(['default' => 'default'], $this->schemaEntityDisplayBuilder->getViewModes('node', 'page'));

    // Check Schema.org types default view display properties for Event.
    $this->createSchemaEntity('node', 'Event');
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $entity_view_display */
    $entity_view_display = EntityViewDisplay::load('node.event.teaser');
    $expected_components = [
      'body',
      'links',
      'schema_end_date',
      'schema_start_date',
      'uid',
      'title',
      'created',
    ];
    $this->assertEquals($expected_components, array_keys($entity_view_display->getComponents()));
  }

}
