<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_pathauto\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgTokenKernelTestBase;

/**
 * Test Schema.org Pathauto tokens.
 *
 * @group schemadotorg
 */
class SchemaDotOrgPathautoTokenTest extends SchemaDotOrgTokenKernelTestBase {

  /**
   * Modules.
   *
   * @var string[]
   */
  protected static $modules = [
    'path',
    'path_alias',
    'pathauto',
    'schemadotorg_pathauto',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setup();

    $this->installEntitySchema('path_alias');
    $this->installConfig([
      'pathauto',
      'schemadotorg_pathauto',
    ]);
  }

  /**
   * Tests Schema.org tokens.
   */
  public function testTokens(): void {
    // Check that a mapped node type supports the 'schemadotorg:base-path' token.
    $this->createSchemaEntity('node', 'Event');
    $node = Node::create(['type' => 'event', 'title' => 'Some event']);
    $node->save();
    $this->assertTokens(
      'node',
      ['node' => $node],
      ['schemadotorg:base-path' => 'events']
    );
  }

}
