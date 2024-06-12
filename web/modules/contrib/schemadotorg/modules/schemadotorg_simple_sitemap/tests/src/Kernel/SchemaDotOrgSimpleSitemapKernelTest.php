<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_simple_sitemap\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org Simple Sitemap.
 *
 * @covers schemadotorg_simple_sitemap_schemadotorg_mapping_insert()
 * @group schemadotorg
 */
class SchemaDotOrgSimpleSitemapKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'simple_sitemap',
    'schemadotorg_simple_sitemap',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('simple_sitemap');
    $this->installEntitySchema('simple_sitemap_type');
    $this->installSchema('simple_sitemap', ['simple_sitemap']);
    $this->installConfig(['simple_sitemap']);
  }

  /**
   * Test Schema.org simple_sitemap.
   */
  public function testSimpleSitemap(): void {
    $this->createSchemaEntity('node', 'Place');

    /** @var \Drupal\simple_sitemap\Manager\Generator $generator */
    $generator = \Drupal::service('simple_sitemap.generator');

    // Check that the node.place is added to sitemap.xml.
    $expected_settings = [
      'default' => [
        'index' => TRUE,
        'priority' => '0.5',
        'changefreq' => '',
        'include_images' => FALSE,
      ],
      'index' => [
        'index' => FALSE,
        'priority' => '0.5',
        'changefreq' => '',
        'include_images' => FALSE,
      ],
    ];
    $actual_settings = $generator->entityManager()
      ->getBundleSettings('node', 'place');
    $this->assertEquals($expected_settings, $actual_settings);
  }

}
