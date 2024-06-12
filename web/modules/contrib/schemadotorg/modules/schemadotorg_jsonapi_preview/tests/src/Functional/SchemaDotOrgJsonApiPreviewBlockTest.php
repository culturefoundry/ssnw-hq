<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonapi_preview\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON:API preview block.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonApiPreviewBlockTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonapi', 'schemadotorg_jsonapi_preview'];

  /**
   * Test Schema.org Schema.org JSON:API preview block.
   */
  public function testBlock(): void {
    $assert = $this->assertSession();

    $this->drupalPlaceBlock('schemadotorg_jsonapi_preview');

    $account = $this->createUser(['access content', 'view schemadotorg jsonapi']);

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);

    // @todo Determine why JSON:API preview requires a cache clear.
    drupal_flush_all_caches();

    $node = $this->drupalCreateNode([
      'type' => 'thing',
      'title' => 'Something',
    ]);
    $node->save();

    // Check that JSON:API preview is not displayed for users without permission.
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org JSON:API');

    // Clear all plugin caches.
    /** @var \Drupal\module_test\PluginManagerCacheClearer $plugin_cache_clearer */
    $plugin_cache_clearer = \Drupal::service('plugin.cache_clearer');
    $plugin_cache_clearer->clearCachedDefinitions();

    // Check that JSON:API preview is not displayed.
    $this->drupalLogin($account);
    $this->drupalGet($node->toUrl());
    $assert->responseContains('Schema.org JSON:API');
  }

}
