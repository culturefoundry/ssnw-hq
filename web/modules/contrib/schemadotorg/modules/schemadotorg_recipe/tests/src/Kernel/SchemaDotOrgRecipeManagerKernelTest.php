<?php

declare(strict_types=1);

namespace Drupal\Tests\recipe\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\schemadotorg_recipe\SchemaDotOrgRecipeManagerInterface;

/**
 * Tests the functionality of the Schema.org recipe manager.
 *
 * @covers \Drupal\recipe\SchemaDotOrgTaxonomyPropertyVocabularyManagerTest;
 * @group schemadotorg
 */
class SchemaDotOrgRecipeManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_recipe',
  ];

  /**
   * The Schema.org recipe manager service.
   */
  protected SchemaDotOrgRecipeManagerInterface $schemaRecipeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_recipe']);
    $this->installEntityDependencies('media');
    $this->installEntityDependencies('node');
    $this->schemaRecipeManager = $this->container->get('schemadotorg_recipe.manager');
  }

  /**
   * Test Schema.org recipe manager.
   */
  public function testManager(): void {
    $root = $this->container->getParameter('app.root');
    $module_path = $this->getModulePath('schemadotorg');

    // Check determining if Schema.org Blueprints Recipe exists.
    $this->assertFalse($this->schemaRecipeManager->isRecipe('missing_recipe'));
    $this->assertTrue($this->schemaRecipeManager->isRecipe('schemadotorg_recipe_test'));

    // Check getting a list of Schema.org recipes.
    $recipes = $this->schemaRecipeManager->getRecipes();
    $this->assertArrayHasKey('schemadotorg_recipe_test', $recipes);
    $recipes = $this->schemaRecipeManager->getRecipes(TRUE);
    $this->assertArrayNotHasKey('schemadotorg_recipe_test', $recipes);

    // Check getting a Schema.org recipe's schemadotorg specific data.
    $recipe = $this->schemaRecipeManager->getRecipe('schemadotorg_recipe_test');
    $expected = [
      'directory' => "$root/$module_path/tests/recipes/schemadotorg_recipe_test",
      'path' => "$root/$module_path/tests/recipes/schemadotorg_recipe_test/recipe.yml",
      'types' => [
        'node:event:Event' => [],
        'node:custom_thing:Thing' => [
          'entity' => ['label' => 'Something'],
          'properties' => [
            'name' => ['required' => TRUE],
            'description' => TRUE,
            'image' => TRUE,
            'custom' => [
              'name' => 'custom',
              'type' => 'string',
              'label' => 'Custom',
              'group' => 'general',
              'group_field_weight' => -100,
              'default_value' => [['value' => 'Custom value']],
            ],
          ],
        ],
      ],
      'applicable' => TRUE,
      'applied' => FALSE,
    ];
    $this->assertEquals($expected, $recipe['schemadotorg']);

    // Check getting a module's Schema.org Blueprints recipe settings.
    $settings = $this->schemaRecipeManager->getRecipeSettings('schemadotorg_recipe_test');
    $this->assertEquals('Something', $settings['schemadotorg']['types']['node:custom_thing:Thing']['entity']['label']);
    $this->assertEquals('_add_', $settings['schemadotorg']['types']['node:custom_thing:Thing']['properties']['name']['name']);
    $this->assertEquals('_add_', $settings['schemadotorg']['types']['node:custom_thing:Thing']['properties']['description']['name']);
    $this->assertEquals('_add_', $settings['schemadotorg']['types']['node:custom_thing:Thing']['properties']['image']['name']);
  }

}
