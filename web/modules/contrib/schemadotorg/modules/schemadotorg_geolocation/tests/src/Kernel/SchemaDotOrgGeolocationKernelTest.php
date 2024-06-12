<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_geolocation\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

require_once __DIR__ . '/../../../schemadotorg_geolocation.install';

/**
 * Tests the functionality of the Schema.org Geolocation.
 *
 * @covers schemadotorg_geolocation_schemadotorg_property_field_alter()
 * @group schemadotorg
 */
class SchemaDotOrgGeolocationKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'address',
    'geolocation',
    'geolocation_address',
    'geolocation_leaflet',
    'schemadotorg_geolocation',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(static::$modules);

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');

    schemadotorg_geolocation_install(FALSE);
  }

  /**
   * Test Schema.org geolocation configuration.
   */
  public function testGeolocation(): void {
    // Create Place with geo property.
    $this->createSchemaEntity('node', 'Place');

    // Check geolocation form display component.
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'place');
    $form_component = $form_display->getComponent('schema_geo');
    $this->assertEquals('geolocation_leaflet', $form_component['type']);
    $expected_values = [
      'enable' => TRUE,
      'address_field' => 'schema_address',
      'geocoder' => 'photon',
      'sync_mode' => 'manual',
      'direction' => 'one_way',
      'button_position' => 'topleft',
      'ignore' => [
        'organization' => TRUE,
        'address-line1' => FALSE,
        'address-line2' => FALSE,
        'locality' => FALSE,
        'administrative-area' => FALSE,
        'postal-code' => FALSE,
      ],
    ];
    $this->assertEquals($expected_values, $form_component['third_party_settings']['geolocation_address']);

    // Check geolocation view display component.
    $view_display = $this->entityDisplayRepository->getViewDisplay('node', 'place');
    $view_component = $view_display->getComponent('schema_geo');
    $this->assertEquals('geolocation_map', $view_component['type']);
    $this->assertEquals('leaflet', $view_component['settings']['map_provider_id']);
    $this->assertEquals('15', $view_component['settings']['map_provider_settings']['zoom']);
  }

}
