<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_taxonomy\Kernel;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org taxonomy JSON-LD.
 *
 * @covers schemadotorg_taxonomy_schemadotorg_jsonld_schema_property_alter();
 * @group schemadotorg
 */
class SchemaDotOrgTaxonomyJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'taxonomy',
    'schemadotorg_taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');
    $this->installConfig(['schemadotorg_taxonomy']);
  }

  /**
   * Test Schema.org taxonomy JSON-LD.
   */
  public function testJsonLd(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    // Create a Schema.org Recipe which automatically creates
    // a 'recipe_category' vocabulary.
    // @se e\Drupal\schemadotorg_taxonomy\SchemaDotOrgTaxonomyPropertyVocabularyManager::propertyFieldAlter
    $this->createSchemaEntity('node', 'Recipe');

    // Create a recipe with a recipe category.
    $term = Term::create([
      'vid' => 'recipe_category',
      'name' => 'My recipe category',
    ]);
    $term->save();
    $node = Node::create([
      'type' => 'recipe',
      'title' => 'My recipe',
      'schema_recipe_category' => [
        'target_id' => $term->id(),
      ],
    ]);
    $node->save();

    // Check that the term's name is used as the JSON-LD item value.
    // @see schemadotorg_taxonomy_schemadotorg_jsonld_schema_property_alter()
    $json_ld = $this->builder->buildEntity($node);
    $this->assertEquals('My recipe', $json_ld['name']);
    $this->assertEquals(['My recipe category'], $json_ld['recipeCategory']);
  }

}
