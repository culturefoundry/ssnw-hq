<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_cer\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org Corresponding Entity Reference settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgCorrespondingReferenceSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_cer',
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
   * Test Schema.org Corresponding Entity Reference settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_cer.settings', '/admin/config/schemadotorg/settings/types');
  }

}
