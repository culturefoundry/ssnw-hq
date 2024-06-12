<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_diagram\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org node tab/task.
 *
 * @group schemadotorg
 */
class SchemaDotOrgNodeLocalTaskTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_node',
    'schemadotorg_diagram',
    'schemadotorg_jsonapi_preview',
    'schemadotorg_jsonld_preview',
  ];

  /**
   * Test Schema.org Schema.org node tab/task..
   */
  public function testNode(): void {
    $assert = $this->assertSession();

    $account = $this->createUser([
      'access content',
      'administer nodes',
      'view schemadotorg diagram',
      'view schemadotorg jsonapi',
      'view schemadotorg jsonld',
    ]);

    $this->drupalPlaceBlock('page_title_block');
    $this->drupalPlaceBlock('local_tasks_block');

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);

    $node = $this->drupalCreateNode([
      'type' => 'thing',
      'title' => 'Something',
    ]);
    $node->save();

    // Check that Schema.org tab is not displayed for users without permission.
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org');

    $this->drupalLogin($account);

    // Check that Schema.org preview is displayed.
    $this->drupalGet($node->toUrl());
    $assert->responseContains('Schema.org');

    // Hide the Schema.org preview task.
    $config_names = [
      'schemadotorg_diagram.settings',
      'schemadotorg_jsonapi_preview.settings',
      'schemadotorg_jsonld_preview.settings',
    ];
    foreach ($config_names as $config_name) {
      \Drupal::configFactory()
        ->getEditable($config_name)
        ->set('node_task', FALSE)
        ->save();
    }

    // Clear all plugin caches.
    \Drupal::service('plugin.cache_clearer')->clearCachedDefinitions();

    // Rebuild the menu router based on all rebuilt data.
    // Important: This rebuild must happen last, so the menu router is guaranteed
    // to be based on up to date information.
    \Drupal::service('router.builder')->rebuild();

    // Check that Schema.org preview is displayed.
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org');
  }

}
