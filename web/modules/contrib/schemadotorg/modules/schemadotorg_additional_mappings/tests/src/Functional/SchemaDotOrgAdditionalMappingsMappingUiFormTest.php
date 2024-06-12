<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org additional mappings UI form.
 *
 * @covers \Drupal\schemadotorg_additional_mappings\SchemaDotOrgAdditionalMappingsManager::mappingDefaultsAlter
 * @covers \Drupal\schemadotorg_additional_mappings\SchemaDotOrgAdditionalMappingsManager::mappingFormAlter
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalMappingsMappingUiFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_ui', 'schemadotorg_additional_mappings'];

  /**
   * Test Schema.org additional mappings UI form.
   */
  public function testMappingUi(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    $schema_properties = [
      'dateCreated',
      'dateModified',
      'inLanguage',
      'name',
      'primaryImageOfPage',
      'relatedLink',
      'significantLink',
    ];

    /* ********************************************************************** */

    // Check that the Schema.org WebPage mapping is not available before the
    // Schema.org type is selected.
    $this->drupalGet('admin/structure/types/schemadotorg');
    $assert->elementNotExists('css', '#edit-mapping-additional-mappings');

    // Get the Recipe Schema.org mapping form.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Recipe']]);

    // Check that the Schema.org WebPage mapping settings exists and open.
    $assert->elementExists('css', '#edit-mapping-additional-mappings');
    $assert->checkboxChecked('mapping[additional_mappings][WebPage][schema_type]');

    // Check that WebPage Schema.org properties are checked by default.
    foreach ($schema_properties as $schema_property) {
      $assert->checkboxChecked('mapping[additional_mappings][WebPage][schema_properties][' . $schema_property . ']');
    }

    // Create the Recipe Schema.org mapping form.
    $this->submitForm([], 'Save');
    $mapping = SchemaDotOrgMapping::load('node.recipe');

    // Check that Schema.org WebPage mapping is saved as a third party setting.
    $expected_schema_properties = [
      'created' => 'dateCreated',
      'changed' => 'dateModified',
      'langcode' => 'inLanguage',
      'title' => 'name',
      'schema_image' => 'primaryImageOfPage',
      'schema_related_link' => 'relatedLink',
      'schema_significant_link' => 'significantLink',
    ];
    $this->assertEquals(
      $expected_schema_properties,
      $mapping->getAdditionalMapping('WebPage')['schema_properties']
    );

    $expected_schema_properties = [
      'schema_cooking_method' => 'cookingMethod',
      'schema_cook_time' => 'cookTime',
      'created' => 'dateCreated',
      'changed' => 'dateModified',
      'schema_image' => 'image',
      'langcode' => 'inLanguage',
      'schema_is_family_friendly' => 'isFamilyFriendly',
      'title' => 'name',
      'schema_nutrition' => 'nutrition',
      'schema_prep_time' => 'prepTime',
      'schema_recipe_category' => 'recipeCategory',
      'schema_recipe_cuisine' => 'recipeCuisine',
      'schema_recipe_ingredient' => 'recipeIngredient',
      'schema_recipe_instructions' => 'recipeInstructions',
      'schema_recipe_yield' => 'recipeYield',
      'schema_suitable_for_diet' => 'suitableForDiet',
      'body' => 'text',
      'schema_total_time' => 'totalTime',
    ];
    $this->assertEquals(
      $expected_schema_properties,
      $mapping->getSchemaProperties()
    );

    // Check that the expected WebPage and Recipe fields were created.
    $field_config = FieldConfig::loadMultiple();
    $expected_field_names = [
       'node.recipe.body',
       'node.recipe.schema_cook_time',
       'node.recipe.schema_cooking_method',
       'node.recipe.schema_image',
       'node.recipe.schema_is_family_friendly',
       'node.recipe.schema_nutrition',
       'node.recipe.schema_prep_time',
       'node.recipe.schema_recipe_category',
       'node.recipe.schema_recipe_cuisine',
       'node.recipe.schema_recipe_ingredient',
       'node.recipe.schema_recipe_instructions',
       'node.recipe.schema_recipe_yield',
       'node.recipe.schema_related_link',
       'node.recipe.schema_significant_link',
       'node.recipe.schema_suitable_for_diet',
       'node.recipe.schema_total_time',
    ];
    $this->assertEquals($expected_field_names, array_keys($field_config));

    // Check that only nodes can include a WebPage mapping.
    $this->drupalGet('admin/config/people/accounts/schemadotorg');
    $assert->elementNotExists('css', '#edit-mapping-additional-mappings');

    // Check that WebPage can't be mapped WebPage.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'WebPage']]);
    $assert->elementNotExists('css', '#edit-mapping-additional-mappings');

    // Check that 'additional_mappings' properties can be cleared.
    $this->drupalGet('admin/structure/types/manage/recipe/schemadotorg');
    $edit = [];
    foreach ($schema_properties as $schema_property) {
      $edit['mapping[additional_mappings][WebPage][schema_properties][' . $schema_property . ']'] = FALSE;
    }
    $this->submitForm($edit, 'Save');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->mappingStorage->loadUnchanged('node.recipe');
    $additional_mapping = $mapping->getAdditionalMapping('WebPage');
    $expected_additional_mapping = [
      'schema_type' => 'WebPage',
      'schema_properties' => [],
    ];
    $this->assertEquals($expected_additional_mapping, $additional_mapping);

    // Check that 'additional_mappings' can be cleared.
    $this->drupalGet('admin/structure/types/manage/recipe/schemadotorg');
    $edit = ['mapping[additional_mappings][WebPage][schema_type]' => FALSE];
    $this->submitForm($edit, 'Save');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->mappingStorage->loadUnchanged('node.recipe');
    $this->assertNull($mapping->getAdditionalMapping('WebPage'));
  }

}
