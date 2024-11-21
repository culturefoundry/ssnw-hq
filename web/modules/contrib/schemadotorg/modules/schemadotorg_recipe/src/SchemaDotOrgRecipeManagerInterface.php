<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_recipe;

use Symfony\Component\Process\Process;

/**
 * Schema.org recipe manager interface.
 */
interface SchemaDotOrgRecipeManagerInterface {

  /**
   * Determine if a recipe is a Schema.org Blueprints Recipe.
   *
   * @param string $name
   *   A recipe.
   *
   * @return bool
   *   TRUE if a recipe is a Schema.org Blueprints Recipe.
   */
  public function isRecipe(string $name): bool;

  /**
   * Get a list of Schema.org recipes.
   *
   * @param bool $applied
   *   Return only applied recipes.
   *
   * @return array
   *   A list of Schema.org recipes.
   */
  public function getRecipes(bool $applied = FALSE): array;

  /**
   * Get a Schema.org recipe's info.
   *
   * @param string $name
   *   A recipe name.
   *
   * @return array|null
   *   A Schema.org recipe's module info.
   */
  public function getRecipe(string $name): ?array;

  /**
   * Get a module's Schema.org Blueprints recipe settings.
   *
   * @param string $name
   *   A recipe name.
   *
   * @return false|array
   *   A module's Schema.org Blueprints recipe settings.
   *   FALSE if the module is not a Schema.org Blueprints recipe
   */
  public function getRecipeSettings(string $name): FALSE|array;

  /**
   * Apply a Schema.org recipe.
   *
   * @param string $name
   *   A Schema.org recipe name.
   *
   * @return \Symfony\Component\Process\Process
   *   The command process.
   */
  public function apply(string $name): Process;

  /**
   * Generate a Schema.org recipe's content.
   *
   * @param string $name
   *   A Schema.org recipe name.
   */
  public function generate(string $name): void;

  /**
   * Kill a Schema.org recipe's content.
   *
   * @param string $name
   *   A Schema.org recipe name.
   */
  public function kill(string $name): void;

}
