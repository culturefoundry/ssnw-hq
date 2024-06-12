<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_paragraphs\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org layout paragraphs UI.
 *
 * @covers schemadotorg_layout_paragraphs_form_schemadotorg_mapping_form_alter()
 * @group schemadotorg
 */
class SchemaDotOrgLayoutParagraphsUiTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_layout_paragraphs',
    'schemadotorg_ui',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Check that access is allowed to 'Add Schema.org type' page.
    $account = $this->drupalCreateUser([
      'administer content types',
      'administer node fields',
      'administer schemadotorg',
    ]);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org layout paragraphs UI.
   */
  public function testLayoutParagraphsUi(): void {
    $assert = $this->assertSession();

    // Check that WebPage has layout enabled by default.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'WebPage']]);
    $assert->responseContains('Schema.org layout');
    $assert->checkboxChecked('mapping[properties][mainEntity][field][name]');

    // Check that WebSite does not have layout enabled by default.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'WebSite']]);
    $assert->responseContains('Schema.org layout');
    $assert->checkboxNotChecked('mapping[properties][mainEntity][field][name]');

    // Check that FAQPage does not have layout.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'FAQPage']]);
    $assert->responseNotContains('Schema.org layout');
  }

}
