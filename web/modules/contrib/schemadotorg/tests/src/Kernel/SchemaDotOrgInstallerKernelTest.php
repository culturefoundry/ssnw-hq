<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\schemadotorg\SchemaDotOrgInstallerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface;

/**
 * Tests the Schema.org installer service.
 *
 * @coversDefaultClass \Drupal\schemadotorg\SchemaDotOrgInstaller
 * @group schemadotorg
 */
class SchemaDotOrgInstallerKernelTest extends SchemaDotOrgKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['media'];

  /**
   * The Schema.org installer service.
   */
  protected SchemaDotOrgInstallerInterface $installer;

  /**
   * The Schema.org mapping type storage.
   */
  protected SchemaDotOrgMappingTypeStorageInterface $mappingTypeStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchemaDotOrg();

    $this->installer = $this->container->get('schemadotorg.installer');
    $this->mappingTypeStorage = $this->container->get('entity_type.manager')->getStorage('schemadotorg_mapping_type');
  }

  /**
   * Tests SchemaDotOrgInstallerInterface::requirements().
   *
   * @covers ::requirements
   */
  public function testRequirements(): void {
    // Check Schema.org names requirements are not returning an error.
    // @see \Drupal\schemadotorg\SchemaDotOrgInstaller::checkNamesRequirements
    $requirements = $this->installer->requirements('runtime');
    $this->assertArrayNotHasKey('schemadotorg_names', $requirements);

    // Check Schema.org names requirements are returning an error.
    \Drupal::configFactory()
      ->getEditable('schemadotorg.names')
      ->set('custom_names', [])
      ->save();
    $requirements = $this->installer->requirements('runtime');
    $this->assertArrayHasKey('schemadotorg_names', $requirements);

    // Check installation recommended modules requirements.
    // @see \Drupal\schemadotorg\SchemaDotOrgInstaller::checkRecommendedRequirements
    $requirements = $this->installer->requirements('runtime');
    $this->assertNotEmpty($requirements);
    $this->assertArrayHasKey('schemadotorg_recommended_modules', $requirements);
    $this->assertEquals('Schema.org Blueprints: Recommended modules missing', $requirements['schemadotorg_recommended_modules']['title']);

    // Check that installation recommended modules requirements can be disabled.
    $this->config('schemadotorg.settings')
      ->set('requirements.recommended_modules', FALSE)
      ->save();
    $requirements = $this->installer->requirements('runtime');
    $this->assertArrayNotHasKey('schemadotorg_recommended_modules', $requirements);

    // Check installation integration requirements exists.
    $requirements = $this->installer->requirements('runtime');
    $this->assertNotEmpty($requirements);
    $this->assertArrayHasKey('schemadotorg_integration_modules', $requirements);
    $this->assertEquals('Schema.org Blueprints: Integration modules missing', $requirements['schemadotorg_integration_modules']['title']);

    // Check installation recommended requirements does not exist.
    // @see \Drupal\schemadotorg\SchemaDotOrgInstaller::checkIntegrationRequirements
    $this->uninstallModule('media');
    $requirements = $this->installer->requirements('runtime');
    $this->assertArrayNotHasKey('schemadotorg_integration_modules', $requirements);
  }

}
