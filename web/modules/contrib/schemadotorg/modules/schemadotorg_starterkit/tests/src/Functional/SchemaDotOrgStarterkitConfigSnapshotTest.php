<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_starterkit\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgConfigSnapshotTestBase;

/**
 * Tests the generated configuration files against a config snapshot.
 *
 * @group schemadotorg
 */
class SchemaDotOrgStarterkitConfigSnapshotTest extends SchemaDotOrgConfigSnapshotTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_starterkit_test'];

  /**
   * {@inheritdoc}
   */
  protected string $snapshotDirectory = __DIR__ . '/../../schemadotorg/config/snapshot';

}
