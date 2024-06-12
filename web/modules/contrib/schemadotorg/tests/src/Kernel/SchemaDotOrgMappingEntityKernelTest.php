<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Config\Entity\ConfigEntityType;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\NodeType;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Tests the Schema.org mapping entity.
 *
 * @coversClass \Drupal\schemadotorg\Entity\SchemaDotOrgMapping
 * @group schemadotorg
 */
class SchemaDotOrgMappingEntityKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * A node type.
   */
  protected NodeType $nodeType;

  /**
   * A Schema.org mapping entity for a node.
   */
  protected SchemaDotOrgMappingInterface $nodeMapping;

  /**
   * A Schema.org mapping entity for a user.
   */
  protected SchemaDotOrgMappingInterface $userMapping;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('node_type');
  }

  /**
   * Test Schema.org mapping entity.
   */
  public function testSchemaDotOrgMappingEntity(): void {
    // Create Thing node with field.
    $node_type = NodeType::create([
      'type' => 'thing',
      'name' => 'Thing',
    ]);
    $node_type->save();
    $this->createSchemaDotOrgField('node', 'Thing');

    // Create Thing with mapping.
    $node_mapping = SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'thing',
      'schema_type' => 'Thing',
      'schema_properties' => [
        'title' => 'name',
        'schema_alternate_name' => 'alternateName',
      ],
    ]);
    $node_mapping->save();

    // Create user with Person mapping.
    $user_mapping = SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'user',
      'target_bundle' => 'user',
      'schema_type' => 'Person',
      'schema_properties' => [
        'name' => 'name',
      ],
    ]);
    $user_mapping->save();

    /* ********************************************************************** */
    // Check getting the entity type for which this mapping is used. (i.e. node)
    $this->assertEquals('node', $node_mapping->getTargetEntityTypeId());

    // Check getting the bundle to be mapped. (i.e. page)
    $this->assertEquals('thing', $node_mapping->getTargetBundle());

    // Check setting the bundle to be mapped.
    $node_mapping->setTargetBundle('cat');
    $this->assertEquals('cat', $node_mapping->getTargetBundle());
    $node_mapping->setTargetBundle('thing');

    // Check getting the entity type definition. (i.e. node annotation)
    $target_entity_type_definition = $node_mapping->getTargetEntityTypeDefinition();
    $this->assertInstanceOf(ContentEntityType::class, $target_entity_type_definition);
    $this->assertEquals('node', $target_entity_type_definition->id());
    $this->assertEquals('Content', $target_entity_type_definition->getLabel());

    // Check getting the entity type's bundle ID. (i.e. node_type)
    $this->assertEquals('node_type', $node_mapping->getTargetEntityTypeBundleId());

    // Check getting the entity type's bundle definition. (i.e. node_type annotation)
    $target_entity_type_bundle_definition = $node_mapping->getTargetEntityTypeBundleDefinition();
    $this->assertInstanceOf(ConfigEntityType::class, $target_entity_type_bundle_definition);
    $this->assertEquals('node_type', $target_entity_type_bundle_definition->id());
    $this->assertEquals('Content type', $target_entity_type_bundle_definition->getLabel());

    // Check getting the bundle entity type. (i.e. node_type:page)
    $target_entity_bundle_entity = $node_mapping->getTargetEntityBundleEntity();
    $this->assertInstanceOf(ConfigEntityType::class, $target_entity_type_bundle_definition);
    $this->assertEquals('thing', $target_entity_bundle_entity->id());
    $this->assertEquals('Thing', $target_entity_bundle_entity->label());

    // Check determining if the entity type supports bundling.
    $this->assertTrue($node_mapping->isTargetEntityTypeBundle());
    $this->assertFalse($user_mapping->isTargetEntityTypeBundle());

    // Check determining if a new bundle entity is being created.
    $this->assertFalse($node_mapping->isNewTargetEntityTypeBundle());
    $this->assertFalse($user_mapping->isNewTargetEntityTypeBundle());
    $new_bundle_mapping = SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'place',
      'schema_type' => 'Place',
    ]);
    $this->assertTrue($new_bundle_mapping->isNewTargetEntityTypeBundle());

    // Check getting the Schema.org type to be mapped.
    $this->assertEquals('Thing', $node_mapping->getSchemaType());

    // Check setting the Schema.org type to be mapped.
    $node_mapping->setSchemaType('Cat');
    $this->assertEquals('Cat', $node_mapping->getSchemaType());
    $node_mapping->setSchemaType('Thing');

    // Check getting the Schema.org types to be mapped.
    $this->assertEquals(['Thing' => 'Thing'], $node_mapping->getAllSchemaTypes());
    $node_mapping->setAdditionalMapping('WebPage', ['schema_related_link' => 'relatedLink']);
    $this->assertEquals(['Thing' => 'Thing', 'WebPage' => 'WebPage'], $node_mapping->getAllSchemaTypes());
    $node_mapping->set('additional_mappings', []);

    // Check getting the mappings for Schema.org properties.
    $expected_schema_properties = [
      'title' => 'name',
      'schema_alternate_name' => 'alternateName',
    ];
    $this->assertEquals($expected_schema_properties, $node_mapping->getSchemaProperties());

    // Check getting the original mappings for Schema.org properties.
    $this->assertEquals($node_mapping->getOriginalSchemaProperties(), $node_mapping->getSchemaProperties());

    // Check setting the original mappings for Schema.org properties.
    $node_mapping->setOriginalSchemaProperties([]);
    $this->assertEquals([], $node_mapping->getOriginalSchemaProperties());

    // Check getting the new mappings for Schema.org properties.
    $this->assertEquals($node_mapping->getSchemaProperties(), $node_mapping->getNewSchemaProperties());
    $node_mapping->save();
    $node_mapping->setSchemaPropertyMapping('body', 'description');
    $this->assertEquals(['body' => 'description'], $node_mapping->getNewSchemaProperties());

    // Check getting the Schema.org properties for the main and additional mappings.
    $node_mapping->setAdditionalMapping('WebPage', ['schema_related_link' => 'relatedLink']);
    $expected_schema_properties = [
      'title' => 'name',
      'schema_alternate_name' => 'alternateName',
      'body' => 'description',
      'schema_related_link' => 'relatedLink',
    ];
    $this->assertEquals($expected_schema_properties, $node_mapping->getAllSchemaProperties());
    $node_mapping->set('additional_mappings', []);

    // Check getting the mapping set for a property.
    $this->assertEquals('name', $node_mapping->getSchemaPropertyMapping('title'));

    // Check setting the mapping for a Schema.org property.
    $node_mapping->setSchemaPropertyMapping('field_thing_type', 'additionalType');
    $this->assertEquals('additionalType', $node_mapping->getSchemaPropertyMapping('field_thing_type'));

    // Check that setting the mapping for an invalid Schema.org property
    // throws an exception.
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("The 'notValid' property does not exist in Schema.org type 'Thing'.");
    $node_mapping->setSchemaPropertyMapping('field_not_validate', 'notValid');

    // Check removing the Schema.org property mapping.
    $node_mapping->removeSchemaProperty('created');
    $this->assertNull($node_mapping->getSchemaPropertyMapping('created'));

    // Check getting the field name for a property.
    $this->assertNull($node_mapping->getSchemaPropertyFieldName('notAProperty'));
    $this->assertEquals('title', $node_mapping->getSchemaPropertyFieldName('name'));
    $this->assertEquals('schema_alternate_name', $node_mapping->getSchemaPropertyFieldName('alternateName'));

    // Check getting the field name for an additional mapping's property.
    // Check setting an additional Schema.org mapping.
    $node_mapping->setAdditionalMapping('WebPage', ['schema_related_link' => 'relatedLink']);
    $this->assertEquals('schema_related_link', $node_mapping->getSchemaPropertyFieldName('relatedLink'));
    $node_mapping->set('additional_mappings', []);

    // Check determining if a Schema.org property is mapped to a Drupal field.
    $this->assertTrue($node_mapping->hasSchemaPropertyMapping('name'));
    $this->assertFalse($node_mapping->hasSchemaPropertyMapping('not_name'));

    // Check getting additional Schema.org mappings.
    $this->assertEquals([], $node_mapping->getAdditionalMappings());

    // Check that setting an additional Schema.org mapping with an invalid
    // Schema.org property throws an exception.
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("The 'notValid' property does not exist in Schema.org type 'Thing'.");
    $node_mapping->setAdditionalMapping('WebPage', ['field_not_validate' => 'notValid']);

    // Check setting an additional Schema.org mapping.
    $additional_schema_type = 'WebPage';
    $additional_schema_properties = [
      'title' => 'name',
      'schema_image' => 'primaryImageOfPage',
      'schema_related_link' => 'relatedLink',
      'schema_significant_link' => 'significantLink',
    ];
    $node_mapping->setAdditionalMapping($additional_schema_type, $additional_schema_properties);
    $expected_additional_mapping = [
      'schema_type' => $additional_schema_type,
      'schema_properties' => $additional_schema_properties,
    ];
    $expected_additional_mappings = [
      $additional_schema_type => $expected_additional_mapping,
    ];
    $this->assertEquals($expected_additional_mappings, $node_mapping->getAdditionalMappings());

    // Check getting Schema.org properties for all additional Schema.org mappings.
    $expected_schema_properties = [
      'title' => 'name',
      'schema_image' => 'primaryImageOfPage',
      'schema_related_link' => 'relatedLink',
      'schema_significant_link' => 'significantLink',
    ];
    $this->assertEquals($expected_schema_properties, $node_mapping->getAdditionalMappingsSchemaProperties());

    // Check getting an additional Schema.org mapping.
    $this->assertEquals($expected_additional_mapping, $node_mapping->getAdditionalMapping($additional_schema_type));
    $this->assertEquals(NULL, $node_mapping->getAdditionalMapping('Thing'));

    // Check removing an additional Schema.org mapping.
    $this->assertEquals($node_mapping, $node_mapping->removeAdditionalMapping($additional_schema_type));
    $this->assertEquals(NULL, $node_mapping->getAdditionalMapping($additional_schema_type));
    $this->assertEquals([], $node_mapping->getAdditionalMappings());

    // Check calculating and getting the configuration dependencies.
    $expected_dependencies = [
      'config' => [
        'field.field.node.thing.schema_alternate_name',
        'node.type.thing',
      ],
      'module' => ['node'],
    ];
    $actual_dependencies = $node_mapping->calculateDependencies()->getDependencies();
    $this->assertEquals($expected_dependencies, $actual_dependencies);

    // Check deleting field removes the property mapping.
    $this->assertEquals('alternateName', $node_mapping->getSchemaPropertyMapping('schema_alternate_name'));
    FieldConfig::load('node.thing.schema_alternate_name')->delete();
    $this->mappingStorage->resetCache();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $node_mapping */
    $node_mapping = $this->mappingStorage->load('node.thing');
    $this->assertNull($node_mapping->getSchemaPropertyMapping('schema_alter_name'));

    // Check deleting the target type removes the mapping.
    // @see \Drupal\schemadotorg\Entity\SchemaDotOrgMapping::onDependencyRemoval
    $this->assertNotNull($this->mappingStorage->load('node.thing'));
    $node_type->delete();
    $this->mappingStorage->resetCache();
    $this->assertNull($this->mappingStorage->load('node.thing'));
  }

}
