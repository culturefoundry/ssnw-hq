<?php

declare(strict_types=1);

namespace Drupal\Tests\mapping_set\Kernel;

use Drupal\schemadotorg_mapping_set\SchemaDotOrgMappingSetManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org mapping set manager.
 *
 * @covers \Drupal\mapping_set\SchemaDotOrgTaxonomyPropertyVocabularyManagerTest;
 * @group schemadotorg
 */
class SchemaDotOrgMappingSetManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_mapping_set',
  ];

  /**
   * The Schema.org mapping set manager service.
   */
  protected SchemaDotOrgMappingSetManagerInterface $schemaMappingSetManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_mapping_set']);
    $this->installEntityDependencies('media');
    $this->installEntityDependencies('node');
    $this->schemaMappingSetManager = $this->container->get('schemadotorg_mapping_set.manager');
  }

  /**
   * Test Schema.org mapping set manager.
   */
  public function testManager(): void {
    // Update mapping sets to simples sets.
    $config = $this->config('schemadotorg_mapping_set.settings');
    $config->set('sets', [
      'required' => [
        'label' => 'Required',
        'types' => ['node:ContactPoint', 'node:Person'],
      ],
      'common' => [
        'label' => 'Common',
        'types' => ['node:Place', 'node:Person'],
      ],
    ])->save();

    // Check determining if a Schema.org mapping set is already setup.
    $this->assertFalse($this->schemaMappingSetManager->isSetup('required'));
    $this->assertFalse($this->schemaMappingSetManager->isSetup('common'));

    // Check getting mapping sets for an entity type and Schema.org type.
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'ContactPoint');
    $this->assertCount(1, $mapping_sets);
    $this->assertArrayHasKey('required', $mapping_sets);
    $this->assertArrayNotHasKey('common', $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Place');
    $this->assertCount(1, $mapping_sets);
    $this->assertArrayNotHasKey('required', $mapping_sets);
    $this->assertArrayHasKey('common', $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Person');
    $this->assertCount(2, $mapping_sets);
    $this->assertArrayHasKey('required', $mapping_sets);
    $this->assertArrayHasKey('common', $mapping_sets);

    // Check getting Schema.org types from mapping set name.
    $this->assertEquals([
      'node:Place' => 'node:Place',
      'node:Person' => 'node:Person',
    ], $this->schemaMappingSetManager->getTypes('common'));
    $this->assertEquals([
      'node:ContactPoint' => 'node:ContactPoint',
      'node:Person' => 'node:Person',
      'node:Place' => 'node:Place',
    ], $this->schemaMappingSetManager->getTypes('common', TRUE));

    // Check setting up the Schema.org mapping set.
    $this->assertEmpty($this->entityTypeManager->getStorage('node_type')->getQuery()->accessCheck()->execute());
    $this->schemaMappingSetManager->setup('common');
    $this->assertNotEmpty($this->entityTypeManager->getStorage('node_type')->getQuery()->accessCheck()->execute());
    $this->assertEquals([
      'contact_point' => 'contact_point',
      'place' => 'place',
      'person' => 'person',
    ], $this->entityTypeManager->getStorage('node_type')->getQuery()->accessCheck()->execute());
    $this->assertEquals([
      'node.contact_point' => 'node.contact_point',
      'node.place' => 'node.place',
      'node.person' => 'node.person',
    ], $this->entityTypeManager->getStorage('schemadotorg_mapping')->getQuery()->accessCheck()->execute());

    // Check determining if a Schema.org mapping set is already setup.
    $this->assertTrue($this->schemaMappingSetManager->isSetup('required'));
    $this->assertTrue($this->schemaMappingSetManager->isSetup('common'));

    // Check that devel_generate.module is required to generate content.
    try {
      $this->schemaMappingSetManager->generate('common');
    }
    catch (\Exception $exception) {
      $this->assertEquals('The devel_generate.module needs to be enabled.', $exception->getMessage());
    }

    // Check that devel_generate.module is required to kill content.
    try {
      $this->schemaMappingSetManager->kill('common');
    }
    catch (\Exception $exception) {
      $this->assertEquals('The devel_generate.module needs to be enabled.', $exception->getMessage());
    }

    // Check tearing down the Schema.org mapping set.
    $this->schemaMappingSetManager->teardown('common');
    $this->assertEquals([
      'contact_point' => 'contact_point',
      'person' => 'person',
    ], $this->entityTypeManager->getStorage('node_type')->getQuery()->accessCheck()->execute());
    $this->assertEquals([
      'node.contact_point' => 'node.contact_point',
      'node.person' => 'node.person',
    ], $this->entityTypeManager->getStorage('schemadotorg_mapping')->getQuery()->accessCheck()->execute());

    // Check each types mapping sets as we are tearing mapping set.
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Person');
    $this->assertCount(2, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Person', TRUE);
    $this->assertCount(1, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Person', FALSE);
    $this->assertCount(1, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Place');
    $this->assertCount(1, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Place', TRUE);
    $this->assertCount(0, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Place', FALSE);
    $this->assertCount(1, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'ContactPoint');
    $this->assertCount(1, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'ContactPoint', TRUE);
    $this->assertCount(1, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'ContactPoint', FALSE);
    $this->assertCount(0, $mapping_sets);

    $this->schemaMappingSetManager->teardown('required');
    $this->assertEquals([], $this->entityTypeManager->getStorage('node_type')->getQuery()->accessCheck()->execute());
    $this->assertEquals([], $this->entityTypeManager->getStorage('schemadotorg_mapping')->getQuery()->accessCheck()->execute());

    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Person', TRUE);
    $this->assertCount(0, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'Person', FALSE);
    $this->assertCount(2, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'ContactPoint', TRUE);
    $this->assertCount(0, $mapping_sets);
    $mapping_sets = $this->schemaMappingSetManager->getMappingSets('node', 'ContactPoint', FALSE);
    $this->assertCount(1, $mapping_sets);
  }

}
