<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_options\Kernel;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the Schema.org options.
 *
 * @group schemadotorg
 */
class SchemaDotOrgOptionsKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_options',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(static::$modules);
  }

  /**
   * Test Schema.org options.
   */
  public function testOptions(): void {
    $this->appendSchemaTypeDefaultProperties('Person', ['gender']);
    $this->createSchemaEntity('node', 'Person');
    $this->createSchemaEntity('node', 'Recipe');
    $this->createSchemaEntity('node', 'MedicalStudy');

    // Check that gender is assigned custom allowed values..
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = FieldStorageConfig::load('node.schema_gender');
    $expected_allowed_values = [
      'Male' => 'Male',
      'Female' => 'Female',
      'Unspecified' => 'Unspecified',
    ];
    $this->assertEquals($expected_allowed_values, $field_storage->getSetting('allowed_values'));

    // Check that knowsLanguage is assigned an allowed values function.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = FieldStorageConfig::load('node.schema_knows_language');
    $this->assertEquals('schemadotorg_options_allowed_values_language', $field_storage->getSetting('allowed_values_function'));

    // Check that suitableForDiet is assigned an allowed values function.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = FieldStorageConfig::load('node.schema_suitable_for_diet');
    $expected_allowed_values = [
      'DiabeticDiet' => 'Diabetic',
      'GlutenFreeDiet' => 'Gluten Free',
      'HalalDiet' => 'Halal',
      'HinduDiet' => 'Hindu',
      'KosherDiet' => 'Kosher',
      'LowCalorieDiet' => 'Low Calorie',
      'LowFatDiet' => 'Low Fat',
      'LowLactoseDiet' => 'Low Lactose',
      'LowSaltDiet' => 'Low Salt',
      'VeganDiet' => 'Vegan',
      'VegetarianDiet' => 'Vegetarian',
    ];
    $this->assertEquals($expected_allowed_values, $field_storage->getSetting('allowed_values'));

    // Check that status allowed values use OptGroup for multiple enumerations..
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = FieldStorageConfig::load('node.schema_status');
    $expected_allowed_values = [
      'EventCancelled' => 'Event Cancelled',
      'EventMovedOnline' => 'Event Moved Online',
      'EventPostponed' => 'Event Postponed',
      'EventRescheduled' => 'Event Rescheduled',
      'EventScheduled' => 'Event Scheduled',
      'ActiveNotRecruiting' => 'Active not Recruiting',
      'Completed' => 'Completed',
      'EnrollingByInvitation' => 'Enrolling by Invitation',
      'NotYetRecruiting' => 'Not Yet Recruiting',
      'Recruiting' => 'Recruiting',
      'ResultsAvailable' => 'Results Available',
      'ResultsNotAvailable' => 'Results not Available',
      'Suspended' => 'Suspended',
      'Terminated' => 'Terminated',
      'Withdrawn' => 'Withdrawn',
    ];
    $this->assertEquals($expected_allowed_values, $field_storage->getSetting('allowed_values'));

    /* ********************************************************************** */
    // Check hook_schemadotorg_property_field_type_alter().
    /* ********************************************************************** */

    // Check default field type for Schema.org properties with allowed values.
    $field_types = ['string' => 'string'];
    schemadotorg_options_schemadotorg_property_field_type_alter($field_types, 'Person', 'gender');
    $this->assertEquals(['list_string' => 'list_string', 'string' => 'string'], $field_types);

    // Check that the property's field type if a default field type is defined.
    $field_types = ['string' => 'string'];
    schemadotorg_options_schemadotorg_property_field_type_alter($field_types, 'SpecialAnnouncement', 'category');
    $this->assertEquals(['list_string' => 'list_string', 'string' => 'string'], $field_types);

    // Check that the property's field type if a default field type is defined.
    $field_types = ['string' => 'string'];
    schemadotorg_options_schemadotorg_property_field_type_alter($field_types, 'Recommendation', 'category');
    $this->assertEquals(['list_string' => 'list_string', 'string' => 'string'], $field_types);

    // Check settings default field type to list string for
    // allowed values function.
    $field_types = ['string' => 'string'];
    schemadotorg_options_schemadotorg_property_field_type_alter($field_types, 'Person', 'knowsLanguage');
    $this->assertEquals(['list_string' => 'list_string', 'string' => 'string'], $field_types);
  }

}
