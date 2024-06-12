<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_endpoint\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Test Schema.org JSON-LD endpoint caching.
 *
 * @covers schemadotorg_jsonld_page_attachments_alter()
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdEndpointCacheTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonld_endpoint', 'node'];

  /**
   * Tests that Schema.org JSON-lD is cached.
   */
  public function testJsonLdCache(): void {

    $assert = $this->assertSession();

    $this->appendSchemaTypeDefaultProperties('Organization', ['subOrganization']);
    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.subOrganization.type', 'field_ui:entity_reference:node')
      ->save();
    $this->createSchemaEntity('node', 'Organization');

    $organization_node_1 = $this->drupalCreateNode([
      'type' => 'organization',
      'title' => 'Organization 1',
      'body' => 'This is Organization 1.',
    ]);
    $organization_node_2 = $this->drupalCreateNode([
      'type' => 'organization',
      'title' => 'Organization 2',
      'body' => 'This is Organization 2.',
      'schema_sub_organization' => ['target_id' => $organization_node_1->id()],
    ]);

    /* ********************************************************************** */

    // Check that organization 1's page first request is a miss and then a hit.
    $this->drupalGet('jsonld/content/' . $organization_node_1->uuid());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'MISS');
    $this->drupalGet('jsonld/content/' . $organization_node_1->uuid());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'HIT');

    // Check that organization 1's cache context and tags.
    $assert->responseHeaderContains('X-Drupal-Cache-Tags', 'config:schemadotorg_mapping_list config:user.role.anonymous http_response node:1');
    $assert->responseHeaderContains('X-Drupal-Cache-Contexts', 'route url.site user.permissions',);

    // Check that organization 2's page first request is a miss and then a hit.
    $this->drupalGet('jsonld/content/' . $organization_node_2->uuid());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'MISS');
    $this->drupalGet('jsonld/content/' . $organization_node_2->uuid());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'HIT');

    // Check that organization 2's cache context and tags.
    $assert->responseHeaderContains('X-Drupal-Cache-Tags', 'config:schemadotorg_mapping_list config:user.role.anonymous http_response node:1 node:2');
    $assert->responseHeaderContains('X-Drupal-Cache-Contexts', 'route url.site user.permissions',);

    // Check that organization 2's JSON-LD contains 'Organization 1' label.
    $assert->responseContains('Organization 1');

    // Update organization 1's title.
    $organization_node_1->setTitle('Organization I')->save();

    // Check that organization 2's page first request is a miss and then a hit.
    $this->drupalGet('jsonld/content/' . $organization_node_2->uuid());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'MISS');
    $this->drupalGet('jsonld/content/' . $organization_node_2->uuid());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'HIT');

    // Check that organization 2's JSON-LD contains 'Organization I' label.
    $assert->responseContains('Organization I');
  }

}
