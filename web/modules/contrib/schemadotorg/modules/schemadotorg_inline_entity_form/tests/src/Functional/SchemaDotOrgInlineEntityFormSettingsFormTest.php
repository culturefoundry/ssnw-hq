<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_inline_entity_form\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org inline entity form settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgInlineEntityFormSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_inline_entity_form'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Inline Entity Form settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_inline_entity_form.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
