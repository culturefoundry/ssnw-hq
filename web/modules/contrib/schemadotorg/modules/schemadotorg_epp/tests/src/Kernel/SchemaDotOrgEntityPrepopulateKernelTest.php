<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_epp\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\Node;
use Drupal\schemadotorg_epp\SchemaDotOrgEppManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org Entity Prepopulate.
 *
 * @group schemadotorg
 */
class SchemaDotOrgEntityPrepopulateKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the epp.module has fixed its schema.
   *
   * @see https://www.drupal.org/project/epp/issues/3348759
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'epp',
    'schemadotorg_epp',
  ];

  /**
   * The Schema.org Entity Prepopulate manager.
   */
  protected SchemaDotOrgEppManagerInterface $schemaEppManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installConfig(self::$modules);

    $this->schemaEppManager = $this->container->get('schemadotorg_epp.manager');
  }

  /**
   * Test Schema.org entity prepopulate.
   */
  public function testEntityPrepopulate(): void {
    global $base_path;

    /* ********************************************************************** */
    // Organization.
    /* ********************************************************************** */

    $this->appendSchemaTypeDefaultProperties('Organization', ['subOrganization', 'parentOrganization', 'subjectOf']);

    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.memberOf.type', 'field_ui:entity_reference:node')
      ->set('schema_properties.default_fields.worksFor.type', 'field_ui:entity_reference:node')
      ->set('schema_properties.default_fields.parentOrganization.type', 'field_ui:entity_reference:node')
      ->set('schema_properties.default_fields.subOrganization.type', 'field_ui:entity_reference:node')
      ->set('schema_properties.default_fields.subjectOf.type', 'field_ui:entity_reference:node')
      ->save();

    $this->createSchemaEntity('node', 'Person');
    $this->createSchemaEntity('node', 'Organization');

    /* ********************************************************************** */

    $fields = [
      'node.person.schema_member_of' => 'target_id: [current-page:query:member_of]',
      'node.person.schema_works_for' => 'target_id: [current-page:query:works_for]',
      'node.organization.schema_parent_organization' => 'target_id: [current-page:query:parent_organization]',
      'node.organization.schema_sub_organization' => 'target_id: [current-page:query:sub_organization]',
      'node.organization.schema_subject_of' => 'target_id: [current-page:query:subject_of]',
    ];
    foreach ($fields as $id => $value) {
      $this->assertEquals($value, FieldConfig::load($id)->getThirdPartySetting('epp', 'value'));
    }

    // Create an organization node.
    $node = Node::create([
      'title' => 'Organization',
      'type' => 'organization',
    ]);
    $node->save();

    // Check that the organization has nodes links with entity prepopulate parameters.
    $node_links = $this->schemaEppManager->getNodeLinks($node);
    $this->convertArrayValuesToStrings($node_links);
    $expected_node_links = [
      'person--member_of' => [
        'title' => 'Add Person',
        'url' => $base_path . 'node/add/person?member_of=1',
      ],
      'organization--parent_organization' => [
        'title' => 'Add Organization',
        'url' => $base_path . 'node/add/organization?parent_organization=1',
      ],
    ];
    $this->assertEquals($expected_node_links, $node_links);

    /* ********************************************************************** */
    // Hotel.
    /* ********************************************************************** */

    $this->appendSchemaTypeDefaultProperties('Hotel', ['containsPlace']);
    $this->appendSchemaTypeDefaultProperties('HotelRoom', 'containedInPlace');

    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.containsPlace.type', 'field_ui:entity_reference:node')
      ->set('schema_properties.default_fields.containedInPlace.type', 'field_ui:entity_reference:node')
      ->save();

    $this->createSchemaEntity('node', 'HotelRoom');
    $this->createSchemaEntity('node', 'Hotel');

    /* ********************************************************************** */

    $fields = [
      'node.hotel.schema_contains_place' => 'target_id: [current-page:query:contains_place]',
      'node.hotel_room.schema_contained_in_place' => 'target_id: [current-page:query:contained_in_place]',
    ];
    foreach ($fields as $id => $value) {
      $this->assertEquals($value, FieldConfig::load($id)->getThirdPartySetting('epp', 'value'));
    }

    // Create an hotel node.
    $node = Node::create([
      'title' => 'Hotel',
      'type' => 'hotel',
    ]);
    $node->save();

    // Check that the hotel has nodes links with entity prepopulate parameters.
    $node_links = $this->schemaEppManager->getNodeLinks($node);
    $this->convertArrayValuesToStrings($node_links);
    $expected_node_links = [
      'hotel_room--contained_in_place' => [
        'title' => 'Add Hotel Room',
        'url' => $base_path . 'node/add/hotel_room?contained_in_place=2',
      ],
      'person--member_of' => [
        'title' => 'Add Person (Member of)',
        'url' => $base_path . 'node/add/person?member_of=2',
      ],
      'person--works_for' => [
        'title' => 'Add Person (Works for)',
        'url' => $base_path . 'node/add/person?works_for=2',
      ],
      'person--member_of--works_for' => [
        'title' => 'Add Person (Member of + Works for)',
        'url' => $base_path . 'node/add/person?member_of=2&works_for=2',
      ],
      'hotel--parent_organization' => [
        'title' => 'Add Hotel',
        'url' => $base_path . 'node/add/hotel?parent_organization=2',
      ],
      'organization--parent_organization' => [
        'title' => 'Add Organization',
        'url' => $base_path . 'node/add/organization?parent_organization=2',
      ],
    ];
    $this->assertEquals($expected_node_links, $node_links);
  }

}
