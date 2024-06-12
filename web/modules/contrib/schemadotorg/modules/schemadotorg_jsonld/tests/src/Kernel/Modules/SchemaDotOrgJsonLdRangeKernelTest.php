<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Kernel\Modules;

use Drupal\node\Entity\Node;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD range.module integration.
 *
 * @covers range_schemadotorg_jsonld_schema_property_alter()
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdRangeKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'range',
    'schemadotorg_jsonld',
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
    $this->installConfig(['schemadotorg_jsonld']);
    $this->builder = $this->container->get('schemadotorg_jsonld.builder');
  }

  /**
   * Test Schema.org range JSON-LD.
   */
  public function testJsonLdRange(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->createSchemaEntity('node', 'JobPosting');

    // Job node.
    $job_node = Node::create([
      'type' => 'job_posting',
      'title' => 'Some job',
      'schema_estimated_salary' => [
        'from' => 100000,
        'to' => 200000,
      ],
    ]);
    $job_node->save();

    $expected_value = [
      '@type' => 'JobPosting',
      '@url' => $job_node->toUrl()->setAbsolute()->toString(),
      'title' => 'Some job',
      'estimatedSalary' => [
        '@type' => 'MonetaryAmount',
        'minValue' => 100000,
        'maxValue' => 200000,
        'currency' => 'USD',
      ],
    ];
    $actual_value = $this->builder->buildEntity($job_node);
    $this->assertEquals($expected_value, $actual_value);
  }

}
