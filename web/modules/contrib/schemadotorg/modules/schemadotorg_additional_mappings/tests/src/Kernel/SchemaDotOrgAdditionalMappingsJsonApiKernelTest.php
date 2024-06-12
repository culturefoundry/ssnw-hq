<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Kernel;

use Drupal\jsonapi_extras\Entity\JsonapiResourceConfig;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\Tests\schemadotorg_jsonapi\Kernel\SchemaDotOrgJsonApiKernelTestBase;

/**
 * Tests the functionality of the Schema.org additional mappings JSON:API support.
 *
 * @covers schemadotorg_additional_mappings_jsonapi_resource_config_presave()
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalMappingsJsonApiKernelTest extends SchemaDotOrgJsonApiKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_additional_mappings'];

  /**
   * The Schema.org mapping manager.
   */
  protected SchemaDotOrgMappingManagerInterface $mappingManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_additional_mappings']);
  }

  /**
   * Test Schema.org Additional Mappings JSON:API support.
   */
  public function testWebPageJsonApi(): void {
    $this->createSchemaEntity('node', 'Recipe');

    // Check that a WebPage's JSON:API is configured as expected.
    /** @var \Drupal\jsonapi_extras\Entity\JsonapiResourceConfig $resource */
    $resource = JsonapiResourceConfig::load('node--recipe');
    $resource_fields = $resource->get('resourceFields');
    $expected_fields = [
      'body' => 'text',
      'changed' => 'changed',
      'created' => 'created',
      'default_langcode' => 'default_langcode',
      'langcode' => 'langcode',
      'nid' => 'nid',
      'promote' => 'promote',
      'schema_cook_time' => 'cook_time',
      'schema_cooking_method' => 'cooking_method',
      'schema_image' => 'image',
      'schema_is_family_friendly' => 'is_family_friendly',
      'schema_nutrition' => 'nutrition',
      'schema_prep_time' => 'prep_time',
      'schema_recipe_category' => 'recipe_category',
      'schema_recipe_cuisine' => 'recipe_cuisine',
      'schema_recipe_ingredient' => 'recipe_ingredient',
      'schema_recipe_instructions' => 'recipe_instructions',
      'schema_recipe_yield' => 'recipe_yield',
      'schema_suitable_for_diet' => 'suitable_for_diet',
      'schema_total_time' => 'total_time',
      'status' => 'status',
      'sticky' => 'sticky',
      'title' => 'title',
      'type' => 'type',
      'uid' => 'uid',
      'uuid' => 'uuid',
    ];
    $actual_fields = [];
    ksort($resource_fields);
    foreach ($resource_fields as $resource_field) {
      if ($resource_field['disabled']) {
        continue;
      }
      $actual_fields[$resource_field['fieldName']] = $resource_field['publicName'];
    }
    $this->assertEquals($expected_fields, $actual_fields);
  }

}
