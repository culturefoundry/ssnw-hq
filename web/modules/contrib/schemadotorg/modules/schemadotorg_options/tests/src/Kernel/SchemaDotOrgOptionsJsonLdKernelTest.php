<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_webpage\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org Options JSON-LD.
 *
 * @covers schemadotorg_options_schemadotorg_jsonld_schema_property_alter
 * @group schemadotorg
 */
class SchemaDotOrgOptionsJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_options',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_options']);
    $this->manager = $this->container->get('schemadotorg_jsonld.manager');
  }

  /**
   * Test Schema.org Options.
   */
  public function testOptions(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->createSchemaEntity('node', 'SpecialAnnouncement');

    $node = Node::create([
      'type' => 'special_announcement',
      'title' => 'Some announcement',
      'schema_category' => 'emergency',
    ]);
    $node->save();

    /* ********************************************************************** */

    // Check that the JSON-LD for an category list element displays
    // the WikiData URI.
    $jsonld = $this->builder->buildEntity($node);
    $this->assertEquals('SpecialAnnouncement', $jsonld['@type']);
    $this->assertEquals('https://www.wikidata.org/wiki/Q5070802', $jsonld['category']);
  }

}
