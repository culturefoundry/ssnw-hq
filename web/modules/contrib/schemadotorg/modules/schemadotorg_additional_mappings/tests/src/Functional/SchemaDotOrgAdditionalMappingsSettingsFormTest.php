<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org additional mappings settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalMappingsSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_additional_mappings'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org webpage settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_additional_mappings.settings', '/admin/config/schemadotorg/settings/types');
  }

}
