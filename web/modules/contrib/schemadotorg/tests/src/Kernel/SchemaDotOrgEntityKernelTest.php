<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\field\Entity\FieldConfig;

/**
 * Tests Schema.org entity types.
 *
 * @group schemadotorg
 */
class SchemaDotOrgEntityKernelTest extends SchemaDotOrgEntityKernelTestBase {


  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_additional_mappings',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');

    $this->installConfig(['schemadotorg_additional_mappings']);
  }

  /**
   * Tests creating common entity type/bundle Schema.org types.
   *
   * Schema.org types includes...
   * - paragraph:ContentPoint
   * - media:ImageObject
   * - user:Person
   * - node:Place
   * - node:Organization
   * - node:Event.
   */
  public function testCreateSchemaEntity(): void {
    // Check creating paragraph:ContentPoint Schema.org mapping.
    $mapping = $this->createSchemaEntity('paragraph', 'ContactPoint');
    $this->assertEquals('paragraph', $mapping->getTargetEntityTypeId());
    $this->assertEquals('contact_point', $mapping->getTargetBundle());
    $this->assertEquals('ContactPoint', $mapping->getSchemaType());
    $this->assertEquals($mapping->getSchemaProperties(), [
      'schema_contact_option' => 'contactOption',
      'schema_contact_type' => 'contactType',
      'schema_email' => 'email',
      'schema_hours_available' => 'hoursAvailable',
      'schema_telephone' => 'telephone',
    ]);

    // Check creating media:ImageObject Schema.org mapping.
    $this->createMediaImage();
    $mapping = $this->createSchemaEntity('media', 'ImageObject');
    $this->assertEquals('media', $mapping->getTargetEntityTypeId());
    $this->assertEquals('image', $mapping->getTargetBundle());
    $this->assertEquals('ImageObject', $mapping->getSchemaType());
    $this->assertEquals($mapping->getSchemaProperties(), [
      'created' => 'dateCreated',
      'changed' => 'dateModified',
      'field_media_image' => 'image',
      'langcode' => 'inLanguage',
      'name' => 'name',
      'thumbnail' => 'thumbnail',
    ]);

    // Check creating user:Person Schema.org mapping.
    $mapping = $this->createSchemaEntity('user', 'Person');
    $this->assertEquals('user', $mapping->getTargetEntityTypeId());
    $this->assertEquals('user', $mapping->getTargetBundle());
    $this->assertEquals('Person', $mapping->getSchemaType());
    $this->assertEquals($mapping->getSchemaProperties(), [
      'mail' => 'email',
      'name' => 'name',
      'schema_additional_name' => 'additionalName',
      'schema_description' => 'description',
      'schema_family_name' => 'familyName',
      'schema_given_name' => 'givenName',
      'schema_image' => 'image',
      'schema_knows_language' => 'knowsLanguage',
      'schema_same_as' => 'sameAs',
      'schema_telephone' => 'telephone',
      'schema_member_of' => 'memberOf',
      'schema_works_for' => 'worksFor',
    ]);

    // Check creating node:Place Schema.org mapping.
    $mapping = $this->createSchemaEntity('node', 'Place');
    $this->assertEquals('node', $mapping->getTargetEntityTypeId());
    $this->assertEquals('place', $mapping->getTargetBundle());
    $this->assertEquals('Place', $mapping->getSchemaType());
    $this->assertEquals($mapping->getSchemaProperties(), [
      'body' => 'description',
      'schema_address' => 'address',
      'schema_image' => 'image',
      'schema_latitude' => 'latitude',
      'schema_longitude' => 'longitude',
      'schema_telephone' => 'telephone',
      'title' => 'name',
    ]);

    // Check creating node:Organization Schema.org mapping.
    $mapping = $this->createSchemaEntity('node', 'Organization');
    $this->assertEquals('node', $mapping->getTargetEntityTypeId());
    $this->assertEquals('organization', $mapping->getTargetBundle());
    $this->assertEquals('Organization', $mapping->getSchemaType());
    $this->assertEquals($mapping->getSchemaProperties(), [
      'body' => 'description',
      'schema_image' => 'image',
      'title' => 'name',
    ]);
  }

  /**
   * Tests Schema.org mapping dependencies.
   */
  public function testDependencies(): void {
    $mapping = $this->createSchemaEntity('node', 'Person');

    // Check calculating dependencies and Schema.org properties with additional mappings.
    $expected_dependencies = [
      'config' => [
        'field.field.node.person.body',
        'field.field.node.person.schema_additional_name',
        'field.field.node.person.schema_email',
        'field.field.node.person.schema_family_name',
        'field.field.node.person.schema_given_name',
        'field.field.node.person.schema_image',
        'field.field.node.person.schema_knows_language',
        'field.field.node.person.schema_member_of',
        'field.field.node.person.schema_related_link',
        'field.field.node.person.schema_same_as',
        'field.field.node.person.schema_significant_link',
        'field.field.node.person.schema_telephone',
        'field.field.node.person.schema_works_for',
        'node.type.person',
      ],
      'module' => ['node'],
    ];
    $this->assertEquals($expected_dependencies, $mapping->getDependencies());
    $expected_schema_properties = [
      'schema_additional_name' => 'additionalName',
      'body' => 'description',
      'schema_email' => 'email',
      'schema_family_name' => 'familyName',
      'schema_given_name' => 'givenName',
      'schema_image' => 'image',
      'schema_knows_language' => 'knowsLanguage',
      'schema_member_of' => 'memberOf',
      'title' => 'name',
      'schema_same_as' => 'sameAs',
      'schema_telephone' => 'telephone',
      'schema_works_for' => 'worksFor',
      'created' => 'dateCreated',
      'changed' => 'dateModified',
      'langcode' => 'inLanguage',
      'schema_related_link' => 'relatedLink',
      'schema_significant_link' => 'significantLink',
    ];
    $this->assertEquals($expected_schema_properties, $mapping->getAllSchemaProperties());

    // Check removal of dependencies and Schema.org properties with additional mappings.
    FieldConfig::loadByName('node', 'person', 'schema_image')->delete();
    FieldConfig::loadByName('node', 'person', 'schema_related_link')->delete();
    FieldConfig::loadByName('node', 'person', 'schema_significant_link')->delete();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->mappingStorage->loadUnchanged($mapping->id());
    $expected_dependencies = [
      'config' => [
        'field.field.node.person.body',
        'field.field.node.person.schema_additional_name',
        'field.field.node.person.schema_email',
        'field.field.node.person.schema_family_name',
        'field.field.node.person.schema_given_name',
        'field.field.node.person.schema_knows_language',
        'field.field.node.person.schema_member_of',
        'field.field.node.person.schema_same_as',
        'field.field.node.person.schema_telephone',
        'field.field.node.person.schema_works_for',
        'node.type.person',
      ],
      'module' => ['node'],
    ];
    $this->assertEquals($expected_dependencies, $mapping->getDependencies());
    $expected_schema_properties = [
      'schema_additional_name' => 'additionalName',
      'body' => 'description',
      'schema_email' => 'email',
      'schema_family_name' => 'familyName',
      'schema_given_name' => 'givenName',
      'schema_knows_language' => 'knowsLanguage',
      'schema_member_of' => 'memberOf',
      'title' => 'name',
      'schema_same_as' => 'sameAs',
      'schema_telephone' => 'telephone',
      'schema_works_for' => 'worksFor',
      'created' => 'dateCreated',
      'changed' => 'dateModified',
      'langcode' => 'inLanguage',
    ];
    $this->assertEquals($expected_schema_properties, $mapping->getAllSchemaProperties());
  }

}
