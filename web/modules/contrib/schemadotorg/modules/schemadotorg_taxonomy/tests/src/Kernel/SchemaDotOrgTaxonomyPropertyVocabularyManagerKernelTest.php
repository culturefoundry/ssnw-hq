<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_taxonomy\Kernel;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

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

    $this->contentTranslationManager = $this->container->get('content_translation.manager');
  }

  /**
   * Test Schema.org taxonomy property vocabulary manager.
   */
  public function testManager(): void {
    $this->createSchemaEntity('node', 'Recipe');

    /* ********************************************************************** */

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
  }

}
