<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_preview\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD Preview settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdPreviewSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonld_preview'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org JSON-LD Preview settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_jsonld_preview.settings', '/admin/config/schemadotorg/settings/jsonld');
  }

}
