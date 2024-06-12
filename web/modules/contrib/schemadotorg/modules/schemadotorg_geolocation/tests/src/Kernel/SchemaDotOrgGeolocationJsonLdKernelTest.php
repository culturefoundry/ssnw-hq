<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_geolocation\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

require_once __DIR__ . '/../../../schemadotorg_geolocation.install';

/**
 * Tests the functionality of the Schema.org Geolocation JSON-LD integration.
 *
 * @covers schemadotorg_geolocation_schemadotorg_jsonld_schema_property_alter()
 * @group schemadotorg
 */
class SchemaDotOrgGeolocationJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'geolocation',
    'schemadotorg_geolocation',
  ];

  /**
   * Test Schema.org alter the JSON-LD geo property.
   */
  public function testGeo(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    schemadotorg_geolocation_install(FALSE);

    // Create Place with geo property.
    $this->createSchemaEntity('node', 'Place');

    // Create a place.
    $place_node = Node::create([
      'type' => 'place',
      'title' => 'Somewhere',
      'schema_geo' => [['lat' => 51.47879, 'lng' => -0.010677]],
    ]);
    $place_node->save();

    // Check Place geo JSON-LD value.
    $expected_value = [
      '@type' => 'GeoCoordinates',
      'latitude' => '51.47879',
      'longitude' => '-0.010677',
    ];
    $jsonld = $this->builder->buildEntity($place_node);
    $this->assertEquals($expected_value, $jsonld['geo']);
  }

}
