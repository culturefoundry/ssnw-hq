<?php

declare(strict_types=1);

namespace Drupal\Tests\node\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests Schema.org content model documentation help functionality.
 *
 * @covers schemadotorg_content_model_documentation_help()
 * @group schemadotorg
 */
class SchemaDotOrgContentModelDocumentationHelpTest extends SchemaDotOrgBrowserTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the schema has been fixed.
   *
   * @see https://www.drupal.org/project/epp/issues/3348759
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'node',
    'help',
    'schemadotorg_content_model_documentation',
  ];

  /**
   * Test content model documentation help link.
   */
  public function testHelpLink(): void {
    $assert = $this->assertSession();

    $this->drupalPlaceBlock('help_block');
    $this->drupalLogin($this->rootUser);

    /* ********************************************************************** */

    // Create Event without the markup.module enabled.
    $this->createSchemaEntity('node', 'Event');

    // Check that the node add form help block includes a documentation link.
    $this->drupalGet('node/add/event');
    $assert->linkExists('Read documentation');
    $assert->linkByHrefExists('/admin/structure/types/manage/event/document');
    $assert->elementAttributeExists('css', 'div[role="complementary"] a', 'data-dialog-type');

    // Check that the node edit form includes a documentation link.
    $node = $this->drupalCreateNode(['type' => 'event']);
    $this->drupalGet('node/' . $node->id() . '/edit');
    $assert->linkExists('Read documentation');
    $assert->linkByHrefExists('/admin/structure/types/manage/event/document');
    $assert->elementAttributeExists('css', 'div[role="complementary"] a', 'data-dialog-type');

    // Check that schema_* fields are documented.
    $this->drupalGet('admin/structure/types/manage/event/document');
    $assert->responseContains('schema_duration');
    $assert->responseContains('schema_end_date');
    $assert->responseContains('schema_start_date');
  }

}
