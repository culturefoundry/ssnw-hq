<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Test Schema.org JSON-LD caching.
 *
 * @covers schemadotorg_jsonld_page_attachments_alter()
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdCacheTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_jsonld_preview', 'node'];

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
    $organization_node_1_uri = $organization_node_1->toUrl()->setAbsolute()->toString();
    $organization_node_2 = $this->drupalCreateNode([
      'type' => 'organization',
      'title' => 'Organization 2',
      'body' => 'This is Organization 2.',
      'schema_sub_organization' => ['target_id' => $organization_node_1->id()],
    ]);
    $organization_node_2_uri = $organization_node_2->toUrl()->setAbsolute()->toString();

    /* ********************************************************************** */

    // Check that organization 1's page first request is a miss and then a hit.
    $this->drupalGet($organization_node_1->toUrl());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'MISS');
    $this->drupalGet($organization_node_1->toUrl());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'HIT');

    // Check that organization 1's cache context and tags.
    $assert->responseHeaderContains('X-Drupal-Cache-Tags', 'config:filter.format.plain_text config:filter.settings config:schemadotorg_jsonld.settings config:schemadotorg_mapping_list config:user.role.anonymous http_response node:1 node_view rendered user:0 user_view');
    $assert->responseHeaderContains('X-Drupal-Cache-Contexts', 'languages:language_interface theme timezone url.query_args:_wrapper_format url.site user.permissions user.roles:authenticated');

    // Check that organization 2's page first request is a miss and then a hit.
    $this->drupalGet($organization_node_2->toUrl());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'MISS');
    $this->drupalGet($organization_node_2->toUrl());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'HIT');

    // Check that organization 2's cache context and tags.
    $assert->responseHeaderContains('X-Drupal-Cache-Tags', 'config:filter.format.plain_text config:filter.settings config:schemadotorg_jsonld.settings config:schemadotorg_mapping_list config:user.role.anonymous http_response node:1 node:2 node_view rendered user:0 user_view');
    $assert->responseHeaderContains('X-Drupal-Cache-Contexts', 'languages:language_interface theme timezone url.query_args:_wrapper_format url.site user.permissions user.roles:authenticated');

    // Check that organization 2's JSON-LD.
    $assert->responseContains('<script type="application/ld+json">{
    "@context": "https://schema.org",
    "@type": "Organization",
    "@url": "' . $organization_node_2_uri . '",
    "name": "Organization 2",
    "description": "\u003Cp\u003EThis is Organization 2.\u003C/p\u003E\n",
    "subOrganization": [
        {
            "@type": "Organization",
            "name": "Organization 1",
            "@url": "' . $organization_node_1_uri . '"
        }
    ]
}</script>');

    // Update organization 1's title.
    $organization_node_1->setTitle('Organization I')->save();

    // Check that organization 2's page first request is a miss and then a hit.
    $this->drupalGet($organization_node_2->toUrl());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'MISS');
    $this->drupalGet($organization_node_2->toUrl());
    $assert->responseHeaderEquals('X-Drupal-Cache', 'HIT');

    // Check that organization 2's JSON-LD.
    $assert->responseContains('<script type="application/ld+json">{
    "@context": "https://schema.org",
    "@type": "Organization",
    "@url": "' . $organization_node_2_uri . '",
    "name": "Organization 2",
    "description": "\u003Cp\u003EThis is Organization 2.\u003C/p\u003E\n",
    "subOrganization": [
        {
            "@type": "Organization",
            "name": "Organization I",
            "@url": "' . $organization_node_1_uri . '"
        }
    ]
}</script>');
  }

}
