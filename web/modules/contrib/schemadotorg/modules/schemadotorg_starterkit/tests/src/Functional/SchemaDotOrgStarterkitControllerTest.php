<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_starterkit\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org Starter kit controller.
 *
 * @group schemadotorg
 */
class SchemaDotOrgStarterkitControllerTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_starterkit'];

  /**
   * Test Schema.org starter kit controller.
   */
  public function testController(): void {
    $assert = $this->assertSession();
    $this->drupalLogin($this->drupalCreateUser(['administer schemadotorg']));

    // Check missing starter kit missing a dependency.
    $this->drupalGet('admin/config/schemadotorg/starterkits');
    $assert->responseContains(' <td><ul><li>missing_dependency <em>(Missing)</em></li></ul></td>');
  }

}
