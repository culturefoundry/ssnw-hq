<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_ui\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org mapping type select form.
 *
 * @covers \Drupal\schemadotorg_ui\Form\SchemaDotOrgUiMappingTypeSelectForm
 * @group schemadotorg
 */
class SchemaDotOrgUiMappingTypeSelectFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_ui_test'];

  /**
   * Test Schema.org mapping type select form.
   */
  public function testMappingTypeSelectForm(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    // Check displaying find Schema.org type form.
    $this->drupalGet('schemadotorg_ui_test/mapping_type_select');
    $assert->fieldExists('type');
    $assert->buttonExists('Find');

    // Check validating the schema type before continuing.
    $this->submitForm(['type' => 'NotThing'], 'Find');
    $assert->statusMessageContains('The Schema.org type NotThing is not valid.', 'error');
    $assert->fieldExists('type');

    // Check submitting and redirecting to the <current> page with
    // ?type query parameter.
    $this->submitForm(['type' => 'ContactPoint'], 'Find');
    $assert->addressEquals('/schemadotorg_ui_test/mapping_type_select?type=ContactPoint');
  }

}
