<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_translation\Kernel;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\schemadotorg_translation\SchemaDotOrgTranslationManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\Tests\schemadotorg\Traits\SchemaDotOrgTestTrait;

/**
 * Tests the functionality of the Schema.org translation manager.
 *
 * @covers \Drupal\schemadotorg_translation\SchemaDotOrgTaxonomyPropertyVocabularyManagerTest;
 * @group schemadotorg
 */
class SchemaDotOrgTranslationManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {
  use SchemaDotOrgTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'language',
    'content_translation',
    'schemadotorg_translation',
  ];

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $fieldManager;

  /**
   * The content translation manager.
   */
  protected ContentTranslationManagerInterface $contentTranslationManager;

  /**
   * Schema.org translation manager.
   */
  protected SchemaDotOrgTranslationManagerInterface $schemaTranslationManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('language_content_settings');
    $this->installConfig(['schemadotorg_translation']);

    $this->fieldManager = $this->container->get('entity_field.manager');
    $this->contentTranslationManager = $this->container->get('content_translation.manager');
    $this->schemaTranslationManager = $this->container->get('schemadotorg_translation.manager');
  }

  /**
   * Test Schema.org translation manager.
   */
  public function testManager(): void {
    /* ********************************************************************** */
    // Insert Schema.org mapping.
    // @see schemadotorg_translation_schemadotorg_mapping_insert()
    /* ********************************************************************** */

    // Add additionalTyp property to Place.
    $this->appendSchemaTypeDefaultProperties('Place', 'additionalType');

    // Create a Schema.org mapping.
    $this->createSchemaEntity('node', 'Place');

    // Check that node.place has translations enabled.
    // @see \Drupal\schemadotorg_translation\SchemaDotOrgTranslationManager::enableEntityType
    $this->assertNotNull(ContentLanguageSettings::load('node.place'));
    $this->assertTrue($this->contentTranslationManager->isEnabled('node', 'place'));

    // Check that exclude type does not have translations enabled.
    $this->config('schemadotorg_translation.settings')
      ->set('excluded_schema_types', ['Action'])
      ->save();
    $this->createSchemaEntity('node', 'Action');
    $this->assertFalse($this->contentTranslationManager->isEnabled('node', 'action'));

    // Check that exclude types support inheritance.
    $this->createSchemaEntity('node', 'SearchAction');
    $this->assertFalse($this->contentTranslationManager->isEnabled('node', 'search_action'));

    /* ********************************************************************** */
    // Insert field config.
    // @see schemadotorg_translation_field_config_insert()
    /* ********************************************************************** */

    // Check that node.place fields translations enabled.
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertTrue($field_definitions['title']->isTranslatable());
    $this->assertTrue($field_definitions['body']->isTranslatable());
    $this->assertTrue($field_definitions['schema_address']->isTranslatable());
    $this->assertFalse($field_definitions['schema_image']->isTranslatable());
    $this->assertFalse($field_definitions['schema_telephone']->isTranslatable());
    $this->assertFalse($field_definitions['schema_additional_type']->isTranslatable());

    // Check property field added to a Schema.org has translation enabled.
    $this->createSchemaDotOrgField('node', 'Place');
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertTrue($field_definitions['schema_alternate_name']->isTranslatable());

    // Check included field type (i.e. string) has translation enabled.
    $this->createSchemaDotOrgField('node', 'Place', 'text');
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertTrue($field_definitions['schema_text']->isTranslatable());

    // Check exclude field name do not have translation enabled.
    $this->config('schemadotorg_translation.settings')
      ->set('excluded_field_names', ['field_excluded'])
      ->save();
    FieldStorageConfig::create([
      'entity_type' => 'node',
      'field_name' => 'field_excluded',
      'type' => 'string',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'node',
      'bundle' => 'place',
      'field_name' => 'field_excluded',
    ])->save();
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertFalse($field_definitions['field_excluded']->isTranslatable());

    // Check included field name do have translation enabled.
    $this->config('schemadotorg_translation.settings')
      ->set('included_field_names', ['field_included'])
      ->save();
    FieldStorageConfig::create([
      'entity_type' => 'node',
      'field_name' => 'field_included',
      // Note: Include field name will ignore the excluded field type.
      'type' => 'integer',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'node',
      'bundle' => 'place',
      'field_name' => 'field_included',
    ])->save();
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertTrue($field_definitions['field_included']->isTranslatable());

    // Check integer field added to a Schema.org type does not have
    // translation enabled.
    $this->createSchemaDotOrgField('node', 'Place', 'integer', 'integer');
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertFalse($field_definitions['schema_integer']->isTranslatable());

    // Check excluded Schema.org properties.
    $this->config('schemadotorg_translation.settings')
      ->set('excluded_schema_properties', ['disambiguatingDescription'])
      ->save();
    $this->createSchemaDotOrgField('node', 'Place', 'disambiguatingDescription');
    $field_definitions = $this->fieldManager->getFieldDefinitions('node', 'place');
    $this->assertFalse($field_definitions['schema_disambiguating_desc']->isTranslatable());
  }

}
