<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org additional type module.
 *
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalTypeTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_ui',
    'schemadotorg_additional_type',
  ];

  /**
   * Test Schema.org additional type UI.
   */
  public function testAdditionalType(): void {
    $assert = $this->assertSession();

    /* ********************************************************************** */
    // Mapping defaults.
    // @see schemadotorg_additional_type_schemadotorg_mapping_defaults_alter()
    /* ********************************************************************** */

    // Check mapping defaults for Schema.org type that supports additional typing.
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Person',
    );
    $this->assertArrayHasKey('additionalType', $defaults['properties']);
    $this->assertEquals('', $defaults['properties']['additionalType']['name']);
    $this->assertEquals('list_string', $defaults['properties']['additionalType']['type']);
    $this->assertEquals('Type', $defaults['properties']['additionalType']['label']);
    $this->assertEquals('person_type', $defaults['properties']['additionalType']['machine_name']);
    $this->assertEquals(['Patient' => 'Patient'], $defaults['properties']['additionalType']['allowed_values']);

    // Check mapping default for Schema.org type that has additional type enabled.
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
    );
    $this->assertEquals('_add_', $defaults['properties']['additionalType']['name']);

    // Check mapping defaults for Schema.org type that has customized allowed_values.
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'WebPage',
    );
    $expected_allowed_values = [
      'AboutPage' => 'About Page',
      'ContactPage' => 'Contact Page',
      'MedicalWebPage' => 'Medical Web Page',
    ];
    $this->assertEquals($expected_allowed_values, $defaults['properties']['additionalType']['allowed_values']);

    // Check mapping defaults for existing Schema.org type just return the field name.
    $defaults = $this->mappingManager->getMappingDefaults('node', 'event', 'Event');
    $expected_type_properties = [
      'name' => '_add_',
      'type' => 'list_string',
      'label' => 'Type',
      'machine_name' => 'event_type',
      'unlimited' => FALSE,
      'required' => FALSE,
      'description' => 'An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax.',
      'allowed_values' => [
        'BusinessEvent' => 'Business Event',
        'ChildrensEvent' => 'Childrens Event',
        'ComedyEvent' => 'Comedy Event',
        'CourseInstance' => 'Course Instance',
        'DanceEvent' => 'Dance Event',
        'DeliveryEvent' => 'Delivery Event',
        'EducationEvent' => 'Education Event',
        'EventSeries' => 'Event Series',
        'ExhibitionEvent' => 'Exhibition Event',
        'Festival' => 'Festival',
        'FoodEvent' => 'Food Event',
        'Hackathon' => 'Hackathon',
        'LiteraryEvent' => 'Literary Event',
        'MusicEvent' => 'Music Event',
        'PublicationEvent' => 'Publication Event',
        'BroadcastEvent' => '- Broadcast Event',
        'OnDemandEvent' => '- On Demand Event',
        'SaleEvent' => 'Sale Event',
        'ScreeningEvent' => 'Screening Event',
        'SocialEvent' => 'Social Event',
        'SportsEvent' => 'Sports Event',
        'TheaterEvent' => 'Theater Event',
        'VisualArtsEvent' => 'Visual Arts Event',
      ],
    ];
    $this->assertEquals($expected_type_properties, $defaults['properties']['additionalType']);

    /* ********************************************************************** */
    // Schema.org mapping UI form alter.
    // @see schemadotorg_additional_type_form_schemadotorg_mapping_form_alter()
    /* ********************************************************************** */

    $this->drupalLogin($this->rootUser);

    // Check no additional type field on Schema.org type select form.
    $this->drupalGet('admin/structure/types/schemadotorg');
    $assert->responseNotContains('Enable Schema.org additional type');

    // Check that additional type field appears but is not checked by default.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Person']]);
    $assert->responseContains('Enable Schema.org additional type');
    $assert->checkboxNotChecked('mapping[properties][additionalType][field][name]');

    // Check that additional type field does appear when not supported.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Patient']]);
    $assert->responseNotContains('Enable Schema.org additional type');

    // Check that additional type field is checked by default.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Event']]);
    $assert->responseContains('Enable Schema.org additional type');
    $assert->checkboxChecked('mapping[properties][additionalType][field][name]');

    // Create the Event Schema.org type mapping.
    $this->submitForm([], 'Save');
    $assert->statusMessageContains('The content type Event has been added.');
  }

}
