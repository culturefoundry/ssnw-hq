<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_translation\Kernel;

use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\Language\LanguageManager;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\language\LanguageNegotiatorInterface;
use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org translation JSON-LD.
 *
 * @covers schemadotorg_translation_schemadotorg_jsonld_schema_type_entity_alter()
 * @group schemadotorg
 */
class SchemaDotOrgTranslationJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'config_translation',
    'content_translation',
    'language',
    'locale',
    'schemadotorg_translation',
  ];


  /**
   * The language negotiator.
   */
  protected LanguageNegotiatorInterface $languageNegotiator;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(static::$modules);
    $this->languageNegotiator = $this->container->get('language_negotiator');
  }

  /**
   * Test Schema.org taxonomy JSON-LD.
   */
  public function testJsonLd(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $language = ConfigurableLanguage::createFromLangcode('es');
    $language->save();
    $this->config('language.negotiation')
      ->set('url.prefixes.es', 'es')
      ->save();
    $this->createSchemaEntity('node', 'WebPage');
    drupal_flush_all_caches();

    /** @var \Drupal\node\NodeInterface $node */
    $node = Node::create([
      'type' => 'page',
      'title' => 'English',
    ]);
    $node->save();

    $node_translation = $node->addTranslation('es', ['title' => 'Spanish']);
    $node_translation->save();

    // Check default translation includes https://schema.org/workTranslation.
    $json_ld = $this->builder->buildEntity($node);
    $this->assertEquals(
      [['@type' => 'WebPage', '@id' => $node_translation->toUrl()->setAbsolute()->toString()]],
      $json_ld['workTranslation']
    );

    // Switch the default (aka current) language to Spanish (es).
    $language_default = new LanguageDefault(['name' => 'Spanish', 'id' => 'es']);
    $language_manager = new LanguageManager($language_default);
    $this->container->set('language_manager', $language_manager);
    \Drupal::setContainer($this->container);

    // Check Spanish translation includes https://schema.org/translationOfWork.
    $json_ld = $this->builder->buildEntity($node);
    $this->assertEquals(
      ['@id' => $node->toUrl()->setAbsolute()->toString()],
      $json_ld['translationOfWork']
    );
  }

}
