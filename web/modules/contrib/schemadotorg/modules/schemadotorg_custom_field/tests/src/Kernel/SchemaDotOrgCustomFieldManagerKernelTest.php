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
    'cer',
    'custom_field',
    'schemadotorg_options',
    'schemadotorg_cer',
    'schemadotorg_custom_field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(static::$modules);

    \Drupal::moduleHandler()->loadInclude('schemadotorg_cer', 'install');
    schemadotorg_cer_install(FALSE);
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
          'datetime_type' => 'datetime',
        ],
        'calories' => [
          'name' => 'calories',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'carbohydrate_content' => [
          'name' => 'carbohydrate_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'cholesterol_content' => [
          'name' => 'cholesterol_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'fat_content' => [
          'name' => 'fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'fiber_content' => [
          'name' => 'fiber_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'protein_content' => [
          'name' => 'protein_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'saturated_fat_content' => [
          'name' => 'saturated_fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'sodium_content' => [
          'name' => 'sodium_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'sugar_content' => [
          'name' => 'sugar_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'trans_fat_content' => [
          'name' => 'trans_fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
        ],
        'unsaturated_fat_content' => [
          'name' => 'unsaturated_fat_content',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '0',
          'datetime_type' => 'datetime',
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
          'min' => 0,
          'max' => 1000,
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
          'datetime_type' => 'datetime',
        ],
        'accepted_answer' => [
          'name' => 'accepted_answer',
          'type' => 'string_long',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
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
          'datetime_type' => 'datetime',
        ],
        'dose_value' => [
          'name' => 'dose_value',
          'type' => 'integer',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
        ],
        'dose_unit' => [
          'name' => 'dose_unit',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
        ],
        'frequency' => [
          'name' => 'frequency',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
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
            ['key' => 'daily', 'value' => 'Daily'],
            ['key' => '2_times_a_day', 'value' => '2 times a day'],
            ['key' => '3_times_a_day', 'value' => '3 times a day'],
            ['key' => '4_times_a_day', 'value' => '4 times a day'],
            ['key' => '5_times_a_day', 'value' => '5 times a day'],
            ['key' => 'every_3_hours', 'value' => 'Every 3 hours'],
            ['key' => 'every_6_hours', 'value' => 'Every 6 hours'],
            ['key' => 'every_8_hours', 'value' => 'Every 8 hours'],
            ['key' => 'every_12_hours', 'value' => 'Every 12 hours'],
            ['key' => 'every_24_hours', 'value' => 'Every 24 hours'],
            ['key' => 'bedtime', 'value' => 'Bedtime'],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected_settings_frequency, $settings['field_settings']['frequency']);

    /* ********************************************************************** */

    // Check Quiz mapping defaults hasPart to custom.
    $mapping_default = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Quiz',
    );
    $this->assertEquals('custom', $mapping_default['properties']['hasPart']['type']);
  }

  /**
   * Test Schema.org custom field settings.
   */
  public function testCustomSettings(): void {
    // Check default_schema_properties custom field settings.
    $this->config('schemadotorg_custom_field.settings')
      ->set('default_schema_properties.Thing--alternateName', [
        'schema_type' => 'Thing',
        'schema_properties' => [
          'integer' => [
            'data_type' => 'integer',
            'max_length' => '999',
            'unsigned' => 0,
            'precision' => '99',
            'scale' => '9',
            'min' => '99',
            'max' => '999',
          ],
          'string' => [
            'data_type' => 'string',
            'widget_type' => 'select',
            'name' => 'custom_string',
            'label' => 'Custom string',
            'description' => 'Custom description',
            'placeholder' => 'Custom placeholder',
            'maxlength' => 999,
            'prefix' => 'Custom prefix',
            'suffix' => 'Custom suffix',
            'required' => TRUE,
          ],
          'allowed_values' => [
            'data_type' => 'string',
            'empty_option' => 'Custom empty option',
            'allowed_values' => [
              'one' => 'One',
              'two' => 'Two',
              'three' => 'Three',
            ],
          ],
          'entity_reference' => [
            'data_type' => 'entity_reference',
            'empty_option' => 'Custom entity reference',
            'target_type' => 'media',
            'handler_settings' => [
              'target_bundles' => ['image' => 'image'],
            ],
          ],
        ],
      ])
      ->save();
    $this->appendSchemaTypeDefaultProperties('Thing', 'alternateName');
    $this->createSchemaEntity('node', 'Thing');

    // Check alternate name custom field storage columns.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = FieldStorageConfig::loadByName('node', 'schema_alternate_name');
    $expected_settings = [
      'columns' => [
        'integer' => [
          'name' => 'integer',
          'type' => 'integer',
          'max_length' => '999',
          'unsigned' => 0,
          'precision' => '99',
          'scale' => '9',
          'datetime_type' => 'datetime',
        ],
        'custom_string' => [
          'name' => 'custom_string',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
        ],
        'allowed_values' => [
          'name' => 'allowed_values',
          'type' => 'string',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
        ],
        'entity_reference' => [
          'name' => 'entity_reference',
          'type' => 'entity_reference',
          'max_length' => '255',
          'unsigned' => 0,
          'precision' => '10',
          'scale' => '2',
          'datetime_type' => 'datetime',
          'target_type' => 'media',
        ],
      ],
    ];
    $this->assertEquals($expected_settings, $field_storage_config->getSettings());

    // Check schema_alternate_name custom field column widget settings.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field_config */
    $field_config = FieldConfig::loadByName('node', 'thing', 'schema_alternate_name');
    $settings = $field_config->getSettings();
    $expected_settings = [
      'integer' => [
        'type' => 'integer',
        'weight' => 0,
        'check_empty' => FALSE,
        'widget_settings' => [
          'label' => 'Integer',
          'settings' => [
            'description' => '',
            'description_display' => 'after',
            'placeholder' => '',
            'min' => 99,
            'max' => 999,
            'prefix' => '',
            'suffix' => '',
            'required' => FALSE,
          ],
        ],
      ],
      'custom_string' => [
        'type' => 'select',
        'weight' => 1,
        'check_empty' => FALSE,
        'widget_settings' => [
          'label' => 'Custom string',
          'settings' => [
            'description' => 'Custom description',
            'description_display' => 'after',
            'required' => TRUE,
            'empty_option' => '- Select -',
            'allowed_values' => [],
          ],
        ],
      ],
      'allowed_values' => [
        'type' => 'select',
        'weight' => 2,
        'check_empty' => FALSE,
        'widget_settings' => [
          'label' => 'Allowed_values',
          'settings' => [
            'description' => '',
            'description_display' => 'after',
            'required' => FALSE,
            'empty_option' => 'Custom empty option',
            'allowed_values' => [
              ['value' => 'One', 'key' => 'one'],
              ['value' => 'Two', 'key' => 'two'],
              ['value' => 'Three', 'key' => 'three'],
            ],
          ],
        ],
      ],
      'entity_reference' => [
        'type' => 'entity_reference_autocomplete',
        'weight' => 3,
        'check_empty' => FALSE,
        'widget_settings' => [
          'label' => 'Entity_reference',
          'settings' => [
            'description' => '',
            'description_display' => 'after',
            'size' => 60,
            'placeholder' => '',
            'required' => FALSE,
            'match_operator' => 'CONTAINS',
            'match_limit' => 10,
            'handler' => 'default:media',
            'handler_settings' => [
              'target_bundles' => [
                'image' => 'image',
              ],
            ],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected_settings, $settings['field_settings']);
  }

}
