<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_type_tray\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org type tray settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgTypeTraySettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_type_tray'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org type tray settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_type_tray.settings', '/admin/config/schemadotorg/settings/types');
  }

}
