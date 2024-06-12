<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonapi_preview\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON:API Preview settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonApiPreviewSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonapi_preview'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org JSON:API Preview settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_jsonapi_preview.settings', '/admin/config/schemadotorg/settings/jsonapi');
  }

}
