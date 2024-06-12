<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_block_content\Functional;

use Drupal\block_content\Entity\BlockContent;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org block content module.
 *
 * @covers schemadotorg_block_content_block_view_alter()
 * @covers schemadotorg_block_content_schemadotorg_jsonld_schema_type_entity_alter()
 * @covers schemadotorg_block_content_schemadotorg_jsonld()
 *
 * @group schemadotorg
 */
class SchemaDotOrgBlockContentTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_block_content',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a text filter.
    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
    ])->save();

    // Create the Statement block type and Schema.org mapping.
    $this->createSchemaEntity('block_content', 'Statement');

    // Create a content block.
    $block_content = BlockContent::create([
      'type' => 'statement',
      'info' => 'Test',
      'body' => [
        'value' => 'This is a test',
        'format' => 'full_html',
      ],
    ]);
    $block_content->save();

    // Place the content block.
    $this->drupalPlaceBlock('block_content:' . $block_content->uuid());

    $this->drupalLogin($this->rootUser);
  }

  /**
   * Test Schema.org block_content.
   */
  public function testBlockContent(): void {
    $assert = $this->assertSession();

    // Check that the content block is displayed.
    // @see schemadotorg_block_content_block_view_alter()
    $this->drupalGet('<front>');
    $assert->responseContains('<div>This is a test</div>');
  }

}
