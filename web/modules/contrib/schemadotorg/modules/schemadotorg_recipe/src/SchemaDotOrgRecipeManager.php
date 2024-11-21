<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_recipe;

use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Recipe\Recipe;
use Drupal\devel_generate\DevelGeneratePluginManager;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgDevelGenerateTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Schema.org Recipe manager service.
 */
class SchemaDotOrgRecipeManager implements SchemaDotOrgRecipeManagerInterface {
  use SchemaDotOrgDevelGenerateTrait;

  /**
   * Cached recipes.
   */
  protected array $recipes;

  /**
   * Cached recipe settings.
   */
  protected array $settings = [];

  /**
   * Constructs a SchemaDotOrgRecipeManager object.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system service.
   * @param \Drupal\Core\Extension\ModuleExtensionList $moduleExtensionList
   *   The module extension list.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $schemaMappingManager
   *   The Schema.org mapping manager.
   * @param \Drupal\devel_generate\DevelGeneratePluginManager|null $develGenerateManager
   *   The Devel generate manager.
   */
  public function __construct(
    protected ContainerInterface $container,
    protected FileSystemInterface $fileSystem,
    protected ModuleExtensionList $moduleExtensionList,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgMappingManagerInterface $schemaMappingManager,
    protected ?DevelGeneratePluginManager $develGenerateManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isRecipe(string $module): bool {
    return (bool) $this->getRecipe($module);
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipe($name): ?array {
    return $this->getRecipes()[$name] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipes(bool $applied = FALSE): array {
    if (!isset($this->recipes)) {
      $root = $this->container->getParameter('app.root');
      $recipe_directories = ['modules', 'recipes'];
      $recipe_files = [];
      foreach ($recipe_directories as $recipe_directory) {
        if (file_exists($root . '/' . $recipe_directory)) {
          $recipe_files += $this->fileSystem->scanDirectory($root . '/' . $recipe_directory, '#^recipe\.yml$#');
        }
      }

      $this->recipes = [];
      foreach (array_keys($recipe_files) as $recipe_path) {
        $recipe_directory = dirname($recipe_path);
        $recipe_name = basename(dirname($recipe_path));

        // Ignore any recipe in /tests/recipes/* directory.
        // @see schemadotorg/tests/recipes/schemadotorg_recipe_test/recipe.yml
        if (str_ends_with($recipe_directory, '/tests/recipes/' . $recipe_name)
          && !drupal_valid_test_ua()) {
          continue;
        }

        $recipe_data = Yaml::decode(file_get_contents($recipe_path));

        // Determine if the recipe is applicable.
        try {
          Recipe::createFromDirectory($recipe_directory);
          $is_applicable = TRUE;
        }
        catch (\Exception $exception) {
          $is_applicable = FALSE;
        }

        $types = [];
        $actions = NestedArray::getValue($recipe_data, ['config', 'actions']) ?? [];
        foreach ($actions as $config_name => $action) {
          if (!str_starts_with($config_name, 'schemadotorg.schemadotorg_mapping.')
            || !NestedArray::keyExists($action, ['createSchemaType'])) {
            continue;
          }

          // Extract the entity type id and bundle from the config name.
          [, , $entity_type_id, $bundle] = explode('.', $config_name);
          $defaults = NestedArray::getValue($action, ['createSchemaType']);
          $schema_type = $defaults['schema_type'];
          unset($defaults['schema_type']);

          $type = "$entity_type_id:$bundle:$schema_type";
          $types[$type] = $defaults;
        }

        $is_applied = ($types) ? TRUE : FALSE;
        foreach ($types as $type => $type_settings) {
          if (!$this->getMappingStorage()->loadByType($type)) {
            $is_applied = FALSE;
          }
        }

        $recipe = $recipe_data + [
          'install' => [],
          // Namespace all Schema.org specific data.
          'schemadotorg' => [
            'directory' => $recipe_directory,
            'path' => $recipe_path,
            'types' => $types,
            'applicable' => $is_applicable,
            'applied' => $is_applied,
          ],
        ];

        $this->recipes[$recipe_name] = $recipe;
      }
    }

    $recipes = $this->recipes;
    if ($applied) {
      foreach ($recipes as $recipe_name => $recipe_data) {
        if (!$recipe_data['schemadotorg']['applied']) {
          unset($recipes[$recipe_name]);
        }
      }
    }

    return $recipes;
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipeSettings(string $name): FALSE|array {
    if (!isset($this->settings[$name])) {
      $settings = $this->getRecipe($name);
      foreach ($settings['schemadotorg']['types'] as $type => $defaults) {
        [$entity_type_id, $bundle, $schema_type] = $this->getMappingStorage()->parseType($type);
        $defaults = $this->schemaMappingManager->prepareCustomMappingDefaults($entity_type_id, $bundle, $schema_type, $defaults);
        $mapping_defaults = $this->schemaMappingManager->getMappingDefaults($entity_type_id, $bundle, $schema_type, $defaults);
        $settings['schemadotorg']['types'][$type] = $mapping_defaults;
      }
      $this->settings[$name] = $settings;
    }
    return $this->settings[$name] ?? FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function apply(string $name): Process {
    $recipe = $this->getRecipe($name);

    // Apply the recipe using a Process to get result.
    // @see \Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait::applyRecipe
    // @see \Drupal\Core\Recipe\RecipeRunner::processRecipe
    $root = $this->container->getParameter('app.root');
    $path = $recipe['schemadotorg']['directory'];

    $process = new Process([
      (new PhpExecutableFinder())->find(),
      'core/scripts/drupal',
      'recipe',
      $path,
    ]);
    $process->setWorkingDirectory($root);
    $process->setTimeout(500);
    $process->run();

    drupal_flush_all_caches();

    return $process;
  }

  /**
   * {@inheritdoc}
   */
  public function generate(string $name): void {
    $settings = $this->getRecipeSettings($name);
    $types = array_keys($settings['schemadotorg']['types']);
    $this->develGenerate($types, 5);
  }

  /**
   * {@inheritdoc}
   */
  public function kill(string $name): void {
    $settings = $this->getRecipeSettings($name);
    $types = array_keys($settings['schemadotorg']['types']);
    $this->develGenerate($types, 0);
  }

}
