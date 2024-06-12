<?php

declare(strict_types=1);

namespace Drupal\Tests\SchemaDotOrgJsonLdEndpoint\Functional;

use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD endpoints.
 *
 * @covers \Drupal\schemadotorg_jsonld_endpoint\Controller\SchemaDotOrgJsonLdEndpointController
 * @covers \Drupal\schemadotorg_jsonld_endpoint\Routing\SchemaDotOrgJsonLdEndpointRoutes
 * @covers \Drupal\schemadotorg_jsonld_endpoint\ParamConverter\SchemaDotOrgJsonLdEndpointEntityUuidConverter
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdEndpointTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_jsonapi',
    'schemadotorg_jsonld_endpoint',
  ];

  /**
   * Test Schema.org JSON-LD endpoints.
   */
  public function testEndpoints(): void {
    $assert = $this->assertSession();

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);
    $node = $this->drupalCreateNode([
      'type' => 'thing',
      'title' => 'Something',
    ]);
    $node->save();

    // Create a Schema.org mapping for Thing.
    SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'thing',
      'schema_type' => 'Thing',
      'schema_properties' => [
        'title' => 'name',
      ],
    ])->save();

    // Check access allowed to node's JSON-LD via /jsonld/content/{uuid}.
    $this->drupalGet('jsonld/content/' . $node->uuid());
    $assert->statusCodeEquals(200);

    // Check 404 when using a node's ID.
    $this->drupalGet('jsonld/content/' . $node->id());
    $assert->statusCodeEquals(404);

    $node->setUnpublished()->save();
    $this->drupalGet('jsonld/content/' . $node->uuid());
    $assert->statusCodeEquals(403);
  }

}
