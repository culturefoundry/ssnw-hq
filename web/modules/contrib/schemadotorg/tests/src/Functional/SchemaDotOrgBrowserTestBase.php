<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\schemadotorg\Traits\SchemaDotOrgTestTrait;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingStorage;

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
   * Passes if a link with the specified label and href is found.
   *
   * @param string $label
   *   Text between the anchor tags.
   * @param string $href
   *   The full or partial value of the 'href' attribute of the anchor tag.
   * @param string $message
   *   (optional) A message to display with the assertion. Do not translate.
   *
   * @see \Drupal\Tests\WebAssert::linkExists
   * @see \Drupal\Tests\WebAssert::linkByHrefExists
   */
  protected function assertLinkExists(string $label, string $href, string $message = ''): void {
    $message = ($message ?: strtr('Link with label %label and href %href not found.', ['%label' => $label, '%href' => $href]));
    $links = $this->getSession()->getPage()->findAll('named', ['link', $label]);
    $result = FALSE;
    foreach ($links as $link) {
      if ($link->hasAttribute('href')
        && str_contains($link->getAttribute('href'), $href)) {
        $result = TRUE;
      }
    }
    $this->assertSession()->assert($result, $message);
  }

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
