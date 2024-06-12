<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

/**
 * Tests the functionality of the Schema.org settings form.
 *
 * @covers \Drupal\schemadotorg\Form\SchemaDotOrgSettingsPropertiesForm
 * @covers \Drupal\schemadotorg\Form\SchemaDotOrgNamesForm
 * @group schemadotorg
 */
class SchemaDotOrgSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg.settings', '/admin/config/schemadotorg/settings/types');
    $this->assertSaveSettingsConfigForm('schemadotorg.settings', '/admin/config/schemadotorg/settings/properties');
    $this->assertSaveSettingsConfigForm('schemadotorg.settings', '/admin/config/schemadotorg/settings/names');
  }

}
