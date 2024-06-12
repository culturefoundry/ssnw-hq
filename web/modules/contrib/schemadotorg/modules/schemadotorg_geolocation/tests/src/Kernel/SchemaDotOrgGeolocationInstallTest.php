<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_geolocation\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

require_once __DIR__ . '/../../../schemadotorg_geolocation.install';

/**
 * Tests the functionality of the Schema.org Geolocation install/uninstall.
 *
 * @covers schemadotorg_geolocation_install()
 * @covers schemadotorg_geolocation_uninstall()
 * @group schemadotorg
 */
class SchemaDotOrgGeolocationInstallTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'schemadotorg',
    'schemadotorg_geolocation',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg']);
  }

  /**
   * Test Schema.org geolocation install/uninstall hooks.
   */
  public function testInstallAndUninstall(): void {
    // Check that GeoCoordinate and geo property are not being defined.
    $this->assertNull(\Drupal::config('schemadotorg.settings')->get('schema_types.default_field_types.Geocoordinates'));
    $this->assertEquals(
      ['address', 'description', 'image', 'latitude', 'longitude', 'name', 'telephone'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.Place')
    );

    schemadotorg_geolocation_install(FALSE);

    // Check adding geolocation field to GeoCoordinates field types.
    $this->assertEquals(
      ['geolocation'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_field_types.GeoCoordinates')
    );

    // Check adding geo to Place's default properties.
    $this->assertEquals(
      ['address', 'description', 'geo', 'image', 'name', 'telephone'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.Place')
    );

    schemadotorg_geolocation_uninstall(FALSE);

    // Check removing GeoCoordinates from field types.
    $this->assertNull(\Drupal::config('schemadotorg.settings')->get('schema_types.default_field_types.GeoCoordinates'));

    // Check removing geo from Place's default properties.
    $this->assertEquals(
      ['address', 'description', 'image', 'latitude', 'longitude', 'name', 'telephone'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.Place')
    );
  }

}
