<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_smart_date\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

require_once __DIR__ . '/../../../schemadotorg_smart_date.install';

/**
 * Tests the functionality of the Schema.org Smart Date install/uninstall.
 *
 * @group schemadotorg
 */
class SchemaDotOrgSmartDateInstallTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'schemadotorg',
    'schemadotorg_smart_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg']);
  }

  /**
   * Test Schema.org Smart Date install/uninstall hooks.
   */
  public function testInstallAndUninstall(): void {
    $config = $this->config('schemadotorg.settings');

    // Check performing set up tasks when the Schema.org Smart Date module is installed.
    schemadotorg_smart_date_install(FALSE);

    $event_properties = $config->get('schema_types.default_properties.Event');
    $this->assertTrue(in_array('eventSchedule', $event_properties));
    $this->assertFalse(in_array('startDate', $event_properties));
    $this->assertFalse(in_array('endDate', $event_properties));

    $event_schedule = $config->get('schema_properties.default_fields.eventSchedule');
    $this->assertEquals(['type' => 'smartdate', 'unlimited' => TRUE], $event_schedule);

    // Check removing any information that the Schema.org Smart Date module sets.
    schemadotorg_smart_date_uninstall(FALSE);

    $event_properties = $config->get('schema_types.default_properties.Event');
    $this->assertFalse(in_array('eventSchedule', $event_properties));
    $this->assertTrue(in_array('startDate', $event_properties));
    $this->assertTrue(in_array('endDate', $event_properties));

    $event_schedule = $config->get('schema_properties.default_fields.eventSchedule');
    $this->assertEquals(['type' => 'daterange', 'unlimited' => TRUE], $event_schedule);
  }

}
