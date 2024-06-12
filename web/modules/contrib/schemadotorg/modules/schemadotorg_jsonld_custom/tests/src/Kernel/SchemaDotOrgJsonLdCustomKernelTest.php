<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_custom\Kernel;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\Tests\schemadotorg_jsonld\Traits\SchemaDotOrgJsonLdTestTrait;

/**
 * Tests the functionality of the Schema.org JSON-LD custom.
 *
 * @covers \Drupal\schemadotorg_jsonld_custom\SchemaDotOrgJsonLdCustomManager
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdCustomKernelTest extends SchemaDotOrgEntityKernelTestBase {
  use SchemaDotOrgJsonLdTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'token',
    'schemadotorg_jsonld',
    'schemadotorg_jsonld_custom',
  ];

  /**
   * Schema.org JSON-LD manager.
   */
  protected SchemaDotOrgJsonLdManagerInterface $manager;

  /**
   * Schema.org JSON-LD builder.
   */
  protected SchemaDotOrgJsonLdBuilderInterface $builder;

  /**
   * The date formatter service.
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['system', 'schemadotorg_jsonld', 'schemadotorg_jsonld_custom']);

    $this->manager = $this->container->get('schemadotorg_jsonld.manager');
    $this->builder = $this->container->get('schemadotorg_jsonld.builder');
    $this->dateFormatter = $this->container->get('date.formatter');
  }

  /**
   * Test Schema.org JSON-LD custom.
   */
  public function testCustom(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->createSchemaEntity('node', 'Article');

    $node = Node::create([
      'type' => 'article',
      'title' => 'Something',
    ]);
    $node->save();

    \Drupal::configFactory()
      ->getEditable('system.site')
      ->set('name', 'Some site')
      ->set('page.front', '/node/' . $node->id())
      ->save();

    // Check building JSON-LD with custom for the entity's route.
    // @see \Drupal\schemadotorg_jsonld_custom\SchemaDotOrgJsonLdCustomManager::loadSchemaTypeEntityJsonLd
    $site_url = Url::fromRoute('<front>')->setAbsolute()->toString();
    $expected_result = [
      // Check path specific (i.e. <front>) JSON-LD is added.
      [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Some site',
        'url' => $site_url,
        'potentialAction' => [
          '@type' => 'SearchAction',
          'target' => $site_url . '/search/node?keys={search_term_string}',
          'query-input' => 'required name=search_term_string',
        ],
      ],
      [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        '@url' => $node->toUrl()->setAbsolute()->toString(),
        'inLanguage' => 'en',
        'headline' => 'Something',
        'dateCreated' => $this->formatDateTime($node->getCreatedTime()),
        'dateModified' => $this->formatDateTime($node->getChangedTime()),
        // Check Schema.org type custom JSON-LD is added.
        'publisher' => [
          '@context' => 'https://schema.org',
          '@type' => 'Organization',
          'name' => 'Some site',
          'url' => $site_url,
        ],
        // Check Schema.org mapping custom JSON-LD is added.
        'copyrightHolder' => 'Some site',
        'copyrightYear' => date('Y'),
      ],
    ];
    $route_match = $this->manager->getEntityRouteMatch($node);
    $this->assertEquals($expected_result, $this->builder->build($route_match));

    // Check that mapping default include schemadotorg_jsonld_custom settings.
    // @see \Drupal\schemadotorg_jsonld_custom\SchemaDotOrgJsonLdCustomManager::alterMappingDefaults
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $mapping_manager */
    $mapping_manager = $this->container->get('schemadotorg.mapping_manager');
    $defaults = $mapping_manager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'NewsArticle',
    );
    $expected_defaults = [
      'schemadotorg_jsonld_custom' => [
        'json' => '{
    "publisher": {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "[site:name]",
        "url": "[site:url]"
    }
}',
      ],
    ];
    $this->assertEquals($expected_defaults, $defaults['third_party_settings']);
  }

}
