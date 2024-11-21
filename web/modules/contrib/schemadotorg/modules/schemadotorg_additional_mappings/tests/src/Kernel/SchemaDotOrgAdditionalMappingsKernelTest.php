<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_mappings\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\field\Entity\FieldConfig;

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
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'Recipe');
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => 'WebPage',
        'schema_properties' => [
          'dateCreated' => TRUE,
          'dateModified' => TRUE,
          'inLanguage' => TRUE,
          'name' => TRUE,
          'primaryImageOfPage' => TRUE,
          'relatedLink' => TRUE,
          'significantLink' => TRUE,
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_defaults['additional_mappings']);

    // Check getting Schema.org mapping entity default values with
    // schema properties that are disabled.
    $defaults = [
      'additional_mappings' => [
        'WebPage' => [
          'schema_type' => 'WebPage',
          'schema_properties' => [
            'primaryImageOfPage' => FALSE,
            'relatedLink' => FALSE,
            'significantLink' => FALSE,
          ],
        ],
      ],
    ];
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'Recipe', $defaults);
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => 'WebPage',
        'schema_properties' => [
          'dateCreated' => TRUE,
          'dateModified' => TRUE,
          'inLanguage' => TRUE,
          'name' => TRUE,
          'primaryImageOfPage' => FALSE,
          'relatedLink' => FALSE,
          'significantLink' => FALSE,
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_defaults['additional_mappings']);

    // Check getting Schema.org mapping entity default values.
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'HealthTopicContent');
    $expected_additional_mappings = [
      'MedicalWebPage' => [
        'schema_type' => 'MedicalWebPage',
        'schema_properties' => [
          'dateCreated' => TRUE,
          'dateModified' => TRUE,
          'inLanguage' => TRUE,
          'name' => TRUE,
          'primaryImageOfPage' => TRUE,
          'relatedLink' => TRUE,
          'significantLink' => TRUE,
          'medicalAudience' => TRUE,
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_defaults['additional_mappings']);

    // Check getting Schema.org mapping entity default values.
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'MedicalStudy');
    $expected_additional_mappings = [
      'ResearchProject' => [
        'schema_type' => 'ResearchProject',
        'schema_properties' => [
          'member' => TRUE,
        ],
      ],
      'MedicalWebPage' => [
        'schema_type' => 'MedicalWebPage',
        'schema_properties' => [
          'dateCreated' => TRUE,
          'dateModified' => TRUE,
          'inLanguage' => TRUE,
          'name' => TRUE,
          'primaryImageOfPage' => TRUE,
          'medicalAudience' => TRUE,
          'relatedLink' => TRUE,
          'significantLink' => TRUE,
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_defaults['additional_mappings']);

    // Check getting Schema.org mapping entity default values.
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'Drug');
    $expected_additional_mappings = [
      'CreativeWork' => [
        'schema_type' => 'CreativeWork',
        'schema_properties' => [
          'citation' => TRUE,
          'isBasedOn' => TRUE,
          'license' => TRUE,
        ],
      ],
      'PronounceableText' => [
        'schema_type' => 'PronounceableText',
        'schema_properties' => [
          'inLanguage' => TRUE,
          'phoneticText' => TRUE,
          'textValue' => TRUE,
        ],
      ],
      'MedicalWebPage' => [
        'schema_type' => 'MedicalWebPage',
        'schema_properties' => [
          'dateCreated' => TRUE,
          'dateModified' => TRUE,
          'inLanguage' => TRUE,
          'name' => TRUE,
          'primaryImageOfPage' => TRUE,
          'medicalAudience' => TRUE,
          'relatedLink' => TRUE,
          'significantLink' => TRUE,
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_defaults['additional_mappings']);

    // Check the additional mappings after creating a Schema.org type with a
    // customize additional mapping property.
    $defaults = [
      'additional_mappings' => [
        'WebPage' => [
          'schema_type' => 'WebPage',
          'schema_properties' => [
            'relatedLink' => ['label' => 'Links'],
            'primaryImageOfPage' => FALSE,
            'significantLink' => FALSE,
          ],
        ],
      ],
    ];
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'Recipe', $defaults);
    $mapping = $this->mappingManager->saveMapping('node', 'Recipe', $mappings_defaults);
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => 'WebPage',
        'schema_properties' => [
          'created' => 'dateCreated',
          'changed' => 'dateModified',
          'langcode' => 'inLanguage',
          'title' => 'name',
          'schema_related_link' => 'relatedLink',
        ],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mapping->getAdditionalMappings());
    /** @var \Drupal\field\FieldConfigInterface $field_config */
    $field_config = $this->entityTypeManager
      ->getStorage('field_config')
      ->load('node.recipe.schema_related_link');
    $this->assertEquals('Links', $field_config->getLabel());

    // Check that the expected Recipe and WebPage fields are created.
    $expected_configs = [
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
      'node.recipe.schema_suitable_for_diet',
      'node.recipe.schema_text',
      'node.recipe.schema_total_time',
    ];
    $this->assertEquals($expected_configs, array_keys(FieldConfig::loadMultiple()));

    // Check getting Schema.org mapping entity default values with
    // schema type that are disabled.
    $defaults = [
      'additional_mappings' => [
        'WebPage' => FALSE,
      ],
    ];
    $mappings_defaults = $this->mappingManager->getMappingDefaults('node', NULL, 'Article', $defaults);
    $expected_additional_mappings = [
      'WebPage' => [
        'schema_type' => NULL,
        'schema_properties' => [],
      ],
    ];
    $this->assertEquals($expected_additional_mappings, $mappings_defaults['additional_mappings']);
    $mapping = $this->mappingManager->saveMapping('node', 'Article', $mappings_defaults);
    $this->assertNull($mapping->getAdditionalMapping('Article'));
  }

}
