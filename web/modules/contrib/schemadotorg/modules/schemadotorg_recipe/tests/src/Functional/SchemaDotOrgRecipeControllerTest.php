<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_recipe\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org Recipe controller.
 *
 * @group schemadotorg
 */
class SchemaDotOrgRecipeControllerTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_recipe'];

  /**
   * Test Schema.org recipe controller.
   */
  public function testController(): void {
    $assert = $this->assertSession();
    $this->drupalLogin($this->drupalCreateUser(['administer schemadotorg']));

    // Check that the 'Schema.org Recipe Test' displays as expected.
    $this->drupalGet('admin/config/schemadotorg/recipes');
    $assert->linkExists('Schema.org Recipe Test');
  }

}
