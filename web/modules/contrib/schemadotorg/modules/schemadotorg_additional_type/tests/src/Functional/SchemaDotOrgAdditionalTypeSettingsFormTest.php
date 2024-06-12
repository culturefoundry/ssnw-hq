<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org additional type settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalTypeSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_additional_type'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org  Additional type settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_additional_type.settings', '/admin/config/schemadotorg/settings/types');
  }

}
