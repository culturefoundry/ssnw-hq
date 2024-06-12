<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_allowed_formats\Functional;

use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org allowed formats settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgAllowedFormatsSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_allowed_formats'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
    ])->save();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Allowed Formats settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_allowed_formats.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
