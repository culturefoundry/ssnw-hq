<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Test Schema.org tokens.
 *
 * @group schemadotorg
 */
class SchemaDotOrgTokenKernelTest extends SchemaDotOrgTokenKernelTestBase {

  /**
   * Tests Schema.org tokens.
   */
  public function testTokens(): void {
    // Check that a mapped node type supports the 'schemadotorg' token.
    $this->createSchemaEntity('node', 'Event');
    $node = Node::create(['type' => 'event', 'title' => 'Some event']);
    $node->save();
    $this->assertTokens(
      'node',
      ['node' => $node],
      ['schemadotorg' => 'Event']
    );

    // Check that a unmapped node type doesn't support the 'schemadotorg' token.
    NodeType::create(['type' => 'page'])->save();
    $node = Node::create(['type' => 'page', 'title' => 'Some page']);
    $node->save();
    $this->assertNoTokens(
      'node',
      ['node' => $node],
      ['schemadotorg' => 'WebPage']
    );
  }

}
