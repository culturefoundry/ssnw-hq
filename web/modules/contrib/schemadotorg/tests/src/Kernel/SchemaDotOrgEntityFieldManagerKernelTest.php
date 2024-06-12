<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\node\Entity\NodeType;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;

/**
 * Tests the Schema.org entity field manager service.
 *
 * @coversClass \Drupal\schemadotorg\SchemaDotOrgEntityFieldManager
 * @group schemadotorg
 */
class SchemaDotOrgEntityFieldManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'filter',
    'address',
    'schemadotorg_options',
    'schemadotorg_address',
    'schemadotorg_ui',
  ];

  /**
   * The Schema.org entity field manager.
   */
  protected SchemaDotOrgEntityFieldManagerInterface $fieldManager;

  /**
   * A node type.
   */
  protected NodeType $nodeType;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');

    $this->installEntitySchema('node');
    $this->installEntitySchema('node_type');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('paragraphs_type');

    // Install the Schema.org Blueprints Address module, which alters the
    // 'schema_types.default_field_types.PostalAddress' settings.
    \Drupal::moduleHandler()->loadInclude('schemadotorg_address', 'install');
    schemadotorg_address_install(FALSE);

    $this->installConfig(['schemadotorg_options']);

    // Set Schema.org field manager.
    $this->fieldManager = $this->container->get('schemadotorg.entity_field_manager');
  }

  /**
   * Test Schema.org entity field manager.
   */
  public function testEntityFieldManager(): void {
    // Create a text format.
    FilterFormat::create([
      'format' => 'filtered_html',
      'name' => 'Filtered HTML',
    ])->save();

    // Create Thing node with field.
    $node_type = NodeType::create([
      'type' => 'thing',
      'name' => 'Thing',
    ]);
    $node_type->save();
    $this->createSchemaDotOrgField('node', 'Thing');
    FieldStorageConfig::create([
      'entity_type' => 'node',
      'field_name' => 'schema_identifier',
      'type' => 'string',
    ])->save();

    // Create contact point paragraph and Schema.org mapping.
    ParagraphsType::create([
      'id' => 'contact_point',
      'label' => 'Contact Point',
    ])->save();
    SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'paragraph',
      'target_bundle' => 'contact_point',
      'schema_type' => 'ContactPoint',
    ])->save();

    /* ********************************************************************** */

    // Check determining if a field exists.
    $this->assertTrue($this->fieldManager->fieldExists('node', 'thing', 'schema_alternate_name'));
    $this->assertFalse($this->fieldManager->fieldExists('node', 'thing', 'not_schema_alternate_name'));
    $this->assertFalse($this->fieldManager->fieldExists('node', 'not_thing', 'schema_alternate_name'));
    $this->assertFalse($this->fieldManager->fieldExists('not_node', 'thing', 'schema_alternate_name'));
    $this->assertFalse($this->fieldManager->fieldExists('node', 'thing', 'schema_identifier'));

    // Check determining if a field storage exists.
    $this->assertTrue($this->fieldManager->fieldStorageExists('node', 'schema_alternate_name'));
    $this->assertFalse($this->fieldManager->fieldStorageExists('node', 'not_schema_alternate_name'));
    $this->assertFalse($this->fieldManager->fieldStorageExists('not_node', 'schema_alternate_name'));
    $this->assertTrue($this->fieldManager->fieldStorageExists('node', 'schema_identifier'));

    // Check getting an existing field instance.
    $this->assertEquals('Alternate name', $this->fieldManager->getField('node', 'schema_alternate_name')->label());
    $this->assertNull($this->fieldManager->getField('node', 'not_schema_alternate_name'));
    $this->assertNull($this->fieldManager->getField('not_node', 'schema_alternate_name'));
    $this->assertNull($this->fieldManager->getField('node', 'schema_identifier'));

    // Check getting a Schema.org property's default field settings.
    $default_field = $this->fieldManager->getPropertyDefaultField('Person', 'additionalName');
    $this->assertEquals('Middle name', $default_field['label']);
    $default_field = $this->fieldManager->getPropertyDefaultField('Person', 'affiliation');
    $expected_field = [
      'label' => 'Affiliations',
      'unlimited' => TRUE,
      'required' => FALSE,
      'name' => 'affiliation',
      'description' => 'An organization that this person is affiliated with.',
    ];
    $this->assertEquals($expected_field, $default_field);

    // Check getting a Schema.org property's available field types as options.
    $expected_field_type_options = [
      'Recommended' => [
        'string' => 'Text (plain)',
        'string_long' => 'Text (plain, long)',
        'list_string' => 'List (text)',
        'text' => 'Text (formatted)',
        'text_long' => 'Text (formatted, long)',
        'text_with_summary' => 'Text (formatted, long, with summary)',
      ],
      'Date and time' => [
        'datetime' => 'Date',
        'timestamp' => 'Timestamp',
      ],
      'File upload' => [
        'file' => 'File',
        'image' => 'Image',
      ],
      'General' => [
        'address' => 'Address',
        'boolean' => 'Boolean',
        'address_country' => 'Country',
        'email' => 'Email',
        'internal_property_test' => 'Internal Property (test)',
        'link' => 'Link',
        'field_ui:entity_reference:media' => 'Media',
        'field_ui:entity_reference_revisions:paragraph' => 'Paragraphs',
        'shape' => 'Shape',
        'shape_required' => 'Shape (required)',
        'single_internal_property_test' => 'Single Internal Property (test)',
        'telephone' => 'Telephone number',
        'field_test' => 'Test field item',
        'serialized_item_test' => 'Test serialized field item',
        'serialized_property_item_test' => 'Test serialized property field item',
        'address_zone' => 'Zone',
      ],
      'Number' => [
        'decimal' => 'Number (decimal)',
        'float' => 'Number (float)',
        'integer' => 'Number (integer)',
      ],
      'Reference' => [
        'field_ui:entity_reference:node' => 'Content',
        'field_ui:entity_reference:block_content' => 'Custom block',
        'entity_reference' => 'Entity reference',
        'entity_reference_revisions' => 'Entity reference revisions',
        'field_ui:entity_reference:user' => 'User',
      ],
      'Selection list' => [
        'list_float' => 'List (float)',
        'list_integer' => 'List (integer)',
      ],
    ];
    $actual_field_type_options = $this->fieldManager->getPropertyFieldTypeOptions('Thing', 'alternateName');
    $this->convertArrayValuesToStrings($actual_field_type_options);
    $this->assertEquals($expected_field_type_options, $actual_field_type_options);

    // Check getting available fields as options.
    $expected_field_options = [
      '_add_' => 'Add a new field…',
      'Fields' => [
        'schema_alternate_name' => 'Alternate name [Text (plain)]',
      ],
      'Base fields' => [
        'uuid' => 'uuid [UUID]',
        'revision_uid' => 'revision_uid [Entity reference]',
        'uid' => 'uid [Entity reference]',
        'promote' => 'promote [Boolean]',
        'sticky' => 'sticky [Boolean]',
        'langcode' => 'langcode [Language]',
        'title' => 'title [Text (plain)]',
        'created' => 'created [Created]',
        'changed' => 'changed [Last changed]',
      ],
      'Existing fields' => [
        'schema_identifier' => 'schema_identifier [Text (plain)]',
      ],
    ];
    $actual_field_options = $this->fieldManager->getFieldOptions('node', 'thing');
    $this->convertArrayValuesToStrings($actual_field_options);
    $this->assertEquals($expected_field_options, $actual_field_options);

    // Add link as the default field type to alternateName.
    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_field_types', ['alternateName' => ['link']])
      ->save();
    drupal_static_reset();

    // Check getting field types for Schema.org property.
    $tests = [
      [
        'alternateName',
        [
          'link' => 'link',
          'string' => 'string',
          'string_long' => 'string_long',
          'list_string' => 'list_string',
          'text' => 'text',
          'text_long' => 'text_long',
          'text_with_summary' => 'text_with_summary',
        ],
      ],
      [
        'name',
        [
          'string' => 'string',
          'string_long' => 'string_long',
          'list_string' => 'list_string',
          'text' => 'text',
          'text_long' => 'text_long',
          'text_with_summary' => 'text_with_summary',
        ],
      ],
      [
        'gender',
        [
          'list_string' => 'list_string',
          'string' => 'string',
          'string_long' => 'string_long',
          'text' => 'text',
          'text_long' => 'text_long',
          'text_with_summary' => 'text_with_summary',
        ],
      ],
      [
        'worksFor',
        [
          'string' => 'string',
        ],
      ],
      [
        'contactPoint',
        [
          'field_ui:entity_reference_revisions:paragraph' => 'field_ui:entity_reference_revisions:paragraph',
        ],
      ],
      [
        'video',
        [
          'field_ui:entity_reference:media' => 'field_ui:entity_reference:media',
          'link' => 'link',
        ],
      ],
      [
        'location',
        [
          'address' => 'address',
          'string_long' => 'string_long',
          'string' => 'string',
          'list_string' => 'list_string',
          'text' => 'text',
          'text_long' => 'text_long',
          'text_with_summary' => 'text_with_summary',
        ],
      ],
    ];
    foreach ($tests as $test) {
      // Checking keys which also checks the sort order of the field types.
      $this->assertEquals(array_keys($test[1]), array_keys($this->fieldManager->getSchemaPropertyFieldTypes('Thing', $test[0])));
    }
  }

}
