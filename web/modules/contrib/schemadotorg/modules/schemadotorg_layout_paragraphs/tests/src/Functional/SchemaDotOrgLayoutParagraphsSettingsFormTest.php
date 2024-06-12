<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_paragraphs\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org layout paragraphs settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgLayoutParagraphsSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_layout_paragraphs'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org layout paragraphs settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_layout_paragraphs.settings', '/admin/config/schemadotorg/settings/types');
  }

}
