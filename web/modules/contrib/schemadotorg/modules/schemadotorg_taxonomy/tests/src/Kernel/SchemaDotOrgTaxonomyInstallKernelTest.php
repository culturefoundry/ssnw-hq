<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_taxonomy\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org taxonomy installation.
 *
 * @covers \schemadotorg_taxonomy_install()
 * @group schemadotorg
 */
class SchemaDotOrgTaxonomyInstallKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'taxonomy',
    'schemadotorg_jsonld_endpoint',
    'schemadotorg_taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');
    $this->installConfig(['schemadotorg_jsonld_endpoint', 'schemadotorg_taxonomy']);
  }

  /**
   * Test Schema.org taxonomy installation.
   */
  public function testInstall(): void {
    $config = $this->config('schemadotorg_jsonld_endpoint.settings');
    $this->assertNull($config->get('entity_type_endpoints.taxonomy_term'));
    $this->assertNull($config->get('entity_type_endpoints.taxonomy_vocabulary'));

    \Drupal::moduleHandler()->loadInclude('schemadotorg_taxonomy', 'install');
    schemadotorg_taxonomy_install(FALSE);

    $config = $this->config('schemadotorg_jsonld_endpoint.settings');
    $this->assertEquals('term', $config->get('entity_type_endpoints.taxonomy_term'));
    $this->assertEquals('vocabulary', $config->get('entity_type_endpoints.taxonomy_vocabulary'));
  }

}
