<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

/**
 * Tests the generated configuration files against a config snapshot.
 *
 * @group schemadotorg
 */
class SchemaDotOrgConfigSnapshotRecipeTest extends SchemaDotOrgConfigSnapshotTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg'];

  /**
   * {@inheritdoc}
   */
  protected array $recipes = [
    __DIR__ . '/../../recipes/schemadotorg_recipe_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected string $snapshotDirectory = __DIR__ . '/../../schemadotorg/schemadotorg_recipe_test/config/snapshot';

}
