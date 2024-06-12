<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_office_hours\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

require_once __DIR__ . '/../../../schemadotorg_office_hours.install';

/**
 * Tests the functionality of the Schema.org Office Hours install/uninstall.
 *
 * @covers schemadotorg_office_hours_install()
 * @covers schemadotorg_office_hours_uninstall()
 * @group schemadotorg
 */
class SchemaDotOrgOfficeHoursInstallTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'schemadotorg',
    'schemadotorg_office_hours',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg']);
  }

  /**
   * Test Schema.org Office Hours install/uninstall hooks.
   */
  public function testInstallAndUninstall(): void {
    $this->assertNull(\Drupal::config('schemadotorg.settings')->get('schema_types.default_field_types.OpeningHoursSpecification'));
    $this->assertEquals(
      ['address', 'description', 'image', 'latitude', 'longitude', 'name', 'telephone'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.Place')
    );

    schemadotorg_office_hours_install(FALSE);

    // Check adding office_hours field to OpeningHoursSpecification field types.
    $this->assertEquals(
      ['office_hours'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_field_types.OpeningHoursSpecification')
    );

    // Check adding openingHoursSpecification to Place's default properties.
    $this->assertEquals(
      ['address', 'description', 'image', 'latitude', 'longitude', 'name', 'openingHoursSpecification', 'telephone'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.Place')
    );

    // Check switching from openingHours to openingHoursSpecification.
    $this->assertContains(
      'openingHoursSpecification',
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.LocalBusiness')
    );

    schemadotorg_office_hours_uninstall(FALSE);

    // Check removing openingHoursSpecification from Place's default properties.
    $this->assertNull(\Drupal::config('schemadotorg.settings')->get('schema_types.default_field_types.OpeningHoursSpecification'));
    $this->assertEquals(
      ['address', 'description', 'image', 'latitude', 'longitude', 'name', 'telephone'],
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.Place')
    );

    // Check switching back from openingHoursSpecification to openingHours.
    $this->assertContains(
      'openingHours',
      \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.LocalBusiness')
    );
  }

}
