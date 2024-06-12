<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Config\Entity\ConfigEntityType;
use Drupal\Core\Entity\ContentEntityType;

/**
 * Tests the Schema.org type manager service.
 *
 * @coversClass \Drupal\schemadotorg\SchemaDotOrgMappingTypeStorage
 * @group schemadotorg
 */
class SchemaDotOrgMappingTypeStorageKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_paragraphs']);
  }

  /**
   * Test Schema.org mapping type storage.
   */
  public function testSchemaDotOrgMappingTypeStorage(): void {
    // Check getting entity types that implement Schema.org.
    $expected_entity_types = [
      'node' => 'node',
      'paragraph' => 'paragraph',
      'user' => 'user',
    ];
    $actual_entity_types = $this->mappingTypeStorage->getEntityTypes();
    $this->assertEquals($expected_entity_types, $actual_entity_types);

    // Check getting entity types with bundles that implement Schema.org.
    $expected_bundle_entity_types = [
      'node' => 'node',
      'paragraph' => 'paragraph',
    ];
    $actual_bundle_entity_types = $this->mappingTypeStorage->getEntityTypesWithBundles();
    $this->assertEquals($expected_bundle_entity_types, $actual_bundle_entity_types);

    // Check getting entity type bundles. (i.e node).
    $actual_entity_type_bundles = $this->mappingTypeStorage->getEntityTypeBundles();
    $this->assertArrayHasKey('paragraph', $actual_entity_type_bundles);
    $this->assertInstanceOf(ContentEntityType::class, $actual_entity_type_bundles['paragraph']);

    // Check getting entity type bundle definitions. (i.e node_type).
    $actual_entity_type_bundle_definitions = $this->mappingTypeStorage->getEntityTypeBundleDefinitions();
    $this->assertArrayHasKey('paragraph', $actual_entity_type_bundle_definitions);
    $this->assertInstanceOf(ConfigEntityType::class, $actual_entity_type_bundle_definitions['paragraph']);
  }

}
