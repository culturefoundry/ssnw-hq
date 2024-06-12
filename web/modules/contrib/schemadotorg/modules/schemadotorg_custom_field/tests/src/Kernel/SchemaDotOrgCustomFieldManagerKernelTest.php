<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_custom_field\Kernel;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org custom field manager.
 *
 * @covers \Drupal\schemadotorg_custom_field\SchemaDotOrgCustomFieldDefaultVocabularyManager
 * @group schemadotorg
 */
class SchemaDotOrgCustomFieldManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the custom field module has a schema.
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enabled

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'custom_field',
    'schemadotorg_options',
    'schemadotorg_custom_field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(static::$modules);
  }

  /**
   * Test Schema.org custom field manager.
   */
  public function testManager(): void {
    /* ********************************************************************** */
    // Recipe.
    /* ********************************************************************** */

    $this->createSchemaEntity('node', 'Recipe');

    // Check recipe nutrition custom field storage columns.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = FieldStorageConfig::loadByName('node', 'schema_nutrition');
    $expected_settings = [
      'columns' => [
        'serving_size' => [
          'name' => 'serving_size',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'calories' => [
          'name' => 'calories',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'carbohydrate_content' => [
          'name' => 'carbohydrate_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'cholesterol_content' => [
          'name' => 'cholesterol_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'fat_content' => [
          'name' => 'fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'fiber_content' => [
          'name' => 'fiber_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'protein_content' => [
          'name' => 'protein_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'saturated_fat_content' => [
          'name' => 'saturated_fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'sodium_content' => [
          'name' => 'sodium_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'sugar_content' => [
          'name' => 'sugar_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'trans_fat_content' => [
          'name' => 'trans_fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'unsaturated_fat_content' => [
          'name' => 'unsaturated_fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
      ],
    ];
    $this->assertEquals($expected_settings, $field_storage_config->getSettings());

    // Check recipe nutrition custom field column widget settings.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = FieldConfig::loadByName('node', 'recipe', 'schema_nutrition');
    $settings = $field_config->getSettings();
    $expected_settings_serving_size = [
      'type' => 'text',
      'widget_settings' => [
        'label' => 'Serving size',
        'settings' => [
          'description' => 'The serving size, in terms of the number of volume or mass.',
          'size' => 60,
          'placeholder' => '',
          'maxlength' => 255,
          'maxlength_js' => FALSE,
          'description_display' => 'after',
          'required' => FALSE,
          'prefix' => '',
          'suffix' => '',
        ],
      ],
      'check_empty' => FALSE,
      'weight' => 0,
    ];
    $this->assertEquals($expected_settings_serving_size, $settings['field_settings']['serving_size']);
    $expected_settings_calories = [
      'type' => 'integer',
      'widget_settings' => [
        'label' => 'Calories',
        'settings' => [
          'description' => 'The number of calories.',
          'description_display' => 'after',
          'placeholder' => '',
          'min' => NULL,
          'max' => NULL,
          'prefix' => '',
          'suffix' => ' calories',
          'required' => FALSE,
        ],
      ],
      'check_empty' => FALSE,
      'weight' => 1,
    ];
    $this->assertEquals($expected_settings_calories, $settings['field_settings']['calories']);

    // Check custom field form display.
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity_form_display */
    $entity_form_display = EntityFormDisplay::load('node.recipe.default');
    $components = $entity_form_display->getComponents();
    $expected_component = [
      'type' => 'custom_stacked',
      'weight' => 150,
      'region' => 'content',
      'settings' => [
        'label' => TRUE,
        'wrapper' => 'fieldset',
        'open' => TRUE,
      ],
      'third_party_settings' => [],
    ];
    $this->assertEquals($expected_component, $components['schema_nutrition']);

    // Check custom field view display.
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $entity_form_display */
    $entity_view_display = EntityViewDisplay::load('node.recipe.default');
    $components = $entity_view_display->getComponents();
    $expected_component = [
      'type' => 'custom_formatter',
      'label' => 'above',
      'settings' => [
        'fields' => [
          'calories' => [
            'format_type' => 'number_integer',
            'formatter_settings' => ['prefix_suffix' => TRUE],
          ],
          'carbohydrate_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'cholesterol_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'fat_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'fiber_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'protein_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'saturated_fat_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'sodium_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'sugar_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'trans_fat_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
          'unsaturated_fat_content' => [
            'format_type' => 'number_integer',
            'formatter_settings' => [
              'prefix_suffix' => TRUE,
            ],
          ],
        ],
      ],
      'third_party_settings' => [],
      'weight' => 150,
      'region' => 'content',
    ];
    $this->assertEquals($expected_component, $components['schema_nutrition']);

    /* ********************************************************************** */
    // FAQPage.
    /* ********************************************************************** */

    $this->createSchemaEntity('node', 'FAQPage');

    // Check FAQ page main entity custom field storage columns.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = FieldStorageConfig::loadByName('node', 'schema_faq_main_entity');
    $expected_settings = [
      'columns' => [
        'name' => [
          'name' => 'name',
          'type' => 'string_long',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'accepted_answer' => [
          'name' => 'accepted_answer',
          'type' => 'string_long',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
      ],
    ];
    $this->assertEquals($expected_settings, $field_storage_config->getSettings());

    // Check faq page main entity custom field column widget settings.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = FieldConfig::loadByName('node', 'faq', 'schema_faq_main_entity');
    $settings = $field_config->getSettings();
    $expected_settings_serving_size = [
      'type' => 'textarea',
      'widget_settings' => [
        'label' => 'Question',
        'settings' => [
          'description' => 'The name of the item.',
          'rows' => 5,
          'placeholder' => '',
          'maxlength' => '',
          'maxlength_js' => FALSE,
          'formatted' => TRUE,
          'default_format' => 'basic_html',
          'format' => [
            'guidelines' => FALSE,
            'help' => FALSE,
          ],
          'description_display' => 'after',
          'required' => FALSE,
        ],
      ],
      'check_empty' => FALSE,
      'weight' => 0,
    ];
    $this->assertEquals($expected_settings_serving_size, $settings['field_settings']['name']);

    /* ********************************************************************** */
    // DietarySupplement.
    /* ********************************************************************** */

    $this->createSchemaEntity('node', 'DietarySupplement');

    // Check dietary supplement maximum intake custom field storage columns.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = FieldStorageConfig::loadByName('node', 'schema_max_intake');
    $expected_settings = [
      'columns' => [
        'target_population' => [
          'name' => 'target_population',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'dose_value' => [
          'name' => 'dose_value',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'dose_unit' => [
          'name' => 'dose_unit',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
        'frequency' => [
          'name' => 'frequency',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
        ],
      ],
    ];
    $this->assertEquals($expected_settings, $field_storage_config->getSettings());

    // Check dietary supplement maximum intake custom field column widget settings.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = FieldConfig::loadByName('node', 'dietary_supplement', 'schema_max_intake');
    $settings = $field_config->getSettings();
    $expected_settings_frequency = [
      'type' => 'select',
      'weight' => 3,
      'check_empty' => FALSE,
      'widget_settings' => [
        'label' => 'Frequency',
        'settings' => [
          'description' => 'How often the dose is taken, e.g. \'daily\'.',
          'description_display' => 'after',
          'required' => FALSE,
          'empty_option' => '- Select -',
          'allowed_values' => [
            ['key' => 'Daily', 'value' => 'Daily'],
            ['key' => '2 times a day', 'value' => '2 times a day'],
            ['key' => '3 times a day', 'value' => '3 times a day'],
            ['key' => '4 times a day', 'value' => '4 times a day'],
            ['key' => '5 times a day', 'value' => '5 times a day'],
            ['key' => 'Every 3 hours', 'value' => 'Every 3 hours'],
            ['key' => 'Every 6 hours', 'value' => 'Every 6 hours'],
            ['key' => 'Every 8 hours', 'value' => 'Every 8 hours'],
            ['key' => 'Every 12 hours', 'value' => 'Every 12 hours'],
            ['key' => 'Every 24 hours', 'value' => 'Every 24 hours'],
            ['key' => 'Bedtime', 'value' => 'Bedtime'],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected_settings_frequency, $settings['field_settings']['frequency']);
  }

}
