<?php

declare(strict_types=1);

namespace Drupal\project_browser\Plugin\ProjectBrowserSource;

use Composer\InstalledVersions;
use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\State\StateInterface;
use Drupal\project_browser\Plugin\ProjectBrowserSourceBase;
use Drupal\project_browser\ProjectBrowser\Project;
use Drupal\project_browser\ProjectBrowser\ProjectsResultsPage;
use Drupal\project_browser\RecipeTracker;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Exposes recipes in the local file system to Project Browser.
 *
 * @ProjectBrowserSource(
 *   id = "local_recipes",
 *   label = @Translation("Recipes"),
 *   description = @Translation("Shows available recipes"),
 * )
 */
final class LocalRecipes extends ProjectBrowserSourceBase {

  public function __construct(
    private readonly string $drupalRoot,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    private readonly StateInterface $state,
    private readonly ModuleExtensionList $moduleList,
    mixed ...$arguments,
  ) {
    parent::__construct(...$arguments);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->getParameter('app.root'),
      $container->get(FileUrlGeneratorInterface::class),
      $container->get(StateInterface::class),
      $container->get(ModuleExtensionList::class),
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
  }

  /**
   * Determines the path to search for recipes.
   *
   * If any recipes have been previously applied (recorded in state), this will
   * search in the same places as the first of those recipes. Otherwise, this
   * will search wherever Composer has installed the first package it has of the
   * `drupal-recipe` type.
   *
   * @return string|null
   *   The search path, or NULL if it could not be determined.
   */
  private function getSearchPath(): ?string {
    $applied_recipes = $this->state->get(RecipeTracker::STATE_KEY);
    if ($applied_recipes) {
      [$path] = $applied_recipes[0];
    }
    $installed_recipes = InstalledVersions::getInstalledPackagesByType('drupal-recipe');
    if ($installed_recipes) {
      $path = InstalledVersions::getInstallPath($installed_recipes[0]);
    }
    return isset($path) ? dirname($path) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getProjects(array $query = []): ProjectsResultsPage {
    $list = [];

    $finder = Finder::create()
      ->files()
      ->name('recipe.yml')
      ->depth(1)
      // Always expose core recipes.
      ->in($this->drupalRoot . '/core/recipes');

    $dir = $this->getSearchPath();
    if ($dir && is_dir($dir)) {
      $finder->in($dir);
    }

    // For now, allow external code to set an specific list of recipes to show.
    $allowed_recipes = $this->state->get('project_browser.allowed_recipes');

    /** @var \Symfony\Component\Finder\SplFileInfo $file */
    foreach ($finder as $file) {
      $recipe = Yaml::decode($file->getContents());
      $path = dirname($file->getRealPath());
      $id = basename($path);

      if ($allowed_recipes && !in_array($id, $allowed_recipes, TRUE) || $id === 'example') {
        continue;
      }

      $list[] = new Project(
        id: $id,
        logo: $this->getRecipeLogo($path, $recipe['name']),
        // These next six items are just default values; it's not yet clear
        // how to determine them for real.
        isCompatible: TRUE,
        isMaintained: TRUE,
        isCovered: TRUE,
        isActive: TRUE,
        starUserCount: 0,
        projectUsageTotal: 0,
        machineName: $id,
        body: $this->getRecipeDescription($recipe['description'] ?? ''),
        title: $recipe['name'],
        // I have absolutely no idea what "status" means in this context.
        status: 1,
        // These next four should really come from `composer.json`, but this
        // will do for now.
        changed: $file->getMTime(),
        created: $file->getCTime(),
        author: [],
        composerNamespace: 'drupal/' . $id,
        type: 'recipe',
        commands: $this->getRecipeCommands($path),
      );
    }

    $plugin_definition = $this->getPluginDefinition();
    return new ProjectsResultsPage(count($list), $list, (string) $plugin_definition['label'], $this->getPluginId(), FALSE);
  }

  /**
   * Generates the commands to apply a recipe.
   *
   * @param string $path
   *   The path of the recipe.
   *
   * @return string
   *   The terminal commands needed to apply the recipe.
   */
  private function getRecipeCommands(string $path): string {
    $copy_button_url = $this->moduleList->getPath('project_browser') . '/images/copy-icon.svg';
    $copy_button_url = $this->fileUrlGenerator->generateAbsoluteString($copy_button_url);

    $commands = '';
    $commands .= '<p>' . $this->t('To apply this recipe to your site, run the following command at the command line:') . '</p>';
    $commands .= '<div class="command-box">';
    // cspell:ignore BINDIR
    $commands .= sprintf('<input value="%s/php %s/core/scripts/drupal recipe %s" readonly="" />', PHP_BINDIR, $this->drupalRoot, $path);
    $commands .= '<button data-copy-command>';
    $commands .= sprintf(
      '<img src="%s" alt="%s" />',
      $copy_button_url,
      $this->t('Copy the command to apply this recipe to the clipboard.'),
    );
    $commands .= '</button>';
    $commands .= '</div>';

    return $commands;
  }

  /**
   * Returns the description of the recipe, if any.
   *
   * @param string $description
   *   The description of the recipe, from `recipe.yml`.
   *
   * @return string[]
   *   The description of the recipe, suitable for the Project object.
   */
  private function getRecipeDescription(string $description): array {
    if ($description) {
      return ['value' => $description];
    }
    return [];
  }

  /**
   * Gets the logo, if any, for a recipe.
   *
   * This assumes the logo will be called `logo.png` and be in the same place
   * as `recipe.yml`.
   *
   * @param string $path
   *   The path of the recipe's directory.
   * @param string $name
   *   The human-readable name of the recipe.
   *
   * @return string[]
   *   Either an empty array, or an array with `uri` and `alt` elements for
   *   the logo.
   */
  private function getRecipeLogo(string $path, string $name): array {
    $file = $path . '/logo.png';
    if (file_exists($file)) {
      return [
        'uri' => $this->fileUrlGenerator->generateAbsoluteString($file),
        'alt' => $this->t('Logo of the "@name" recipe.', ['@name' => $name]),
      ];
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCategories(): array {
    return [];
  }

}
