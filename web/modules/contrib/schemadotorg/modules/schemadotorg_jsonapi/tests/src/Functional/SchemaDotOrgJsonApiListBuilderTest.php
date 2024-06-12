<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonapi\Functional;

use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON:API list builder enhancements.
 *
 * @covers \Drupal\schemadotorg_jsonapi\EventSubscriber\SchemaDotOrgJsonApiEventSubscriber
 * @group schemadotorg
 */
class SchemaDotOrgJsonApiListBuilderTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonapi'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);
    SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'thing',
      'schema_type' => 'Thing',
    ])->save();

    // Create Person content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'person']);
    SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'person',
      'schema_type' => 'Person',
    ])->save();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org list builder enhancements.
   */
  public function testSchemaDotOrgListBuilder(): void {
    $assert = $this->assertSession();

    $this->drupalGet('admin/config/schemadotorg/mappings');

    // Check JSON:API header.
    $assert->responseContains('<th class="priority-low" width="27%">JSON:API</th>');

    // Check link to Thing JSON:API endpoint exists.
    $assert->linkExists('/jsonapi/node/thing');

    // Check link to Person JSON:API endpoint exists.
    $assert->linkExists('/jsonapi/node/person');

  }

}
