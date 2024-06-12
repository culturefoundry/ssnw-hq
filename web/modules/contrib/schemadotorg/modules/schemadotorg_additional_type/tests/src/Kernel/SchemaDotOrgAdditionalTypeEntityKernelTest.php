<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Kernel;

use Drupal\node\Entity\NodeType;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\Tests\schemadotorg_additional_type\Traits\SchemaDotOrgAdditionalTypeTestTrait;

/**
 * Tests Schema.org additional type entity types.
 *
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalTypeEntityKernelTest extends SchemaDotOrgEntityKernelTestBase {
  use SchemaDotOrgAdditionalTypeTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_additional_type',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('node_type');

    $this->installConfig(['schemadotorg_additional_type']);
  }

  /**
   * Tests creating common entity type/bundle Schema.org types.
   */
  public function testCreateSchemaEntity(): void {
    // Check creating node:Event Schema.org mapping with additional type.
    $mapping = $this->createSchemaEntity('node', 'Event');
    $this->assertEquals('node', $mapping->getTargetEntityTypeId());
    $this->assertEquals('event', $mapping->getTargetBundle());
    $this->assertEquals('Event', $mapping->getSchemaType());
    $this->assertEquals($mapping->getSchemaProperties(), [
      'body' => 'description',
      'langcode' => 'inLanguage',
      'schema_duration' => 'duration',
      'schema_end_date' => 'endDate',
      'schema_start_date' => 'startDate',
      'title' => 'name',
      'schema_event_type' => 'additionalType',
    ]);

    // Create Thing with mapping.
    $node_type = NodeType::create([
      'type' => 'thing',
      'name' => 'Thing',
    ]);
    $node_type->save();
    $node_mapping = SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'thing',
      'schema_type' => 'Thing',
      'schema_properties' => [
        'title' => 'name',
        'schema_alternate_name' => 'alternateName',
        'schema_thing_type' => 'additionalType',
      ],
    ]);
    $node_mapping->save();
    $this->createSchemaDotOrgField('node', 'Thing');
    $this->createSchemaDotOrgAdditionalTypeField('node', 'Thing');

    // Check getting the mappings for Schema.org properties with additional type.
    $expected_schema_properties = [
      'title' => 'name',
      'schema_alternate_name' => 'alternateName',
      'schema_thing_type' => 'additionalType',
    ];
    $this->assertEquals($expected_schema_properties, $node_mapping->getSchemaProperties());

    // Check getting the field name for a additional type property.
    $this->assertEquals('schema_thing_type', $node_mapping->getSchemaPropertyFieldName('additionalType'));
  }

}
