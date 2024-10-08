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
    $this->assertEquals(['patient' => 'Patient'], $defaults['properties']['additionalType']['allowed_values']);

    $this->config('schemadotorg_additional_type.settings')
      ->set('use_snake_case', FALSE)
      ->save();
    $defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Person',
    );
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
      'about_page' => 'About Page',
      'contact_page' => 'Contact Page',
      'medical_web_page' => 'Medical Web Page',
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
      'required' => TRUE,
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

    /* ********************************************************************** */
    // Enabling additional type property/field.
    /* ********************************************************************** */

    // Check creating a 'Person' with additional type enabled.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Person']]);
    $edit = [
      'mapping[properties][additionalType][field][name]' => TRUE,
    ];
    $this->submitForm($edit, 'Save');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->mappingStorage->load('node.person');
    $this->assertEquals('schema_person_type', $mapping->getSchemaPropertyFieldName('additionalType'));

    // Check creating another 'Person' with additional type enabled.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Person']]);
    $edit = [
      'mapping[entity][id]' => 'another_person',
      'mapping[properties][additionalType][field][name]' => TRUE,
    ];
    $this->submitForm($edit, 'Save');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->mappingStorage->load('node.another_person');
    $this->assertEquals('schema_person_type', $mapping->getSchemaPropertyFieldName('additionalType'));

    // Check creating other 'Person' with other additional type enabled.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Person']]);
    $edit = [
      'mapping[entity][id]' => 'other_person',
      'mapping[properties][additionalType][field][name]' => TRUE,
      'mapping[properties][additionalType][field][_add_][machine_name]' => 'other_person_type',
    ];
    $this->submitForm($edit, 'Save');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->mappingStorage->load('node.other_person');
    $this->assertEquals('schema_other_person_type', $mapping->getSchemaPropertyFieldName('additionalType'));

    /* ********************************************************************** */
    // Node add and edit form alteration.
    // @see \Drupal\schemadotorg_additional_type\Controller\SchemaDotOrgAdditionalTypeHtmlEntityFormController
    /* ********************************************************************** */

    \Drupal::configFactory()
      ->getEditable('schemadotorg.settings')
      ->set('schema_types.default_properties.Event', ['name'])
      ->save();
    $this->createSchemaEntity('node', 'Event');

    $this->drupalLogin($this->rootUser);

    // Check that create node displays the additional type selection page.
    $this->drupalGet('/node/add/event');
    $assert->responseContains('<h1>Please select the <em class="placeholder">Event</em> type you want to create.</h1>');
    $assert->linkExists('Business Event');
    $assert->linkByHrefExists('/node/add/event?schema_event_type=BusinessEvent');
    $assert->fieldNotExists('schema_event_type');

    // Check that create node displays the form when additional type is defined.
    $this->drupalGet('/node/add/event', ['query' => ['schema_event_type' => 'BusinessEvent']]);
    $assert->responseNotContains('<h1>Please select the <em class="placeholder">Event</em> type you want to create.</h1>');
    $assert->linkNotExists('Business Event');
    $assert->linkByHrefNotExists('/node/add/event?schema_event_type=BusinessEvent');
    $assert->fieldNotExists('schema_event_type');
    $assert->linkExists('Change type');
    $assert->linkByHrefExists('/node/add/event?schema_event_type=');

    // Create a 'Business Event' node.
    $event_node = $this->drupalCreateNode([
      'type' => 'event',
      'schema_event_type' => 'BusinessEvent',
    ]);

    // Check that the edit node form is altered to hide the additional type field
    // and display it value.
    $this->drupalGet($event_node->toUrl('edit-form'));
    $assert->fieldNotExists('schema_event_type');
    $assert->responseContains('<label>Type</label>');
    $assert->responseContains('Business Event');

    // Check that 'Change type' link exists on the edit node form.
    $assert->linkExists('Change type');
    $assert->linkByHrefExists($event_node->toUrl('edit-form')->toString() . '?schema_event_type=');

    // Check that 'Change type' link load the additional type selection page.
    $this->drupalGet($event_node->toUrl('edit-form'), ['query' => ['schema_event_type' => '']]);
    $assert->fieldNotExists('schema_event_type');
    $assert->linkNotExists('Change type');
    $assert->responseContains('<h1>Please select the <em class="placeholder">Event</em> type you want to change to.</h1>');
    $assert->linkExists('Childrens Event');
    $assert->linkByHrefExists($event_node->toUrl('edit-form')->toString() . '?schema_event_type=ChildrensEvent');

    // Check that the edit node form displays the query parameter as the value.
    $this->drupalGet($event_node->toUrl('edit-form'), ['query' => ['schema_event_type' => 'ChildrensEvent']]);
    $assert->fieldNotExists('schema_event_type');
    $assert->responseContains('<label>Type</label>');
    $assert->responseNotContains('Business Event');
    $assert->responseContains('Childrens Event');
  }

}
