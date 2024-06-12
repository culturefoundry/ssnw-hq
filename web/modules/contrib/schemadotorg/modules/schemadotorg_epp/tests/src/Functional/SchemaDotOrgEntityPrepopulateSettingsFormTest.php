<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_epp\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org entity prepopulate settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgEntityPrepopulateSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_epp',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org EntityPrepopulate settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_epp.settings', '/admin/config/schemadotorg/settings/types');
  }

}
