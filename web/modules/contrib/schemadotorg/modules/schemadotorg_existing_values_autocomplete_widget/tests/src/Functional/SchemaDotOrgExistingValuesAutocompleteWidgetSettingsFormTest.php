<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_existing_values_autocomplete_widget\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org Existing values autocomplete widget settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgExistingValuesAutocompleteWidgetSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_existing_values_autocomplete_widget'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Existing values autocomplete widget settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_existing_values_autocomplete_widget.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
