<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_report\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org report settings form.
 *
 * @covers \Drupal\schemadotorg\Form\SchemaDotOrgReportSettingsForm
 * @group schemadotorg
 */
class SchemaDotOrgReportSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_report'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org report settings form.
   */
  public function testSchemaDotOrgReportSettingsForm(): void {
    $this->assertSaveSettingsConfigForm(
      'schemadotorg_report.settings',
      '/admin/config/schemadotorg/settings/references'
    );
  }

}
