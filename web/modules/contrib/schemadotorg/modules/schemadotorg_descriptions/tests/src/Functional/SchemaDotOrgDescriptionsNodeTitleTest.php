<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_descriptions\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests for Schema.org node title descriptions.
 *
 * @covers schemadotorg_descriptions_form_node_form_alter()
 * @group schemadotorg
 */
class SchemaDotOrgDescriptionsNodeTitleTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_ui',
    'schemadotorg_descriptions',
  ];

  /**
   * Test Schema.org node title description.
   *
   * @covers schemadotorg_descriptions_form_node_form_alter()
   */
  public function testNodeTitle(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    // Create the 'article' content type.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Article']]);
    $this->submitForm([], 'Save');
    $assert->statusMessageContains('The content type Article has been added.', 'status');

    // Check that the Article's title description is set to the headline property's comment.
    $this->drupalGet('node/add/article');
    $assert->responseContains('<label for="edit-title-0-value" class="js-form-required form-required">Headline</label>');
    $assert->responseContains('Headline of the article.');

    // Set a custom description for headline.
    $this->config('schemadotorg_descriptions.settings')
      ->set('custom_descriptions.Article--headline', 'This is custom headline description.')
      ->save();

    // Check that the Article's title description is set to the headline property's custom description.
    $this->drupalGet('node/add/article');
    $assert->responseContains('This is custom headline description.');
  }

}
