<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_auto_entitylabel\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org auto entity label.
 *
 * @covers schemadotorg_auto_entitylabel_schemadotorg_mapping_insert()
 * @group schemadotorg
 */
class SchemaDotOrgAutoEntityLabelKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'filter',
    'token',
    'auto_entitylabel',
    'schemadotorg_auto_entitylabel',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installConfig([
      'system',
      'filter',
      'token',
      'auto_entitylabel',
      'schemadotorg_auto_entitylabel',
    ]);
  }

  /**
   * Test Schema.org auto entity labels.
   */
  public function testAutoEntityLabel(): void {
    $this->createSchemaEntity('node', 'Person');

    // Check that node.person pattern has Schema.org property tokens replaced
    // with related fields.
    $settings = $this->config('auto_entitylabel.settings.node.person')
      ->getRawData();
    $this->assertEquals('[node:schema_given_name] [node:schema_family_name]', $settings['pattern']);

    $node = Node::create([
      'type' => 'person',
      'schema_given_name' => [
        'value' => 'John',
      ],
      'schema_family_name' => [
        'value' => 'Smith',
      ],
    ]);
    $node->save();

    // Check that the person node title is automatically generated.
    $this->assertEquals('John Smith', $node->getTitle(), 'The title is set.');
  }

}
