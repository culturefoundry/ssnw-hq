<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingStorage;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\schemadotorg\Traits\SchemaDotOrgTestTrait;

/**
 * Defines an abstract test base for Schema.org tests.
 */
abstract class SchemaDotOrgBrowserTestBase extends BrowserTestBase {
  use SchemaDotOrgTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg'];


  /**
   * The Schema.org mapping storage.
   */
  protected SchemaDotOrgMappingStorage $mappingStorage;

  /**
   * The Schema.org mapping manager.
   */
  protected SchemaDotOrgMappingManagerInterface $mappingManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->mappingStorage = $this->container->get('entity_type.manager')->getStorage('schemadotorg_mapping');
    $this->mappingManager = $this->container->get('schemadotorg.mapping_manager');
  }

  /**
   * Create an entity type/bundle that is mapping to a Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null
   *   The entity type/bundle's Schema.org mapping.
   */
  protected function createSchemaEntity(string $entity_type_id, string $schema_type): ?SchemaDotOrgMappingInterface {
    // Create the entity type and mappings.
    $this->mappingManager->createType($entity_type_id, $schema_type);

    // Load the newly created Schema.org mapping.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
    $mappings = $this->mappingStorage->loadByProperties([
      'target_entity_type_id' => $entity_type_id,
      'schema_type' => $schema_type,
    ]);
    return ($mappings) ? reset($mappings) : NULL;
  }

  /* ************************************************************************ */
  // Assert.
  /* ************************************************************************ */

  /**
   * Assert saving a settings form does not alter the expected values.
   *
   * @param string $name
   *   Configuration settings name.
   * @param string $path
   *   Configuration settings form path.
   */
  protected function assertSaveSettingsConfigForm(string $name, string $path): void {
    $assert = $this->assertSession();

    $expected_data = $this->config($name)->getRawData();
    $this->drupalGet($path);
    $this->submitForm([], 'Save configuration');
    $assert->responseContains('The configuration options have been saved.');
    \Drupal::configFactory()->reset($name);
    $actual_data = \Drupal::configFactory()->get($name)->getRawData();
    $this->assertEquals($expected_data, $actual_data);
  }

}
