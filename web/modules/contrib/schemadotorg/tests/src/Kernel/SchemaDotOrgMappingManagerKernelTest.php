<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;

/**
 * Tests the Schema.org mapping manager service.
 *
 * @coversDefaultClass \Drupal\schemadotorg\SchemaDotOrgMappingManager
 * @group schemadotorg
 */
class SchemaDotOrgMappingManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->entityFieldManager = $this->container->get('entity_field.manager');
  }

  /**
   * Tests SchemaDotOrgMappingManager.
   */
  public function testMappingManager(): void {
    // Checking getting ignored Schema.org properties.
    $this->assertArrayHasKey('accessMode', $this->mappingManager->getIgnoredProperties());

    /* ********************************************************************** */
    // Prepare custom defaults.
    /* ********************************************************************** */

    // Check setting custom defaults entity and properties.
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
    );
    $expected = ['entity' => [], 'properties' => []];
    $this->assertEquals($expected, $defaults);

    // Check allowing for 'schema_properties' to be used with in custom mapping defaults.
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
      defaults: [
        'schema_properties' => ['name' => TRUE],
      ],
    );
    $expected = [
      'properties' => ['name' => TRUE],
      'entity' => [],
    ];
    $this->assertEquals($expected, $defaults);

    // Check Set the entity's bundle.
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      bundle: 'event',
      schema_type: 'Event',
    );
    $this->assertEquals('event', $defaults['entity']['id']);

    // Check property set to FALSE for new mapping.
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Thing',
      defaults: [
        'properties' => ['name' => FALSE],
      ],
    );
    $this->assertFalse($defaults['properties']['name']);

    // Check property set to FALSE is remove for existing mapping.
    $this->mappingManager->createType('node', 'Thing');
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Thing',
      defaults: [
        'properties' => ['name' => FALSE],
      ],
    );
    $this->assertArrayNotHasKey('name', $defaults['properties']);
    SchemaDotOrgMapping::load('node.thing')->delete();

    // Check preparing a custom field without settings.
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
      defaults: [
        'properties' => [
          'custom' => [],
        ],
      ],
    );
    $expected = [
      'type' => 'string',
      'label' => 'custom',
      'description' => '',
      'name' => 'custom',
      'unlimited' => FALSE,
      'required' => FALSE,
    ];
    $this->assertEquals($expected, $defaults['properties']['custom']);

    // Check preparing a custom field with settings.
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
      defaults: [
        'properties' => [
          'custom' => [
            'label' => 'Custom label',
          ],
        ],
      ],
    );
    $expected = [
      'type' => 'string',
      'label' => 'Custom label',
      'description' => '',
      'name' => 'custom',
      'unlimited' => FALSE,
      'required' => FALSE,
    ];
    $this->assertEquals($expected, $defaults['properties']['custom']);

    /* ********************************************************************** */
    // Get mappings defaults.
    /* ********************************************************************** */

    // Check getting Schema.org mapping default values.
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
    );
    $this->assertEquals('Event', $mapping_defaults['entity']['label']);
    $this->assertEquals('event', $mapping_defaults['entity']['id']);
    $this->assertStringStartsWith('An event', $mapping_defaults['entity']['description']);
    $expected = [
      'name' => 'title',
      'type' => 'string',
      'label' => 'Title',
      'machine_name' => 'name',
      'unlimited' => FALSE,
      'required' => FALSE,
      'description' => 'The name of the item.',
    ];
    $this->assertEquals($expected, $mapping_defaults['properties']['name']);
    $this->assertEquals(SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD, $mapping_defaults['properties']['description']['name']);
    $this->assertEquals('', $mapping_defaults['properties']['alternateName']['name']);

    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Person',
    );
    $expected = [
      'name' => SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD,
      'type' => 'string',
      'label' => 'First name',
      'machine_name' => 'given_name',
      'unlimited' => FALSE,
      'required' => TRUE,
      'description' => 'Given name.',
    ];
    $this->assertEquals($expected, $mapping_defaults['properties']['givenName']);

    // Check getting Schema.org mapping default values with ignored property.
    try {
      $this->mappingManager->getMappingDefaults(
        entity_type_id: 'node',
        schema_type: 'Person',
        defaults: [
          'properties' => ['deathPlace' => TRUE],
        ]
      );
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "Schema.org property 'deathPlace' for Schema.org type 'Person' is ignored. Please update your Schema.org settings. (/admin/config/schemadotorg/settings)");
    }

    // Check getting Schema.org mapping default values with invalid property.
    try {
      $defaults = [
        'properties' => ['hiringOrganization' => TRUE],
      ];
      $this->mappingManager->getMappingDefaults(
        entity_type_id: 'node',
        schema_type: 'Event',
        defaults: $defaults,
      );
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "Schema.org property 'hiringOrganization' is not supported by the Schema.org type 'Event'.");
    }

    // Check getting Schema.org mapping default values with custom field.
    try {
      $defaults = [
        'properties' => ['custom' => TRUE],
      ];
      $this->mappingManager->getMappingDefaults(
        entity_type_id: 'node',
        schema_type: 'Event',
        defaults: $defaults,
      );
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "Custom 'custom' property/field is not defined or does not exist.");
    }

    // Check getting Schema.org mapping default values for entity w/o bundles.
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'user',
      schema_type: 'Person',
    );
    $this->assertEquals('User', $mapping_defaults['entity']['label']);
    $this->assertEquals('user', $mapping_defaults['entity']['id']);

    // Check getting Schema.org mapping with a customized default type.
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'FAQPage',
    );
    $this->assertNotEquals('faq_page', $mapping_defaults['entity']['id']);
    $this->assertNotEquals('FAQ Page', $mapping_defaults['entity']['label']);
    $this->assertEquals('faq', $mapping_defaults['entity']['id']);
    $this->assertEquals('FAQ', $mapping_defaults['entity']['label']);

    // Check getting Schema.org mapping default values with custom defaults.
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
      defaults: [
        'entity' => ['label' => 'Custom event label'],
        'properties' => [
          'name' => ['label' => 'Custom name label'],
          'description' => FALSE,
          'alternateName' => TRUE,
        ],
      ],
    );
    $this->assertEquals('Custom event label', $mapping_defaults['entity']['label']);
    $this->assertEquals('title', $mapping_defaults['properties']['name']['name']);
    $this->assertEquals('Custom name label', $mapping_defaults['properties']['name']['label']);
    $this->assertEquals('', $mapping_defaults['properties']['description']['name']);
    $this->assertEquals(SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD, $mapping_defaults['properties']['alternateName']['name']);

    // Check getting Schema.org mapping default values with custom bundle.
    $mapping_defaults = $this->mappingManager->getMappingDefaults('node', 'custom', 'Event');
    $this->assertEquals('custom', $mapping_defaults['entity']['id']);

    // Check getting Schema.org mapping default entity values
    // with label and id prefixes.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
    $mapping_type = $this->entityTypeManager->getStorage('schemadotorg_mapping_type')->load('node');
    $mapping_type
      ->set('label_prefix', 'Schema.org: ')
      ->set('id_prefix', 'schema_')
      ->save();
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
    );
    $this->assertEquals('Schema.org: Event', $mapping_defaults['entity']['label']);
    $this->assertEquals('schema_event', $mapping_defaults['entity']['id']);
    $mapping_type
      ->set('label_prefix', '')
      ->set('id_prefix', '')
      ->save();

    /* ********************************************************************** */
    // Saving and creating.
    /* ********************************************************************** */

    // Check saving a Schema.org mapping.
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Event',
    );
    $mapping = $this->mappingManager->saveMapping('node', 'Event', $mapping_defaults);
    $this->assertEquals('node', $mapping->getTargetEntityTypeId());
    $this->assertEquals('event', $mapping->getTargetBundle());
    $this->assertEquals('Event', $mapping->getSchemaType());

    // Check mapping defaults entity type id validation.
    try {
      $this->mappingManager->getMappingDefaults(
        entity_type_id: 'not_node',
        schema_type: 'Event',
      );
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "A mapping type for 'not_node' does not exist and is required to create a Schema.org 'Event'.");
    }

    // Check mapping defaults schema type validation.
    try {
      $this->mappingManager->getMappingDefaults(
        entity_type_id: 'node',
        schema_type: 'NotEvent',
      );
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "A Schema.org type for 'NotEvent' does not exist.");
    }

    // Check getting Schema.org mapping default values by type.
    $mapping_defaults = $this->mappingManager->getMappingDefaultsByType('node:custom:Event');
    $this->assertEquals('custom', $mapping_defaults['entity']['id']);

    // Check create entity type validation.
    try {
      $this->mappingManager->createTypeValidate('not_entity', 'not_schema');
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "The entity type 'not_entity' is not valid. Please select a entity type (node, user).");
    }

    // Check create schema type validation.
    try {
      $this->mappingManager->createTypeValidate('node', 'not_schema');
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "The Schema.org type 'not_schema' is not valid.");
    }

    // Check create schema mapping validation.
    try {
      $this->mappingManager->createType('paragraph', 'Thing');
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "Mapping type 'paragraph' does not exist and is required to create a Schema.org 'Thing'.");
    }

    // Check creating user:Person type.
    $this->mappingManager->createType('user', 'Person');
    $mapping = SchemaDotOrgMapping::load('user.user');
    $this->assertEquals('user', $mapping->getTargetEntityTypeId());
    $this->assertEquals('user', $mapping->getTargetBundle());

    // Check create node:Article and check that the title label is set to 'Headline'.
    $this->mappingManager->createType('node', 'Article');
    $field_definitions = $this->entityFieldManager->getFieldDefinitions('node', 'article');
    /** @var \Drupal\Core\Field\BaseFieldDefinition $title_field */
    $title_field = $field_definitions['title'];
    $this->assertEquals('Headline', $title_field->getConfig('article')->getLabel());

    /* ********************************************************************** */
    // Create type with custom field.
    /* ********************************************************************** */

    $defaults = [
      'properties' => [
        'custom' => [
          'label' => 'Custom label',
        ],
      ],
    ];
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Thing',
      defaults: $defaults,
    );
    $this->mappingManager->createType('node', 'Thing', $defaults);
    $field_storage_config_storage = $this->entityTypeManager->getStorage('field_storage_config');
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    $this->assertNotNull($field_storage_config_storage->load('node.custom'));
    $field_config = $field_config_storage->load('node.thing.custom');
    $this->assertEquals('Custom label', $field_config->label());

    // Check preparing an existing custom field.
    $defaults = [
      'properties' => [
        'custom' => TRUE,
      ],
    ];
    $defaults = $this->mappingManager->prepareCustomMappingDefaults(
      entity_type_id: 'node',
      bundle: 'another_thing',
      schema_type: 'Thing',
      defaults: $defaults,
    );
    $expected_values = [
      'type' => 'string',
      'name' => 'custom',
      'unlimited' => FALSE,
      'label' => 'Custom label',
      'description' => '',
      'required' => FALSE,
    ];
    $this->assertEquals($expected_values, $defaults['properties']['custom']);

    /* ********************************************************************** */
    // Delete.
    /* ********************************************************************** */

    // Check delete schema mapping validation.
    try {
      $this->mappingManager->deleteTypeValidate('node', 'not_schema');
    }
    catch (\Exception $exception) {
      $this->assertEquals($exception->getMessage(), "No Schema.org mapping exists for not_schema (node).");
    }

    // Check deleting user:Person type.
    $this->mappingManager->deleteType('user', 'Person');
    \Drupal::entityTypeManager()->getStorage('schemadotorg_mapping')->resetCache();
    $this->assertNull(SchemaDotOrgMapping::load('user.user'));
  }

}
