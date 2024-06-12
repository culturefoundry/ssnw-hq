<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_custom_field\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org action.
 *
 * @covers \Drupal\schemadotorg_custom_field\SchemaDotOrgCustomFieldBuilder
 * @group schemadotorg
 */
class SchemaDotOrgCustomFieldBuilderTest extends SchemaDotOrgBrowserTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the custom field module has a schema.
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'schemadotorg_ui',
    'schemadotorg_custom_field',
  ];

  /**
   * Test Schema.org custom field builder.
   */
  public function testBuilder(): void {
    $assert = $this->assertSession();

    $this->createSchemaEntity('node', 'Recipe');

    $this->drupalLogin($this->rootUser);

    // Check node edit form include units.
    // @see \Drupal\schemadotorg_custom_field\SchemaDotOrgCustomFieldBuilder::fieldWidgetFormAlter
    $this->drupalGet('node/add/recipe');
    $assert->responseContains('<span class="field-suffix"> calories</span>');
    $assert->responseContains('<span class="field-suffix"> grams</span>');

    // Create a recipe node and confirm that calories includes units.
    $edit = [
      'title[0][value]' => 'Some recipe',
      'schema_nutrition[0][calories]' => '10',
    ];
    $this->submitForm($edit, 'Save');

    $assert->responseContains('<title>Some recipe | Drupal</title>');
    $assert->responseContains('<div class="field__label ">Calories</div>');
    $assert->responseContains('<div class="field__item">10 calories</div>');
  }

}
