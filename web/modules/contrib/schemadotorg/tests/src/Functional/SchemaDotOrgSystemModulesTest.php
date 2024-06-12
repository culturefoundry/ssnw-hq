<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

/**
 * Tests Schema.org sub modules configure link on the system modules form.
 *
 * @covers schemadotorg_form_system_modules_alter()
 * @group schemadotorg
 */
class SchemaDotOrgSystemModulesTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_descriptions'];

  /**
   * Test system modules form alter hooks.
   */
  public function testSystemModules(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    // Check that fragment/hash is added to sub module's configure link.
    $this->drupalGet('admin/modules');
    $assert->linkByHrefExists('/admin/config/schemadotorg/settings/general#edit-schemadotorg-descriptions');
  }

}
