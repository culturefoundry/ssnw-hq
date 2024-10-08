<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_entity_reference_override\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org entity reference override settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgEntityReferenceOverrideSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the entity_reference_override.module has fixed its schema.
   *
   * Issue #3331271: Schema definition for the "override_format" setting is missing.
   *
   * @see https://www.drupal.org/project/entity_reference_override/issues/3331271
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_entity_reference_override'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org role settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_entity_reference_override.settings', '/admin/config/schemadotorg/settings/properties');
  }

}
