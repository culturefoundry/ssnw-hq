<?php

declare(strict_types=1);

namespace Drupal\Tests\node\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;
use Drupal\filter\Entity\FilterFormat;

/**
 * Tests Schema.org content model documentation markup functionality.
 *
 * @covers schemadotorg_content_model_documentation_help()
 * @group schemadotorg
 */
class SchemaDotOrgContentModelDocumentationMarkupTest extends SchemaDotOrgBrowserTestBase {

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
    'markup',
    'schemadotorg_content_model_documentation',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Basic HTML',
    ])->save();
  }

  /**
   * Test content model documentation help link.
   */
  public function testHelpLink(): void {
    global $base_path;

    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    /* ********************************************************************** */

    // Create Place with the markup.module enabled.
    $this->createSchemaEntity('node', 'Place');

    $dialog_attributes = ' class="use-ajax schemadotorg-content-model-documentation-link" data-dialog-options="{&quot;width&quot;:1000,&quot;classes&quot;:{&quot;ui-dialog&quot;:&quot;schemadotorg-content-model-documentation-ui-dialog&quot;}}" data-dialog-type="modal"';
    // Check that the node add form includes a documentation field.
    $this->drupalGet('node/add/place');
    $assert->responseContains('<p>Entities that have a somewhat fixed, physical extension. <a' . $dialog_attributes . ' href="' . $base_path . 'admin/structure/types/manage/place/document" target="_blank" hreflang="en">Read documentation</a></p>');

    // Check that the node edit form includes a documentation field with a modal link.
    $node = $this->drupalCreateNode(['type' => 'place']);
    $this->drupalGet('node/' . $node->id() . '/edit');
    $assert->responseContains('<p>Entities that have a somewhat fixed, physical extension. <a' . $dialog_attributes . ' href="' . $base_path . 'admin/structure/types/manage/place/document" target="_blank" hreflang="en">Read documentation</a></p>');

    // Disable modal link.
    $this->config('schemadotorg_content_model_documentation.settings')
      ->set('link_modal', FALSE)
      ->save();

    // Check that the node edit form includes a documentation field without a modal link.
    $this->drupalGet('node/' . $node->id() . '/edit');
    $assert->responseContains('<p>Entities that have a somewhat fixed, physical extension. <a href="' . $base_path . 'admin/structure/types/manage/place/document" target="_blank" hreflang="en">Read documentation</a></p>');
  }

}
