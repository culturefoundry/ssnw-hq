<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_physical\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org Physical module.
 *
 * @covers schemadotorg_physical_install()
 * @covers schemadotorg_physical_uninstall()
 * @group schemadotorg
 */
class SchemaDotOrgPhysicalKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'physical',
    'schemadotorg_physical',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_physical']);
  }

  /**
   * Test Schema.org Blueprints Physical installation.
   */
  public function testInstall(): void {
    \Drupal::moduleHandler()->loadInclude('schemadotorg_physical', 'install');

    $config = \Drupal::config('schemadotorg.settings');

    // Check IndividualProduct's default properties.
    $this->assertEquals(NULL, \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.IndividualProduct'));

    // Check QuantitativeValue default field types is 'number'.
    $default_field_types = [
      'integer',
      'decimal',
      'float',
      'range_integer',
      'range_decimal',
      'range_float',
    ];
    $this->assertEquals($default_field_types, $config->get('schema_types.default_field_types.QuantitativeValue'));

    // Check some properties initial default field types.
    $this->assertEquals(NULL, $config->get('schema_properties.default_fields.depth.type'));
    $this->assertEquals('string', $config->get('schema_properties.default_fields.height.type'));
    $this->assertEquals(NULL, $config->get('schema_properties.default_fields.weight.type'));
    $this->assertEquals('string', $config->get('schema_properties.default_fields.width.type'));

    // Install the Schema.org Blueprints Physical module.
    schemadotorg_physical_install(FALSE);

    // Check adding width, height and weight to IndividualProduct's default properties.
    $new_default_properties = [
      'depth',
      'height',
      'width',
    ];
    $this->assertEquals($new_default_properties, \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.IndividualProduct'));

    // Check QuantitativeValue default field types has now also Physical field types.
    $new_default_field_types = [
      'physical_dimensions',
      'physical_measurement',
      'integer',
      'decimal',
      'float',
      'range_integer',
      'range_decimal',
      'range_float',
    ];
    $this->assertEquals($new_default_field_types, $config->get('schema_types.default_field_types.QuantitativeValue'));

    // Check some properties altered default field types.
    $this->assertEquals('field_ui:physical_measurement:length', $config->get('schema_properties.default_fields.depth.type'));
    $this->assertEquals('field_ui:physical_measurement:length', $config->get('schema_properties.default_fields.height.type'));
    $this->assertEquals('field_ui:physical_measurement:weight', $config->get('schema_properties.default_fields.weight.type'));
    $this->assertEquals('field_ui:physical_measurement:length', $config->get('schema_properties.default_fields.width.type'));

    // Check length field used for Distance.
    $this->assertEquals(['field_ui:physical_measurement:length'], $config->get('schema_types.default_field_types.Distance'));

    // Uninstall the Schema.org Blueprints Physical module.
    schemadotorg_physical_uninstall(FALSE);

    // Check QuantitativeValue default field types is 'string_long'.
    $this->assertEquals($default_field_types, $config->get('schema_types.default_field_types.QuantitativeValue'));

    // Check some properties initial default field types.
    $this->assertEquals('string', $config->get('schema_properties.default_fields.height.type'));
    $this->assertEquals(NULL, $config->get('schema_properties.default_fields.weight.type'));
    $this->assertEquals('string', $config->get('schema_properties.default_fields.width.type'));

    // Check removing width, height and weight to IndividualProduct's default properties.
    $this->assertEquals([], \Drupal::config('schemadotorg.settings')->get('schema_types.default_properties.IndividualProduct'));

    // Check remove length field used for Distance.
    $this->assertEquals(NULL, $config->get('schema_types.default_field_types.Distance'));

  }

}
