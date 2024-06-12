<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonapi_preview\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON:API preview node tab/task.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonApiPreviewNodeTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonapi', 'schemadotorg_jsonapi_preview'];

  /**
   * Test Schema.org Schema.org JSON:API preview node tab/task.
   */
  public function testNode(): void {
    $assert = $this->assertSession();

    $account = $this->createUser(['access content', 'administer nodes', 'view schemadotorg jsonapi']);

    $this->drupalPlaceBlock('page_title_block');
    $this->drupalPlaceBlock('local_tasks_block');

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);

    $node = $this->drupalCreateNode([
      'type' => 'thing',
      'title' => 'Something',
    ]);
    $node->save();

    // Check that JSON:API tab is not displayed for users without permission.
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('JSON:API');

    $this->drupalLogin($account);

    // Check that JSON:API preview is displayed.
    $this->drupalGet($node->toUrl());
    $assert->responseContains('JSON:API');

    // Hide the JSON:API preview task.
    \Drupal::configFactory()
      ->getEditable('schemadotorg_jsonapi_preview.settings')
      ->set('node_task', FALSE)
      ->save();

    // Clear all plugin caches.
    \Drupal::service('plugin.cache_clearer')->clearCachedDefinitions();

    // Rebuild the menu router based on all rebuilt data.
    // Important: This rebuild must happen last, so the menu router is guaranteed
    // to be based on up to date information.
    \Drupal::service('router.builder')->rebuild();

    // Check that JSON:API preview is displayed.
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('JSON:API');

  }

}
