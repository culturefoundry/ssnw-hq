<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Kernel;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
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
    'schemadotorg_paragraphs',
    'schemadotorg_jsonld_custom',
    'schemadotorg_jsonld_breadcrumb',
    'schemadotorg_additional_mappings',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');

    $this->installConfig(['schemadotorg_additional_mappings', 'schemadotorg_jsonld_custom']);
    $this->manager = $this->container->get('schemadotorg_jsonld.manager');
    $this->builder = $this->container->get('schemadotorg_jsonld.builder');

    module_set_weight('schemadotorg_additional_mappings', 10);
    module_set_weight('schemadotorg_jsonld_breadcrumb', 11);
  }

  /**
   * Test Schema.org WebPage.
   */
  public function testJsonLd(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    /* ********************************************************************** */
    // WebPage.
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
    // ResearchProject.
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

    /* ********************************************************************** */
    // PronounceableText.
    /* ********************************************************************** */

    $this->createSchemaEntity('node', 'Substance');

    $study_node = Node::create([
      'type' => 'substance',
      'title' => 'Substance',
      'schema_phonetic_text' => '[substance]',
    ]);
    $study_node->save();

    // Check that JSON-LD form Substance includes
    // https://schema.org/PronounceableText as the https://schema.org/name.
    $jsonld = $this->builder->buildEntity($study_node);
    $expected_jsonld = [
      '@type' => [
        'Substance',
        'CreativeWork',
      ],
      '@url' => $study_node->toUrl()->setAbsolute()->toString(),
      'name' => [
        '@type' => 'PronounceableText',
        'inLanguage' => 'en',
        'phoneticText' => '[substance]',
        'speechToTextMarkup' => 'GAEP',
        'textValue' => 'Substance',
      ],
    ];
    $this->assertEquals($expected_jsonld, $jsonld);

    /* ********************************************************************** */
    // Quotation.
    /* ********************************************************************** */

    $this->createSchemaEntity('paragraph', 'Quotation');

    $quotation = Paragraph::create([
      'type' => 'quotation',
      'schema_text' => ['value' => 'Some quote', 'format' => 'empty_format'],
      'schema_name' => 'Some person',
      'schema_job_title' => 'Some job title',
    ]);
    $quotation->save();

    $jsonld = $this->builder->buildEntity($quotation);
    $expected_jsonld = [
      '@type' => 'Quotation',
      'inLanguage' => 'en',
      'text' => '',
      'creator' => [
        '@type' => 'Person',
        'name' => 'Some person',
        'jobTitle' => 'Some job title',
      ],
    ];
    $this->assertEquals($expected_jsonld, $jsonld);
  }

}
