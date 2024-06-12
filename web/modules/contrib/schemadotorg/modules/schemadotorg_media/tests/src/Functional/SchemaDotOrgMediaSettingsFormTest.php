<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_media\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org media settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgMediaSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_media'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Media settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_media.settings', '/admin/config/schemadotorg/settings/types');
    $this->assertSaveSettingsConfigForm('schemadotorg_media.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
