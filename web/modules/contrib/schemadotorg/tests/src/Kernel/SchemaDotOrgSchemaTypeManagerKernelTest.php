<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Component\Utility\NestedArray;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Tests the Schema.org schema type manager service.
 *
 * @coversClass \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager
 * @group schemadotorg
 */
class SchemaDotOrgSchemaTypeManagerKernelTest extends SchemaDotOrgKernelTestBase {

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Install the Schema.org module's entities, config, and tables.
    $this->installSchemaDotOrg();

    // Set schema type manager.
    $this->schemaTypeManager = $this->container->get('schemadotorg.schema_type_manager');
  }

  /**
   * Test Schema.org schema type manager .
   */
  public function testSchemaTypeManager(): void {
    // Check get Schema.org type or property URI.
    $this->assertEquals('https://schema.org/Thing', $this->schemaTypeManager->getUri('Thing'));

    // Check determining if ID is in a valid Schema.org table.
    $this->assertTrue($this->schemaTypeManager->isId('types', 'Thing'));
    $this->assertFalse($this->schemaTypeManager->isId('types', 'thing'));
    $this->assertFalse($this->schemaTypeManager->isId('properties', 'Thing'));
    $this->assertTrue($this->schemaTypeManager->isId('properties', 'name'));
    $this->assertFalse($this->schemaTypeManager->isId('properties', 'Name'));
    $this->assertFalse($this->schemaTypeManager->isId('types', 'name'));

    // Check determining ID is a Schema.org type or property.
    $this->assertTrue($this->schemaTypeManager->isItem('Thing'));
    $this->assertTrue($this->schemaTypeManager->isItem('name'));
    $this->assertFalse($this->schemaTypeManager->isItem('xxx'));

    // Check determining ID is a Schema.org type.
    $this->assertTrue($this->schemaTypeManager->isType('Thing'));
    $this->assertTrue($this->schemaTypeManager->isType('Text'));
    $this->assertTrue($this->schemaTypeManager->isType('Enumeration'));
    $this->assertTrue($this->schemaTypeManager->isType('Intangible'));
    $this->assertFalse($this->schemaTypeManager->isType('name'));
    $this->assertFalse($this->schemaTypeManager->isType('xxx'));

    // Check determining if a Schema.org type is a subtype of another
    // Schema.org type.
    $this->assertTrue($this->schemaTypeManager->isSubTypeOf('SearchAction', 'Action'));
    $this->assertTrue($this->schemaTypeManager->isSubTypeOf('Action', 'Action'));
    $this->assertFalse($this->schemaTypeManager->isSubTypeOf('Action', 'SearchAction'));

    // Check determining if ID is a Schema.org Thing type.
    $this->assertTrue($this->schemaTypeManager->isThing('Thing'));
    $this->assertFalse($this->schemaTypeManager->isThing('Text'));
    $this->assertFalse($this->schemaTypeManager->isThing('Url'));
    $this->assertFalse($this->schemaTypeManager->isThing('Enumeration'));
    $this->assertFalse($this->schemaTypeManager->isThing('Intangible'));

    // Check determining ID is a Schema.org data type.
    $this->assertFalse($this->schemaTypeManager->isDataType('Thing'));
    $this->assertFalse($this->schemaTypeManager->isDataType('name'));
    $this->assertTrue($this->schemaTypeManager->isDataType('Text'));

    // Check determine if ID is a Schema.org Intangible.
    $this->assertFalse($this->schemaTypeManager->isIntangible('Thing'));
    $this->assertTrue($this->schemaTypeManager->isIntangible('Intangible'));
    $this->assertTrue($this->schemaTypeManager->isIntangible('Enumeration'));

    // Check determining if ID is a Schema.org enumeration type.
    $this->assertTrue($this->schemaTypeManager->isEnumerationType('GenderType'));
    $this->assertFalse($this->schemaTypeManager->isEnumerationType('Thing'));
    $this->assertFalse($this->schemaTypeManager->isEnumerationType('Male'));

    // Check determining ID is a Schema.org enumeration value.
    $this->assertFalse($this->schemaTypeManager->isEnumerationValue('GenderType'));
    $this->assertFalse($this->schemaTypeManager->isEnumerationValue('Thing'));
    $this->assertTrue($this->schemaTypeManager->isEnumerationValue('Male'));

    // Check determining ID is a Schema.org property.
    $this->assertTrue($this->schemaTypeManager->isProperty('name'));
    $this->assertFalse($this->schemaTypeManager->isProperty('Thing'));
    $this->assertFalse($this->schemaTypeManager->isProperty('xxx'));

    // Check determining if a Schema.org property is a sub property.
    $this->assertFalse($this->schemaTypeManager->isSubPropertyOf('accountId', 'name'));
    $this->assertFalse($this->schemaTypeManager->isSubPropertyOf('notProperty', 'name'));
    $this->assertTrue($this->schemaTypeManager->isSubPropertyOf('accountId', 'identifier'));

    // Check determining if Schema.org ID is superseded.
    $this->assertTrue($this->schemaTypeManager->isSuperseded('UserInteraction'));
    $this->assertFaLse($this->schemaTypeManager->isSuperseded('Event'));

    // Check parsing Schema.org type or property IDs.
    $tests = [
      [' ', []],
      ['https://schema.org/Thing', ['Thing']],
      [
        'https://schema.org/Thing, https://schema.org/Place',
        ['Thing', 'Place'],
      ],
      [
        'https://not-schema.org/Thing, https://schema.org/Place',
        ['https://not-schema.org/Thing', 'Place'],
      ],
    ];
    foreach ($tests as $test) {
      $expected = array_combine($test[1], $test[1]);
      $this->assertEquals($expected, $this->schemaTypeManager->parseIds($test[0]));
    }

    // Check getting Schema.org type item returns all fields.
    $item = $this->schemaTypeManager->getItem(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES, 'Thing');
    $this->assertEquals('https://schema.org/Thing', $item['id']);
    $this->assertEquals('Thing', $item['label']);
    $this->assertEquals('The most generic type of item.', $item['comment']);

    // Check getting Schema.org property item returns all fields.
    $item = $this->schemaTypeManager->getItem(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_PROPERTIES, 'name');
    $this->assertEquals('https://schema.org/name', $item['id']);
    $this->assertEquals('name', $item['label']);
    $this->assertEquals('The name of the item.', $item['comment']);

    // Check getting Schema.org type or property item returns selected field.
    $item = $this->schemaTypeManager->getItem(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES, 'Thing', ['label']);
    $this->assertArrayNotHasKey('id', $item);
    $this->assertArrayHasKey('label', $item);
    $this->assertArrayNotHasKey('comment', $item);
    $this->assertFalse($this->schemaTypeManager->getItem(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES, 'name'));

    // Check getting Schema.org type.
    $type = $this->schemaTypeManager->getType('Thing');
    $this->assertEquals('https://schema.org/Thing', $type['id']);
    $this->assertEquals('Thing', $type['label']);
    $this->assertEquals('The most generic type of item.', $type['comment']);

    // Check getting Schema.org property.
    $property = $this->schemaTypeManager->getProperty('name');
    $this->assertEquals('https://schema.org/name', $property['id']);
    $this->assertEquals('name', $property['label']);
    $this->assertEquals('The name of the item.', $property['comment']);

    // Check getting a Schema.org property's range includes.
    $this->assertEquals(['Text' => 'Text'], $this->schemaTypeManager->getPropertyRangeIncludes('name'));
    $this->assertEquals([
      'ImageObject' => 'ImageObject',
      'URL' => 'URL',
    ], $this->schemaTypeManager->getPropertyRangeIncludes('image'));

    // Check getting a Schema.org property's default Schema.org type.
    $this->assertEquals('Organization', $this->schemaTypeManager->getPropertyDefaultType('alumniOf'));
    $this->assertEquals('Organization', $this->schemaTypeManager->getPropertyDefaultType('brand'));
    $this->assertEquals('CreativeWork', $this->schemaTypeManager->getPropertyDefaultType('subjectOf'));
    $this->assertEquals('Answer', $this->schemaTypeManager->getPropertyDefaultType('acceptedAnswer'));
    $this->assertNull($this->schemaTypeManager->getPropertyDefaultType('recipeInstructions'));

    // Check getting a Schema.org property's unit.
    $this->assertNull($this->schemaTypeManager->getPropertyUnit('id'));
    $this->assertNull($this->schemaTypeManager->getPropertyUnit('id', 1));
    $this->assertEquals('grams', $this->schemaTypeManager->getPropertyUnit('carbohydrateContent'));
    $this->assertEquals('gram', $this->schemaTypeManager->getPropertyUnit('carbohydrateContent', 1));
    $this->assertNull($this->schemaTypeManager->getPropertyUnit('carbohydrateContent', NULL));
    $this->assertEquals('milligrams', $this->schemaTypeManager->getPropertyUnit('cholesterolContent'));
    $this->assertEquals('milligram', $this->schemaTypeManager->getPropertyUnit('cholesterolContent', 1));
    $this->assertNull($this->schemaTypeManager->getPropertyUnit('cholesterolContent', NULL));

    // Check getting Schema.org type or property items.
    $items = $this->schemaTypeManager->getItems(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES, ['Thing', 'Place']);
    $this->assertEquals('https://schema.org/Thing', $items['Thing']['id']);
    $this->assertEquals('Thing', $items['Thing']['label']);
    $this->assertEquals('The most generic type of item.', $items['Thing']['comment']);
    $this->assertEquals('https://schema.org/Place', $items['Place']['id']);
    $this->assertEquals('Place', $items['Place']['label']);
    $this->assertEquals('Entities that have a somewhat fixed, physical extension.', $items['Place']['comment']);

    // Check getting Schema.org types.
    $types = $this->schemaTypeManager->getTypes(['Thing', 'Place'], [
      'id',
      'label',
    ]);
    $this->assertEquals('https://schema.org/Thing', $types['Thing']['id']);
    $this->assertEquals('Thing', $types['Thing']['label']);
    $this->assertArrayNotHasKey('comment', $types['Thing']);
    $this->assertEquals('https://schema.org/Place', $types['Place']['id']);
    $this->assertEquals('Place', $types['Place']['label']);
    $this->assertArrayNotHasKey('comment', $types['Place']);

    // Check getting Schema.org properties.
    $expected_properties = [
      'alternateName' => ['label' => 'alternateName'],
      'name' => ['label' => 'name'],
    ];
    $actual_properties = $this->schemaTypeManager->getProperties([
      'name',
      'alternateName',
    ], ['label']);
    $this->assertEquals($expected_properties, $actual_properties);

    // Check getting a Schema.org type's properties.
    $type_properties = $this->schemaTypeManager->getTypeProperties('Thing', ['label']);
    $properties = [
      'additionalType',
      'alternateName',
      'description',
      'disambiguatingDescription',
      'identifier',
      'image',
      'name',
      'url',
    ];
    foreach ($properties as $property) {
      $this->assertArrayHasKey($property, $type_properties);
    }
    // Check getting a Schema.org type's properties for an Enumeration which
    // has not properties.
    $this->assertEquals([], $this->schemaTypeManager->getTypeProperties('Abdomen', ['label']));

    // Check getting all child Schema.org types below a specified type.
    $type_children = $this->schemaTypeManager->getTypeChildren('Person');
    $this->assertEquals(['Patient' => 'Patient'], $type_children);
    $type_children = $this->schemaTypeManager->getTypeChildren('GenderType');
    $this->assertEquals([
      'Male' => 'Male',
      'Female' => 'Female',
    ], $type_children);

    // Check getting all child Schema.org types below a specified type.
    $type_children = $this->schemaTypeManager->getAllTypeChildrenAsOptions('Person');
    $this->assertEquals(['Patient' => 'Patient'], $type_children);
    $type_children = $this->schemaTypeManager->getAllTypeChildrenAsOptions('GenderType');
    $this->assertEquals([
      'Male' => 'Male',
      'Female' => 'Female',
    ], $type_children);

    // Check getting all child Schema.org types below a specified type
    // with an ignored type.
    $type_children = $this->schemaTypeManager->getAllTypeChildrenAsOptions('GenderType', ['Male']);
    $this->assertEquals([
      'Female' => 'Female',
    ], $type_children);

    // Check getting Schema.org subtypes.
    $subtypes = $this->schemaTypeManager->getSubtypes('Person');
    $this->assertEquals(['Patient' => 'Patient'], $subtypes);
    $subtypes = $this->schemaTypeManager->getSubtypes('GenderType');
    $this->assertNotEquals(['Male' => 'Male', 'Female' => 'Female'], $subtypes);
    $this->assertEquals([], $subtypes);

    // Check getting Schema.org enumerations.
    $enumerations = $this->schemaTypeManager->getEnumerations('Person');
    $this->assertNotEquals(['Patient' => 'Patient'], $enumerations);
    $this->assertEquals([], $enumerations);
    $enumerations = $this->schemaTypeManager->getEnumerations('GenderType');
    $this->assertEquals([
      'Male' => 'Male',
      'Female' => 'Female',
    ], $enumerations);

    // Check getting Schema.org data types.
    $expected_data_types = [
      'Boolean' => 'Boolean',
      'Date' => 'Date',
      'DateTime' => 'DateTime',
      'False' => 'False',
      'Number' => 'Number',
      'Text' => 'Text',
      'Time' => 'Time',
      'True' => 'True',
      'Float' => 'Float',
      'Integer' => 'Integer',
      'CssSelectorType' => 'CssSelectorType',
      'PronounceableText' => 'PronounceableText',
      'URL' => 'URL',
      'XPathType' => 'XPathType',
    ];
    $actual_data_types = $this->schemaTypeManager->getDataTypes();
    $this->assertEquals($expected_data_types, $actual_data_types);

    // Check getting parent Schema.org types for specified Schema.org type.
    $expected_parent_types = [
      'Thing' => 'Thing',
      'Organization' => 'Organization',
      'Place' => 'Place',
      'LocalBusiness' => 'LocalBusiness',
      'MedicalOrganization' => 'MedicalOrganization',
      'CivicStructure' => 'CivicStructure',
      'EmergencyService' => 'EmergencyService',
      'Hospital' => 'Hospital',
    ];
    $actual_parent_types = $this->schemaTypeManager->getParentTypes('Hospital');
    $this->assertEquals($expected_parent_types, $actual_parent_types);

    // Check getting all Schema.org subtypes below specified Schema.org types.
    $expected_all_sub_types = [
      'Person' => 'Person',
      'Product' => 'Product',
      'Patient' => 'Patient',
      'IndividualProduct' => 'IndividualProduct',
      'ProductCollection' => 'ProductCollection',
      'ProductGroup' => 'ProductGroup',
      'ProductModel' => 'ProductModel',
      'SomeProducts' => 'SomeProducts',
      'Vehicle' => 'Vehicle',
      'BusOrCoach' => 'BusOrCoach',
      'Car' => 'Car',
      'Motorcycle' => 'Motorcycle',
      'MotorizedBicycle' => 'MotorizedBicycle',
      'DietarySupplement' => 'DietarySupplement',
      'Drug' => 'Drug',
    ];
    $actual_all_sub_types = $this->schemaTypeManager->getAllSubTypes([
      'Person',
      'Product',
    ]);
    $this->assertEquals($expected_all_sub_types, $actual_all_sub_types);

    // Check getting all Schema.org types below a specified type.
    $expected_all_type_children = [
      'Product' => 'Product',
      'IndividualProduct' => 'IndividualProduct',
      'ProductCollection' => 'ProductCollection',
      'ProductGroup' => 'ProductGroup',
      'ProductModel' => 'ProductModel',
      'SomeProducts' => 'SomeProducts',
      'Vehicle' => 'Vehicle',
      'BusOrCoach' => 'BusOrCoach',
      'Car' => 'Car',
      'Motorcycle' => 'Motorcycle',
      'MotorizedBicycle' => 'MotorizedBicycle',
    ];
    $actual_all_type_children = $this->schemaTypeManager->getAllTypeChildren('Product', ['label']);
    foreach ($expected_all_type_children as $expected_all_type_child) {
      $this->assertArrayHasKey($expected_all_type_child, $actual_all_type_children);
    }

    // Check getting all Schema.org types below a specified type with
    // ignored types.
    $expected_all_type_children = [
      'Product' => 'Product',
      'IndividualProduct' => 'IndividualProduct',
      'ProductCollection' => 'ProductCollection',
      'ProductGroup' => 'ProductGroup',
      'ProductModel' => 'ProductModel',
      'SomeProducts' => 'SomeProducts',
    ];
    $actual_all_type_children = $this->schemaTypeManager->getAllTypeChildren('Product', ['label'], ['Vehicle']);
    foreach ($expected_all_type_children as $expected_all_type_child) {
      $this->assertArrayHasKey($expected_all_type_child, $actual_all_type_children);
    }

    // Check getting Schema.org type hierarchical tree.
    $type_tree = $this->schemaTypeManager->getTypeTree('Product');
    $this->assertIsNotArray(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'NotAProduct',
    ]));
    $this->assertNull(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'NotAProduct',
    ]));
    $this->assertIsArray(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'IndividualProduct',
      'subtypes',
    ]));
    $this->assertIsArray(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'IndividualProduct',
      'enumerations',
    ]));
    $this->assertIsArray(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'Vehicle',
      'subtypes',
      'BusOrCoach',
      'subtypes',
    ]));
    $this->assertIsArray(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'Vehicle',
      'subtypes',
      'BusOrCoach',
      'enumerations',
    ]));

    // Check getting Schema.org type hierarchical tree with ignored types.
    $type_tree = $this->schemaTypeManager->getTypeTree('Product', ['IndividualProduct']);
    $this->assertIsNotArray(NestedArray::getValue($type_tree, [
      'Product',
      'subtypes',
      'IndividualProduct',
      'subtypes',
    ]));

    // Check getting Schema.org type breadcrumbs.
    $expected_breadcrumbs = [
      'Thing/Organization/LocalBusiness' => [
        'Thing' => 'Thing',
        'Organization' => 'Organization',
        'LocalBusiness' => 'LocalBusiness',
      ],
      'Thing/Place/LocalBusiness' => [
        'Thing' => 'Thing',
        'Place' => 'Place',
        'LocalBusiness' => 'LocalBusiness',
      ],
    ];
    $actual_breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs('LocalBusiness');
    $this->assertEquals($expected_breadcrumbs, $actual_breadcrumbs);

    // Check determining if a Schema.org type has a Schema.org property.
    $this->assertTrue($this->schemaTypeManager->hasProperty('Thing', 'alternateName'));
    $this->assertFalse($this->schemaTypeManager->hasProperty('Thing', 'headline'));
    $this->assertTrue($this->schemaTypeManager->hasProperty('CreativeWork', 'headline'));

    // Check determining if a Schema.org type has subtypes.
    $this->assertTrue($this->schemaTypeManager->hasSubtypes('Thing'));
    $this->assertTrue($this->schemaTypeManager->hasSubtypes('Person'));
    $this->assertFalse($this->schemaTypeManager->hasSubtypes('Patient'));

    // Check getting setting from an associative array by type and property.
    $settings = [
      'Recipe--isFamilyFriendly' => 'Recipe is family friendly',
      'CreativeWork--additionalType' => 'Creative work has additional type',
      'medical_study--ResearchProject' => 'Medical studies are also research projects.',
      'Place' => 'This is a place.',
      'Thing' => 'This is thing',
      'name' => 'A name',
    ];

    $setting_parts = [
      'schema_type' => 'Recipe',
      'schema_property' => 'isFamilyFriendly',
    ];
    $this->assertEquals(
      'Recipe is family friendly',
      $this->schemaTypeManager->getSetting($settings, $setting_parts)
    );

    $setting_parts = [
      'schema_type' => 'Recipe',
      'schema_property' => 'additionalType',
    ];
    $this->assertEquals(
      'Creative work has additional type',
      $this->schemaTypeManager->getSetting($settings, $setting_parts)
    );

    $setting_parts = [
      'schema_type' => 'Recipe',
      'schema_property' => 'name',
    ];
    $this->assertEquals(
      'A name',
      $this->schemaTypeManager->getSetting($settings, $setting_parts)
    );
    $setting_parts = [
      'schema_type' => 'CreativeWork',
      'schema_property' => 'name',
    ];
    $this->assertEquals(
      'A name',
      $this->schemaTypeManager->getSetting($settings, $setting_parts)
    );
    $setting_parts = [
      'bundle' => 'medical_study',
      'schema_type' => 'ResearchProject',
    ];
    $this->assertEquals(
      'Medical studies are also research projects.',
      $this->schemaTypeManager->getSetting($settings, $setting_parts)
    );

    $setting_parts = [
      'schema_type' => 'Place',
    ];
    $this->assertEquals(
      'This is a place.',
      $this->schemaTypeManager->getSetting($settings, $setting_parts)
    );
    $this->assertEquals(
      [
        'Place' => 'This is a place.',
        'Thing' => 'This is thing',
      ],
      $this->schemaTypeManager->getSetting($settings, $setting_parts, TRUE)
    );

    // Check getting setting from an indexed array by type and property.
    $settings = ['name'];
    $this->assertTrue(
      $this->schemaTypeManager->getSetting($settings, ['schema_property' => 'name'])
    );
    $this->assertNull(
      $this->schemaTypeManager->getSetting($settings, ['schema_property' => 'not_name'])
    );
  }

}
