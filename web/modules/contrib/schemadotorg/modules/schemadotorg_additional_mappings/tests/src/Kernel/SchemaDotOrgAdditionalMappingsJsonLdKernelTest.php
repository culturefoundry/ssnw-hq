<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org additional mappings JSON-LD.
 *
 * @covers schemadotorg_additional_mappings_schemadotorg_jsonld_alter
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalMappingsJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_jsonld_breadcrumb',
    'schemadotorg_additional_mappings',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_additional_mappings']);
    $this->manager = $this->container->get('schemadotorg_jsonld.manager');
    $this->builder = $this->container->get('schemadotorg_jsonld.builder');

    module_set_weight('schemadotorg_additional_mappings', 10);
    module_set_weight('schemadotorg_jsonld_breadcrumb', 11);
  }

  /**
   * Test Schema.org WebPage.
   */
  public function testWebPage(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    /* ********************************************************************** */

    $this->createSchemaEntity('node', 'Recipe');

    $recipe_node = Node::create([
      'type' => 'recipe',
      'title' => 'Some recipe',
    ]);
    $recipe_node->save();

    // Check that the JSON-LD WebPage is built with the
    // mainEntity and breadcrumb properties.
    $route_match = $this->manager->getEntityRouteMatch($recipe_node);
    $jsonld = $this->builder->build($route_match);
    $this->assertEquals('WebPage', $jsonld['@type']);
    $this->assertEquals('Recipe', $jsonld['mainEntity']['@type']);
    $this->assertEquals('BreadcrumbList', $jsonld['breadcrumb']['@type']);

    /* ********************************************************************** */

    $this->createSchemaEntity('node', 'MedicalStudy');

    $study_node = Node::create([
      'type' => 'medical_study',
      'title' => 'Medical study',
    ]);
    $study_node->save();

    // Check that JSON-LD is built with MedicalStudy and ResearchProject
    // as the @type.
    $jsonld = $this->builder->buildEntity($study_node);
    $this->assertEquals(['MedicalStudy', 'ResearchProject'], $jsonld['@type']);

    // Check that JSON-LD is built as as WebPage with MedicalStudy and ResearchProject
    // as the @type.
    $route_match = $this->manager->getEntityRouteMatch($study_node);
    $jsonld = $this->builder->build($route_match);
    $this->assertEquals('MedicalWebPage', $jsonld['@type']);
    $this->assertEquals(['MedicalStudy', 'ResearchProject'], $jsonld['mainEntity']['@type']);
  }

}
