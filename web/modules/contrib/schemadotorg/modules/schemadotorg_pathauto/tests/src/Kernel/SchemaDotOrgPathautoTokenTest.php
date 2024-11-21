<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_pathauto\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgTokenKernelTestBase;
use Drupal\node\Entity\Node;

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
    $this->appendSchemaTypeDefaultProperties('Thing', 'alternateName');

    // Check that a mapped node type supports 'schemadotorg:*' tokens.
    $this->createSchemaEntity('node', 'Event');
    $node = Node::create(['type' => 'event', 'title' => 'Some event']);
    $node->save();
    $this->assertTokens(
      'node',
      ['node' => $node],
      [
        'schemadotorg:base-path' => 'events',
        'schemadotorg:alternate-name' => 'Some event',
      ]
    );

    // Check that a mapped node type supports 'schemadotorg:alternate-name' token.
    $this->createSchemaEntity('node', 'Thing');
    $node = Node::create([
      'type' => 'thing',
      'title' => 'Some thing',
      'schema_alternate_name' => 'thing',
    ]);
    $node->save();
    $this->assertTokens(
      'node',
      ['node' => $node],
      [
        'schemadotorg:base-path' => NULL,
        'schemadotorg:alternate-name' => 'thing',
      ]
    );

  }

}
