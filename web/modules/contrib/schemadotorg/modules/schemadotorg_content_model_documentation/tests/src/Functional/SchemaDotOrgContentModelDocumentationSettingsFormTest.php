<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_content_model_documentation\Functional;

use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org content model documentation settings form.
 *
 * @group schemadotorg
 */
class SchemaDotOrgContentModelDocumentationSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the schema has been fixed.
   *
   * @see https://www.drupal.org/project/epp/issues/3348759
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_content_model_documentation'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Basic HTML',
    ])->save();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org content model documentation settings form.
   */
  public function testSettingsForm(): void {
    $this->assertSaveSettingsConfigForm('schemadotorg_content_model_documentation.settings', '/admin/config/schemadotorg/settings/types');
  }

}
