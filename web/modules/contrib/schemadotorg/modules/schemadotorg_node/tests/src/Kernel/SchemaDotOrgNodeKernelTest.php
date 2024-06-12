<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_metatag\Kernel;

use Drupal\node\Entity\NodeType;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org node.
 *
 * @covers schemadotorg_node_schemadotorg_mapping_insert()
 * @group schemadotorg
 */
class SchemaDotOrgNodeKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'menu_ui',
    'schemadotorg_node',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_node']);
  }

  /**
   * Test Schema.org node.
   */
  public function testNode(): void {
    $this->createSchemaEntity('node', 'Place');

    $node_type = NodeType::load('place');
    $this->assertFalse($node_type->displaySubmitted());
    $this->assertEquals([], $node_type->getThirdPartySetting('menu_ui', 'available_menus'));
    $this->assertEquals('', $node_type->getThirdPartySetting('menu_ui', 'parent'));
  }

}
