<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_taxonomy\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Tests the functionality of the Schema.org taxonomy property vocabulary manager.
 *
 * @covers \Drupal\schemadotorg_taxonomy\SchemaDotOrgTaxonomyPropertyVocabularyManager
 * @group schemadotorg
 */
class SchemaDotOrgTaxonomyPropertyVocabularyManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'language',
    'content_translation',
    'taxonomy',
    'schemadotorg_taxonomy',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * The content translation manager.
   */
  protected ContentTranslationManagerInterface $contentTranslationManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');
    $this->installConfig(['schemadotorg_taxonomy']);

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
    $this->contentTranslationManager = $this->container->get('content_translation.manager');
  }

  /**
   * Test Schema.org taxonomy property vocabulary manager.
   */
  public function testManager(): void {
    // Create a Recipe.
    $this->createSchemaEntity('node', 'Recipe');

    // Check that recipeCategory property defaults to
    // 'entity_reference:taxonomy_term' field type.
    /** @var \Drupal\field\FieldConfigInterface|null $field_config */
    $field_config = FieldConfig::loadByName('node', 'recipe', 'schema_recipe_category');
    $this->assertEquals('default:taxonomy_term', $field_config->getSetting('handler'));
    $handler_settings = $field_config->getSetting('handler_settings');
    $this->assertEquals(['recipe_category' => 'recipe_category'], $handler_settings['target_bundles']);
    $this->assertTrue($handler_settings['auto_create']);

    // Check that recipe_category vocabulary is created.
    /** @var \Drupal\taxonomy\VocabularyInterface $vocabulary */
    $vocabulary = Vocabulary::load('recipe_category');
    $this->assertEquals('recipe_category', $vocabulary->id());
    $this->assertEquals('Recipe category', $vocabulary->label());
    $this->assertEquals('The category of the recipe—for example, appetizer, entree, etc.', $vocabulary->getDescription());

    // Check that recipe_category vocabulary is translated.
    $this->assertNotNull(ContentLanguageSettings::load('taxonomy_term.recipe_category'));
    $this->assertTrue($this->contentTranslationManager->isEnabled('taxonomy_term', 'recipe_category'));

    // Check the widget display is set to 'entity_reference_autocomplete_tags'.
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'recipe');
    $form_component = $form_display->getComponent('schema_recipe_category');
    $this->assertEquals('entity_reference_autocomplete_tags', $form_component['type']);

    /* ********************************************************************** */

    // Check the widget display is set to 'options_select'.
    $defaults = [
      'entity' => ['id' => 'other_physician'],
      'properties' => [
        'medicalSpecialty' => [
          'name' => SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD,
          'widget_id' => 'options_select',
        ],
      ],
    ];
    $this->createSchemaEntity('node', 'Physician', $defaults);
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'other_physician');
    $form_component = $form_display->getComponent('schema_medical_specialty');
    $this->assertEquals('options_select', $form_component['type']);
  }

}
