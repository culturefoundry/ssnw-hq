<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_scheduler\Kernel;

use Drupal\node\Entity\NodeType;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org scheduler.
 *
 * @covers schemadotorg_scheduler_schemadotorg_mapping_insert()
 * @group schemadotorg
 */
class SchemaDotOrgSchedulerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'views',
    'scheduler',
    'schemadotorg_scheduler',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig([
      'scheduler',
      'schemadotorg_scheduler',
    ]);
  }

  /**
   * Test Schema.org scheduler.
   */
  public function testScheduler(): void {
    $this->createSchemaEntity('node', 'Article');
    $this->createSchemaEntity('node', 'BlogPosting');
    $this->createSchemaEntity('node', 'Event');

    // Check that events have scheduled publish and unpublish enabled.
    $event_node_type = NodeType::load('event');
    $this->assertTrue($event_node_type->getThirdPartySetting('scheduler', 'publish_enable'));
    $this->assertTrue($event_node_type->getThirdPartySetting('scheduler', 'unpublish_enable'));

    // Check that article has scheduled publish enabled and unpublish disabled.
    $article_node_type = NodeType::load('article');
    $this->assertTrue($article_node_type->getThirdPartySetting('scheduler', 'publish_enable'));
    $this->assertFalse($article_node_type->getThirdPartySetting('scheduler', 'unpublish_enable'));

    // Check that blog post has scheduled publish enabled and unpublish disabled.
    $blog_node_type = NodeType::load('blog_posting');
    $this->assertTrue($blog_node_type->getThirdPartySetting('scheduler', 'publish_enable'));
    $this->assertFalse($blog_node_type->getThirdPartySetting('scheduler', 'unpublish_enable'));
  }

}
