<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_metatag\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org metatag settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgMetatagSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_metatag'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Metatag settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_metatag.settings', '/admin/config/schemadotorg/settings/types');
    $this->assertSaveSettingsConfigForm('schemadotorg_metatag.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
