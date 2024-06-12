<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_field_prefixFunctional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org field prefix settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgFieldPrefixSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_field_prefix'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Field Group settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_field_prefix.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
