<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Component\Utility\Html;
use Drupal\node\Entity\Node;

/**
 * Tests the schema type entity reference selection.
 *
 * @coversDefaultClass \Drupal\schemadotorg\Plugin\EntityReferenceSelection\SchemaDotOrgNodeReferenceSelection
 * @covers \Drupal\schemadotorg\Plugin\EntityReferenceSelection\SchemaDotOrgEntityReferenceSelection
 *
 * @group schemadotorg
 */
class SchemaDotOrgReferenceSelectionKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * An associative array of node values used for expected results.
   */
  protected array $nodeValues = [
    'published1' => [
      'type' => 'place',
      'title' => 'Node published1 (<&>)',
    ],
    'published2' => [
      'type' => 'place',
      'title' => 'Node published2 (<&>)',
    ],
    'published3' => [
      'type' => 'organization',
      'title' => 'Node published3 (<&>)',
    ],
    'unpublished1' => [
      'type' => 'place',
      'title' => 'Node NOT published1 (<&>)',
      'status' => FALSE,
    ],
  ];

  /**
   * Test selection referenceable entities behavior.
   *
   * @covers ::buildEntityQuery
   * @covers \Drupal\schemadotorg\Plugin\EntityReferenceSelection\SchemaDotOrgEntityReferenceSelection::getReferenceableEntities
   * @covers \Drupal\schemadotorg\Plugin\EntityReferenceSelection\SchemaDotOrgEntityReferenceSelection::buildEntityQuery
   *
   * @dataProvider providerTestSelectionReferenceableEntities
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testSelectionReferenceableEntities(array $schema_types, array $expected_keys): void {
    // Create Place with mapping.
    $this->createSchemaEntity('node', 'Place');

    // Create Organization with mapping.
    $this->createSchemaEntity('node', 'Organization');

    // Test as a non-admin.
    $this->drupalSetUpCurrentUser();

    /* ********************************************************************** */

    // Build expected result.
    $expected_result = [];
    foreach ($expected_keys as $key => $type) {
      $values = $this->nodeValues[$key];
      $node = Node::create($values);
      $node->save();
      $expected_result[$type][$node->id()] = Html::escape($node->label());
    }

    // Get rhw 'schemadotorg' entity reference selection handler.
    $selection_options = [
      'target_type' => 'node',
      'handler' => 'schemadotorg',
      'schema_types' => $schema_types,
    ];
    /** @var \Drupal\schemadotorg\Plugin\EntityReferenceSelection\SchemaDotOrgEntityReferenceSelection $selection_handler */
    $selection_handler = $this->container
      ->get('plugin.manager.entity_reference_selection')
      ->getInstance($selection_options);

    // Check that the expected result matches the referenceable entities.
    $result = $selection_handler->getReferenceableEntities();
    foreach ($result as $type => $result_value) {
      $this->assertSame($expected_result[$type], $result_value, 'Query sorted by field returned expected values.');
    }
  }

  /**
   * Data provider for ::testSelectionReferenceableEntities.
   *
   * @return array
   *   The data.
   */
  public function providerTestSelectionReferenceableEntities(): array {
    return [
      [
        ['Place'],
        [
          'published1' => 'place',
          'published2' => 'place',
        ],
      ],
      [
        ['Place', 'Organization'],
        [
          'published3' => 'organization',
          'published1' => 'place',
          'published2' => 'place',
        ],
      ],
    ];
  }

  /**
   * Test selection target bundles behavior.
   *
   * @covers schemadotorg_schemadotorg_mapping_insert()
   */
  public function testSelectionTargetBundles(): void {
    // Set up the memberOf to member entity reference relationship.
    $this->appendSchemaTypeDefaultProperties('Person', ['memberOf']);
    $this->appendSchemaTypeDefaultProperties('Organization', ['member']);
    $this->config('schemadotorg.settings')
      ->clear('schema_properties.range_includes.member')
      ->set('schema_properties.default_fields.memberOf.type', 'field_ui:entity_reference:node')
      ->set('schema_properties.default_fields.organization.type', 'field_ui:entity_reference:node')
      ->save();

    /* ********************************************************************** */

    /** @var \Drupal\field\FieldConfigStorage $field_config_storage */
    $field_config_storage = \Drupal::entityTypeManager()
      ->getStorage('field_config');

    // Create a Person.
    $this->createSchemaEntity('node', 'Person');

    // Check that Person schema_member_of field has no target bundles.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = $field_config_storage->load('node.person.schema_member_of');
    $this->assertEquals([], $field_config->getSetting('handler_settings')['target_bundles']);

    // Create a Organization.
    $this->createSchemaEntity('node', 'Organization');

    // Reset field config storage.
    $field_config_storage->resetCache();

    // Check that Person schema_member_of field now has organization as the
    // target bundle.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = $field_config_storage->load('node.person.schema_member_of');
    $this->assertEquals(['organization' => 'organization'], $field_config->getSetting('handler_settings')['target_bundles']);

    // Check that Organization schema_member field now has person
    // and organization as the target bundles.
    $field_config = $field_config_storage->load('node.organization.schema_member');
    $this->assertEquals(['person' => 'person', 'organization' => 'organization'], $field_config->getSetting('handler_settings')['target_bundles']);

    // Create a LocalBusiness.
    $this->createSchemaEntity('node', 'LocalBusiness');

    // Reset field config storage.
    $field_config_storage->resetCache();

    // Check that Organization schema_member field now has person,
    // organization and local_business as the target bundles.
    $field_config = $field_config_storage->load('node.organization.schema_member');
    $this->assertEquals(['person' => 'person', 'organization' => 'organization', 'local_business' => 'local_business'], $field_config->getSetting('handler_settings')['target_bundles']);

    // Check that Organization schema_member field now has person,
    // organization as the target bundles with local_business excluded.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = $field_config_storage->load('node.organization.schema_member');
    $handler_settings = $field_config->getSetting('handler_settings');
    $handler_settings['excluded_schema_types'] = ['LocalBusiness' => 'LocalBusiness'];
    $field_config->setSetting('handler_settings', $handler_settings);
    $field_config->save();

    $this->assertEquals(['person' => 'person', 'organization' => 'organization'], $field_config->getSetting('handler_settings')['target_bundles']);

  }

}
