<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_physical\Kernel;

use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\physical\LengthUnit;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;

/**
 * Tests the functionality of the Schema.org Physical module JSON-LD integration.
 *
 * @covers schemadotorg_physical_schemadotorg_jsonld_schema_property_alter(()
 * @group schemadotorg
 */
class SchemaDotOrgPhysicalJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'physical',
    'schemadotorg_physical',
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

    \Drupal::moduleHandler()->loadInclude('schemadotorg_physical', 'install');
    schemadotorg_physical_install(FALSE);
  }

  /**
   * Test Schema.org Physical JSON-LD.
   */
  public function testJsonLdPhysical(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.size.type', 'physical_dimensions')
      ->save();
    $this->appendSchemaTypeDefaultProperties('IndividualProduct', ['size']);
    $this->createSchemaEntity('node', 'IndividualProduct');

    // Create a product node.
    $product_node = Node::create([
      'type' => 'individual_product',
      'title' => 'Individual product',
      'schema_depth' => [['number' => '100', 'unit' => LengthUnit::MILLIMETER]],
      'schema_height' => [['number' => '200', 'unit' => LengthUnit::MILLIMETER]],
      'schema_width' => [['number' => '300', 'unit' => LengthUnit::MILLIMETER]],
      'schema_size' => [['length' => '10', 'height' => '10', 'width' => '10', 'unit' => LengthUnit::MILLIMETER]],
    ]);
    $product_node->save();
    $product_jsonld = $this->builder->buildEntity($product_node);

    // Check depth JSON-LD.
    $expected_depth = [
      '@type' => 'QuantitativeValue',
      'unitText' => LengthUnit::MILLIMETER,
      'value' => '100',
    ];
    $this->assertEquals($expected_depth, $product_jsonld['depth']);

    // Check height JSON-LD.
    $expected_height = [
      '@type' => 'QuantitativeValue',
      'unitText' => LengthUnit::MILLIMETER,
      'value' => '200',
    ];
    $this->assertEquals($expected_height, $product_jsonld['height']);

    // Check width JSON-LD.
    $expected_width = [
      '@type' => 'QuantitativeValue',
      'unitText' => LengthUnit::MILLIMETER,
      'value' => '300',
    ];
    $this->assertEquals($expected_width, $product_jsonld['width']);

    // Check size JSON-LD.
    $expected_size = [
      [
        '@type' => 'QuantitativeValue',
        'value' => '10',
        'unitText' => 'mm',
        'name' => 'length',
      ],
      [
        '@type' => 'QuantitativeValue',
        'value' => '10',
        'unitText' => 'mm',
        'name' => 'height',
      ],
      [
        '@type' => 'QuantitativeValue',
        'value' => '10',
        'unitText' => 'mm',
        'name' => 'width',
      ],
    ];
    $this->assertEquals($expected_size, $product_jsonld['size']);
  }

}
