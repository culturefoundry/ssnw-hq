<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org WebPage support.
 *
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalMappingsKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_additional_mappings'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_additional_mappings']);
  }

  /**
   * Test Schema.org additional mappings support.
   */
  public function testAdditionalMappings(): void {
    // Check getting Schema.org mapping entity default values.
    $mappings_default = $this->mappingManager->getMappingDefaults('node', NULL, 'Recipe');
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => 'WebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'title' => 'name',
          'schema_image' => 'primaryImageOfPage',
          'schema_related_link' => 'relatedLink',
          'schema_significant_link' => 'significantLink',
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_default['additional_mappings']);

    // Check getting Schema.org mapping entity default values with
    // schema properties that are disabled.
    $defaults = [
      'additional_mappings' => [
        'WebPage' => [
          'schema_type' => 'WebPage',
          'schema_properties' => [
            'schema_image' => FALSE,
            'schema_related_link' => FALSE,
            'schema_significant_link' => FALSE,
          ],
        ],
      ],
    ];
    $mappings_default = $this->mappingManager->getMappingDefaults('node', NULL, 'Recipe', $defaults);
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => 'WebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'title' => 'name',
          'schema_image' => FALSE,
          'schema_related_link' => FALSE,
          'schema_significant_link' => FALSE,
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_default['additional_mappings']);

    // Check getting Schema.org mapping entity default values.
    $mappings_default = $this->mappingManager->getMappingDefaults('node', NULL, 'HealthTopicContent');
    $expected_additional_mappings = [
      'MedicalWebPage' => [
        'schema_type' => 'MedicalWebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'title' => 'name',
          'schema_image' => 'primaryImageOfPage',
          'schema_related_link' => 'relatedLink',
          'schema_significant_link' => 'significantLink',
          'schema_medical_audience' => 'medicalAudience',
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_default['additional_mappings']);

    // Check getting Schema.org mapping entity default values.
    $mappings_default = $this->mappingManager->getMappingDefaults('node', NULL, 'MedicalStudy');
    $expected_additional_mappings = [
      'ResearchProject' => [
        'schema_type' => 'ResearchProject',
        'schema_properties' => [
          'schema_member' => 'member',
        ],
      ],
      'MedicalWebPage' => [
        'schema_type' => 'MedicalWebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'title' => 'name',
          'schema_image' => 'primaryImageOfPage',
          'schema_medical_audience' => 'medicalAudience',
          'schema_related_link' => 'relatedLink',
          'schema_significant_link' => 'significantLink',
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_default['additional_mappings']);

    // Check getting Schema.org mapping entity default values.
    $mappings_default = $this->mappingManager->getMappingDefaults('node', NULL, 'Drug');
    $expected_additional_mappings = [
      'CreativeWork' => [
        'schema_type' => 'CreativeWork',
        'schema_properties' => [
          'schema_citation' => 'citation',
          'schema_is_based_on' => 'isBasedOn',
          'schema_license' => 'license',
        ],
      ],
      'MedicalWebPage' => [
        'schema_type' => 'MedicalWebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'schema_medical_audience' => 'medicalAudience',
          'title' => 'name',
          'schema_image' => 'primaryImageOfPage',
          'schema_related_link' => 'relatedLink',
          'schema_significant_link' => 'significantLink',
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_default['additional_mappings']);

    // Check the additional mappings after creating a Schema.org type.
    $mapping = $this->createSchemaEntity('node', 'Recipe');
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => 'WebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'title' => 'name',
          'schema_image' => 'primaryImageOfPage',
          'schema_related_link' => 'relatedLink',
          'schema_significant_link' => 'significantLink',
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mapping->getAdditionalMappings());

    // Check that the expected Recipe and WebPage fields are created.
    $expected_configs = [
      'node.recipe.body',
      'node.recipe.schema_cooking_method',
      'node.recipe.schema_cook_time',
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
    $this->assertEquals($expected_configs, array_keys(FieldConfig::loadMultiple()));
  }

}
