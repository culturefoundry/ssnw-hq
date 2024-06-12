<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_address\Kernel;

use Drupal\node\Entity\Node;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org address module JSON-LD integration.
 *
 * @covers address_schemadotorg_jsonld_schema_property_alter(()
 * @group schemadotorg
 */
class SchemaDotOrgAddressJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'address',
    'schemadotorg_address',
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

    $this->installConfig(['schemadotorg_address']);

    \Drupal::moduleHandler()->loadInclude('schemadotorg_address', 'install');
    schemadotorg_address_install(FALSE);
  }

  /**
   * Test Schema.org address JSON-LD.
   */
  public function testJsonLdAddress(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->createSchemaEntity('node', 'Place');

    // Place node.
    $place_node = Node::create([
      'type' => 'place',
      'title' => 'Some place',
      'schema_address' => [
        'country_code' => 'AD',
        'locality' => 'Canillo',
        'postal_code' => 'AD500',
        'address_line1' => 'C. Prat de la Creu, 62-64',
      ],
    ]);
    $place_node->save();

    $expected_value = [
      '@type' => 'Place',
      '@url' => $place_node->toUrl()->setAbsolute()->toString(),
      'name' => 'Some place',
      'address' => [
        '@type' => 'PostalAddress',
        'addressCountry' => 'AD',
        'addressLocality' => 'Canillo',
        'postalCode' => 'AD500',
        'streetAddress' => 'C. Prat de la Creu, 62-64',
      ],
    ];
    $actual_value = $this->builder->buildEntity($place_node);
    $this->assertEquals($expected_value, $actual_value);
  }

}
