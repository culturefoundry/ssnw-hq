<?php

declare(strict_types=1);

namespace Drupal\Tests\starterkit\Kernel;

use Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org starter kit manager.
 *
 * @covers \Drupal\starterkit\SchemaDotOrgTaxonomyPropertyVocabularyManagerTest;
 * @group schemadotorg
 */
class SchemaDotOrgStarterkitManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_starterkit',
  ];

  /**
   * The Schema.org starter kit manager service.
   */
  protected SchemaDotOrgStarterkitManagerInterface $schemaStarterkitManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_starterkit']);
    $this->installEntityDependencies('media');
    $this->installEntityDependencies('node');
    $this->schemaStarterkitManager = $this->container->get('schemadotorg_starterkit.manager');
  }

  /**
   * Test Schema.org starter kit manager.
   */
  public function testManager(): void {
    // Check determining if a module is Schema.org Blueprints Starter Kit.
    $this->assertFalse($this->schemaStarterkitManager->isStarterkit('schemadotorg'));
    $this->assertFalse($this->schemaStarterkitManager->isStarterkit('missing_module'));
    $this->assertTrue($this->schemaStarterkitManager->isStarterkit('schemadotorg_starterkit_test'));

    // Check getting a list of Schema.org starter kits.
    $starterkits = $this->schemaStarterkitManager->getStarterkits();
    $this->assertArrayHasKey('schemadotorg_starterkit_test', $starterkits);
    $starterkits = $this->schemaStarterkitManager->getStarterkits(TRUE);
    $this->assertArrayNotHasKey('schemadotorg_starterkit_test', $starterkits);

    // Check getting a Schema.org starter kit's module info.
    $this->assertIsArray($this->schemaStarterkitManager->getStarterkit('schemadotorg_starterkit_test'));

    // Check getting a module's Schema.org Blueprints starter kit settings.
    $settings = $this->schemaStarterkitManager->getStarterkitSettings('schemadotorg_starterkit_test');
    $this->assertEquals('Something', $settings['types']['node:custom_thing:Thing']['entity']['label']);
    $this->assertEquals('_add_', $settings['types']['node:custom_thing:Thing']['properties']['name']['name']);
    $this->assertEquals('_add_', $settings['types']['node:custom_thing:Thing']['properties']['description']['name']);
    $this->assertEquals('_add_', $settings['types']['node:custom_thing:Thing']['properties']['image']['name']);
  }

}
