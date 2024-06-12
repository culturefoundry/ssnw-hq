<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_endpoint\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD endpoint settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdEndpointSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonld_endpoint'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org JSON-LD settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_jsonld_endpoint.settings', '/admin/config/schemadotorg/settings/jsonld');
  }

}
