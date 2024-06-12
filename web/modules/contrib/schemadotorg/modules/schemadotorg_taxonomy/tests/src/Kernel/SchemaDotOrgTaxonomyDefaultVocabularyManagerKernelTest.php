<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_taxonomy\Kernel;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org taxonomy default vocabulary manager.
 *
 * @covers \Drupal\schemadotorg_taxonomy\SchemaDotOrgTaxonomyDefaultVocabularyManager
 * @group schemadotorg
 */
class SchemaDotOrgTaxonomyDefaultVocabularyManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'language',
    'content_translation',
    'taxonomy',
    'field_group',
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
    // Check that the tags and article_tags vocabularies do not exist.
    $this->assertNull(Vocabulary::load('tags'));
    $this->assertNull(Vocabulary::load('article_tags'));

    // Config tags and article_tags as default vocabularies.
    \Drupal::configFactory()->getEditable('schemadotorg_taxonomy.settings')
      ->set('default_field_groups.custom', 'Custom')
      ->set('default_vocabularies.tags', [
        'id' => 'tags',
        'label' => 'Tags',
      ])
      ->set('default_vocabularies.Article--article_tags', [
        'id' => 'article_tags',
        'label' => 'Article Tags',
      ])
      ->set('default_vocabularies.custom', [
        'id' => 'custom',
        'label' => 'Custom',
        'group' => 'custom',
      ])
      ->save();

    // Create an Article.
    $this->createSchemaEntity('node', 'Article');

    // Check that the tags and article_tags vocabularies exist.
    $this->assertNotNull(Vocabulary::load('tags'));
    $this->assertNotNull(Vocabulary::load('article_tags'));

    // Check that the field storage is created.
    $this->assertNotNull(FieldStorageConfig::loadByName('node', 'field_tags'));
    $this->assertNotNull(FieldStorageConfig::loadByName('node', 'field_article_tags'));

    // Check that the field is created.
    $this->assertNotNull(FieldConfig::loadByName('node', 'article', 'field_tags'));
    $this->assertNotNull(FieldConfig::loadByName('node', 'article', 'field_article_tags'));

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository */
    $entity_display_repository = \Drupal::service('entity_display.repository');

    // Create that the form display and component are created.
    $form_display = $entity_display_repository->getFormDisplay('node', 'article');
    $this->assertNotNull($form_display);
    $form_component = $form_display->getComponent('field_tags');
    $this->assertEquals('entity_reference_autocomplete_tags', $form_component['type']);
    $form_component = $form_display->getComponent('field_article_tags');
    $this->assertEquals('entity_reference_autocomplete_tags', $form_component['type']);
    $form_group = $form_display->getThirdPartySetting('field_group', 'group_taxonomy');
    $this->assertEquals('Categories and Services', $form_group['label']);
    $this->assertEquals('details', $form_group['format_type']);
    $this->assertEquals(['field_tags', 'field_article_tags'], $form_group['children']);
    $form_group = $form_display->getThirdPartySetting('field_group', 'group_custom');
    $this->assertEquals('Custom', $form_group['label']);
    $this->assertEquals('details', $form_group['format_type']);
    $this->assertEquals(['field_custom'], $form_group['children']);

    // Check that the view display and component are created.
    $view_display = $entity_display_repository->getViewDisplay('node', 'article');
    $this->assertNotNull($view_display);
    $view_component = $view_display->getComponent('field_tags');
    $this->assertEquals('entity_reference_label', $view_component['type']);
    $view_component = $view_display->getComponent('field_article_tags');
    $this->assertEquals('entity_reference_label', $view_component['type']);
    $view_group = $view_display->getThirdPartySetting('field_group', 'group_taxonomy');
    $this->assertEquals('Categories and Services', $view_group['label']);
    $this->assertEquals('fieldset', $view_group['format_type']);
    $this->assertEquals(['field_tags', 'field_article_tags'], $view_group['children']);
    $view_group = $view_display->getThirdPartySetting('field_group', 'group_custom');
    $this->assertEquals('Custom', $view_group['label']);
    $this->assertEquals('fieldset', $view_group['format_type']);
    $this->assertEquals(['field_custom'], $view_group['children']);

    // Check that tags and article_tags vocabularies are translated.
    $this->assertNotNull(ContentLanguageSettings::load('taxonomy_term.tags'));
    $this->assertTrue($this->contentTranslationManager->isEnabled('taxonomy_term', 'tags'));
    $this->assertNotNull(ContentLanguageSettings::load('taxonomy_term.article_tags'));
    $this->assertTrue($this->contentTranslationManager->isEnabled('taxonomy_term', 'article_tags'));

    // Check that only the tags vocabulary is applied to all content types.
    $this->createSchemaEntity('node', 'WebPage');

    // Check that the field storage is created.
    $this->assertNotNull(FieldConfig::loadByName('node', 'page', 'field_tags'));
    $this->assertNull(FieldConfig::loadByName('node', 'page', 'field_article_tags'));
  }

}
