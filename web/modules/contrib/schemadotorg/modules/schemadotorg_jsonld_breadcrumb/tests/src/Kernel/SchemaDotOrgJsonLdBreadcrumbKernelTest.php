<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_breadcrumb\Kernel;

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD breadcrumb.
 *
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdBreadcrumbKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_jsonld',
    'schemadotorg_jsonld_breadcrumb',
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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_jsonld']);
    $this->manager = $this->container->get('schemadotorg_jsonld.manager');
    $this->builder = $this->container->get('schemadotorg_jsonld.builder');
  }

  /**
   * Test Schema.org JSON-LD breadcrumb.
   */
  public function testBreadcrumb(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->appendSchemaTypeDefaultProperties('Thing', 'name');
    $this->createSchemaEntity('node', 'Thing');

    $thing_node = Node::create([
      'type' => 'thing',
      'title' => 'Something',
    ]);
    $thing_node->save();

    // Check building JSON-LD with breadcrumb for the entity's route.
    $expected_result = [
      [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
          [
            '@type' => 'ListItem',
            'position' => 1,
            'item' =>
              [
                '@id' => Url::fromRoute('<front>')->setAbsolute()->toString(),
                'name' => 'Home',
              ],
          ],
          [
            '@type' => 'ListItem',
            'position' => 2,
            'item' =>
              [
                '@id' => $thing_node->toUrl()->setAbsolute()->toString(),
                'name' => 'Something',
              ],
          ],
        ],
      ],
      [
        '@context' => 'https://schema.org',
        '@type' => 'Thing',
        '@url' => $thing_node->toUrl()->setAbsolute()->toString(),
        'name' => 'Something',
      ],
    ];
    $route_match = $this->manager->getEntityRouteMatch($thing_node);
    $this->assertEquals($expected_result, $this->builder->build($route_match));

    // Check that the breadcrumb is move to the https://schema.org/breadcrumb
    // property via https://schema.org/WebPage.
    $this->createSchemaEntity('node', 'WebPage');
    $page_node = Node::create([
      'type' => 'page',
      'title' => 'Some page',
    ]);
    $page_node->save();
    $route_match = $this->manager->getEntityRouteMatch($page_node);
    $expected_result = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      '@url' => $page_node->toUrl()->setAbsolute()->toString(),
      'inLanguage' => 'en',
      'name' => 'Some page',
      'dateCreated' => $this->formatDateTime($page_node->getCreatedTime()),
      'dateModified' => $this->formatDateTime($page_node->getChangedTime()),
      'breadcrumb' => [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
          [
            '@type' => 'ListItem',
            'position' => 1,
            'item' =>
              [
                '@id' => Url::fromRoute('<front>')->setAbsolute()->toString(),
                'name' => 'Home',
              ],
          ],
          [
            '@type' => 'ListItem',
            'position' => 2,
            'item' =>
              [
                '@id' => $page_node->toUrl()->setAbsolute()->toString(),
                'name' => 'Some page',
              ],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected_result, $this->builder->build($route_match));

  }

}
