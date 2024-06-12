<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\Tests\schemadotorg\Traits\SchemaDotOrgTestTrait;

/**
 * Defines an abstract test base for Schema.org kernel tests.
 */
abstract class SchemaDotOrgKernelTestBase extends EntityKernelTestBase {
  use SchemaDotOrgTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'user', 'schemadotorg'];

  /**
   * Installs the Schema.org module's entities, config, and tables.
   */
  protected function installSchemaDotOrg(): void {
    $this->installEntitySchema('schemadotorg_mapping');
    $this->installEntitySchema('schemadotorg_mapping_type');

    $this->installConfig(['schemadotorg']);

    $this->installSchema('schemadotorg', ['schemadotorg_types', 'schemadotorg_properties']);

    /** @var \Drupal\schemadotorg\SchemaDotOrgInstallerInterface $installer */
    $installer = $this->container->get('schemadotorg.installer');
    $installer->install();
  }

}
