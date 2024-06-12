<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_preview\Functional;

use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg_jsonld_preview\SchemaDotOrgJsonLdPreviewBuilderInterface;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD preview block.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdPreviewBlockTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonld_preview'];

  /**
   * Test Schema.org Schema.org JSON-LD preview block.
   */
  public function testBlock(): void {
    $assert = $this->assertSession();

    $block = $this->drupalPlaceBlock('schemadotorg_jsonld_preview');

    $account = $this->createUser(['access content', 'view schemadotorg jsonld']);

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);
    $node = $this->drupalCreateNode([
      'type' => 'thing',
      'title' => 'Something',
    ]);
    $node->save();

    // Check that JSON-LD preview is not displayed for users without permission.
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org JSON-LD');

    // Check that JSON-LD preview is not displayed without a mapping.
    $this->drupalLogin($account);
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org JSON-LD');

    // Create a Schema.org mapping for Thing.
    $mapping = SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'thing',
      'schema_type' => 'Thing',
      'schema_properties' => [
        'title' => 'name',
      ],
    ]);
    $mapping->save();

    drupal_flush_all_caches();

    // Check that JSON-LD preview is not displayed for users without permission.
    $this->drupalLogout();
    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org JSON-LD');

    // Check that JSON-LD preview is now displayed.
    $this->drupalLogin($account);
    $this->drupalGet($node->toUrl());
    $assert->responseContains('Schema.org JSON-LD');

    // Check that data table preview is now displayed.
    $settings = $block->get('settings');
    $settings['format'] = SchemaDotOrgJsonLdPreviewBuilderInterface::DATA;
    $block->set('settings', $settings);
    $block->save();

    $this->drupalGet($node->toUrl());
    $assert->responseNotContains('Schema.org JSON-LD');
    $assert->responseContains('Schema.org data');
  }

}
