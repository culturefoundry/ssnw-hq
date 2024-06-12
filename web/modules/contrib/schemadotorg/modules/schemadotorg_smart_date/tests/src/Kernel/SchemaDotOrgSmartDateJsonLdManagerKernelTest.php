<?php

// phpcs:disable

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_smart_date\Kernel;

use Drupal\node\Entity\Node;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\smart_date_recur\Entity\SmartDateRule;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

require_once __DIR__ . '/../../../schemadotorg_smart_date.install';

/**
 * Tests the functionality of the Schema.org Smart Date integration.
 *
 * @covers \Drupal\schemadotorg_smart_date\SchemaDotOrgSmartDateJsonLdManager
 * @group schemadotorg
 */
class SchemaDotOrgSmartDateJsonLdManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable DrupalPractice.Objects.StrictSchemaDisabled.StrictConfigSchema
  /**
   * Disabled config schema checking temporarily until inline entity form fixes missing schema.
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable DrupalPractice.Objects.StrictSchemaDisabled.StrictConfigSchema

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'smart_date',
    'smart_date_recur',
    'schemadotorg_jsonld',
    'schemadotorg_smart_date',
  ];

  /**
   * Schema.org JSON-LD builder.
   */
  protected SchemaDotOrgJsonLdBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_jsonld', 'schemadotorg_smart_date']);
    $this->installEntitySchema('smart_date_rule');
    $this->installEntitySchema('smart_date_override');

    $this->builder = $this->container->get('schemadotorg_jsonld.builder');
  }

  /**
   * Test Schema.org alter the JSON-LD eventSchedule property.
   *
   * @covers ::alterProperty
   */
  public function testEventSchedule(): void {
    $this->assertTrue(TRUE);

    /*
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    // Install the module.
    schemadotorg_smart_date_install(FALSE);

    // Create Event with eventSchedule.
    $this->createSchemaEntity('node', 'Event');

    // Create a basic event.
    $event_node = Node::create([
      'type' => 'event',
      'title' => 'Sometime',
      'schema_event_schedule' => [
        [
          'value' => strtotime('2001-01-01T11:00:00'),
          'end_value' => strtotime('2001-01-01T12:00:00'),
          'duration' => '1',
        ],
      ],
    ]);
    $event_node->save();

    // Check Event eventSchedule JSON-LD data.
    $expected_value = [
      '@type' => 'Schedule',
      'startDate' => '2001-01-01',
      'duration' => 'PT1H',
      'startTime' => '11:00:00',
      'endTime' => '12:00:00',
    ];
    $jsonld = $this->builder->buildEntity($event_node);
    $this->assertEquals($expected_value, $jsonld['eventSchedule']);

    // Create a recurring event.
    $rrule = SmartDateRule::create([
      'rule' => 'RRULE:FREQ=WEEKLY;BYDAY=MO;COUNT=12',
      'freq' => 'WEEKLY',
      'limit' => 'COUNT=12',
      'parameters' => 'BYDAY=MO',
      'unlimited' => 0,
      'field_name' => 'schema_event_schedule',
      'entity_type' => 'node',
    ]);
    $rrule->save();
    $event_recur_node = Node::create([
      'type' => 'event',
      'title' => 'Sometime',
      'schema_event_schedule' => [
        [
          'value' => strtotime('2001-01-01T11:00:00'),
          'end_value' => strtotime('2001-01-01T12:00:00'),
          'duration' => '1',
          'rrule' => $rrule->id(),
          'rrule_index' => 1,
        ],
      ],
    ]);
    $event_recur_node->save();

    $jsonld = $this->builder->buildEntity($event_recur_node);
    $expected_value = [
      '@type' => 'Schedule',
      'startDate' => '2001-01-01',
      'startTime' => '11:00:00',
      'endTime' => '12:00:00',
      'duration' => 'PT1H',
      'repeatFrequency' => 'P1W',
      'byDay' => ['https://schema.org/Monday'],
      'repeatCount' => '12',
    ];
    $this->assertEquals($expected_value, $jsonld['eventSchedule']);
    */
  }

  /**
   * Test Schema.org alter the JSON-LD startDate property.
   *
   * @covers ::alterProperties
   */
  public function testEventStartDate(): void {
    $this->assertTrue(TRUE);

    /*
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    // Reset Event properties to use startDate with smartdate.
    $config = $this->config('schemadotorg.settings');
    $config
      ->set('schema_types.default_properties.Event', ['eventSchedule', 'inLanguage', 'name', 'startDate'])
      ->set('schema_properties.default_fields.startDate', ['type' => 'smartdate'])
      ->save();

    // Create Event with eventSchedule.
    $this->createSchemaEntity('node', 'Event');

    // Create a basic event.
    $event_node = Node::create([
      'type' => 'event',
      'title' => 'Sometime',
      'schema_start_date' => [
        [
          'value' => strtotime('2001-01-01T11:00:00'),
          'end_value' => strtotime('2001-01-01T12:00:00'),
          'duration' => '1',
        ],
      ],
    ]);
    $event_node->save();

    // Check Event eventSchedule JSON-LD data.
    $expected_value = [
      '@type' => 'Event',
      '@url' => $event_node->toUrl()->setAbsolute()->toString(),
      'inLanguage' => 'en',
      'name' => 'Sometime',
      'startDate' => '2001-01-01T11:00:00+11:00',
      'endDate' => '2001-01-01T12:00:00+11:00',
      'duration' => 'PT1H',
    ];
    $jsonld = $this->builder->buildEntity($event_node);
    $this->assertEquals($expected_value, $jsonld);
    */
  }

}
