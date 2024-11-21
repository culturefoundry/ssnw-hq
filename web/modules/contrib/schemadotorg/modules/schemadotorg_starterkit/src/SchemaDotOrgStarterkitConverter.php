<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Schema.org Starter kit converter service.
 */
class SchemaDotOrgStarterkitConverter implements SchemaDotOrgStarterkitConverterInterface {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * The app root.
   */
  protected string $root;

  /**
   * Constructs a SchemaDotOrgStarterkitConvert object.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system service.
   * @param \Drupal\Core\Extension\ModuleExtensionList $moduleExtensionList
   *   The module extension list.
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $moduleInstaller
   *   The module installer service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface $starterkitManager
   *   The Schema.org starter kit manager.
   */
  public function __construct(
    protected ContainerInterface $container,
    protected FileSystemInterface $fileSystem,
    protected ModuleExtensionList $moduleExtensionList,
    protected ModuleInstallerInterface $moduleInstaller,
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgStarterkitManagerInterface $starterkitManager,
  ) {
    $this->root = $this->container->getParameter('app.root');
  }

  /**
   * {@inheritdoc}
   */
  public function convert(string $module_name): void {
    $this->copyComposer($module_name);
    $this->copyLogo($module_name);
    $this->copyReadMe($module_name);
    $this->copyConfig($module_name);
    $this->copyDefaultContent($module_name);
    // $this->copyTest($module_name);
    $this->writeRecipe($module_name);
  }

  /**
   * Copy the logo.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function copyLogo(string $module_name): void {
    $module_path = $this->moduleExtensionList->getPath($module_name);
    $logo_path = $module_path . '/logo.png';
    if (file_exists($logo_path)) {
      $recipe_path = $this->getRecipePath($module_name);
      $this->fileSystem->copy($logo_path, $recipe_path . '/logo.png', FileExists::Replace);
    }
  }

  /**
   * Copy the README.md.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function copyReadMe(string $module_name): void {
    $module_path = $this->moduleExtensionList->getPath($module_name);
    $readme_path = $module_path . '/README.md';
    if (file_exists($readme_path)) {
      $contents = file_get_contents($readme_path);
      $contents = $this->replaceText($contents);
      $recipe_path = $this->getRecipePath($module_name);
      file_put_contents($recipe_path . '/README.md', $contents);
    }
  }

  /**
   * Copy the composer.json.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function copyComposer(string $module_name): void {
    $module_path = $this->moduleExtensionList->getPath($module_name);
    $composer_path = $module_path . '/composer.json';
    if (!file_exists($composer_path)) {
      return;
    }

    $data = Json::decode(file_get_contents($composer_path));
    $data = $this->replaceText($data);
    unset($data['require']['drupal/config_rewrite']);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $composer_path = $this->getRecipePath($module_name) . '/composer.json';
    file_put_contents($composer_path, $json);
  }

  /**
   * Copy the config files.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function copyConfig(string $module_name): void {
    $files = $this->getConfigFiles($module_name);
    if (empty($files)) {
      return;
    }

    $config_path = $this->getRecipePath($module_name) . '/config';
    $this->fileSystem->prepareDirectory($config_path, FileSystemInterface::CREATE_DIRECTORY);

    foreach ($files as $file_path => $file) {
      $this->fileSystem->copy($file_path, $config_path . '/' . $file->filename, FileExists::Replace);
    }
  }

  /**
   * Copy default content.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function copyDefaultContent(string $module_name): void {
    $module_path = $this->moduleExtensionList->getPath($module_name);
    $recipe_path = $this->getRecipePath($module_name);

    if (file_exists($recipe_path . '/content')) {
      $this->fileSystem->deleteRecursive($recipe_path . '/content');
    }

    if (file_exists($module_path . '/content')) {
      $this->recursiveDirectoryCopy($module_path . '/content', $recipe_path . '/content');
    }
  }

  /**
   * Copy the config files.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function copyTest(string $module_name): void {
    $module_path = $this->moduleExtensionList->getPath($module_name);
    $module_test_path = $module_path . '/tests';
    if (!file_exists($module_test_path)) {
      return;
    }

    $recipe_path = $this->getRecipePath($module_name);
    if (file_exists($recipe_path . '/tests')) {
      $this->fileSystem->deleteRecursive($recipe_path . '/tests');
    }

    $recipe_tests_path = $recipe_path . '/tests';
    $this->fileSystem->prepareDirectory($recipe_tests_path, FileSystemInterface::CREATE_DIRECTORY);
    $this->recursiveDirectoryCopy($module_test_path . '/schemadotorg', $recipe_tests_path . '/schemadotorg');

    $core_extension = $recipe_path . '/tests/schemadotorg/config/snapshot/core.extension.yml';
    if (file_exists($core_extension)) {
      $contents = file_get_contents($core_extension);
      $data = Yaml::decode($contents);
      unset(
        $data['module']['schemadotorg_starterkit'],
        $data['module']['default_content'],
        $data['module']['schemadotorg_starterkit_events'],
      );
      file_put_contents($core_extension, Yaml::encode($data));
    }

    $recipe_name = $this->replaceText($module_name);
    $recipe_camel_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $recipe_name)));
    $recipe_camel_name = str_replace('Schemadotorg', 'SchemaDotOrg', $recipe_camel_name);

    $test = <<<END
<?php

declare(strict_types=1);

namespace Drupal\Tests\\{$recipe_name}\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgConfigSnapshotTestBase;

/**
 * Tests the generated configuration files against a config snapshot.
 *
 * @group schemadotorg
 */
class {$recipe_camel_name}ConfigSnapshotTest extends SchemaDotOrgConfigSnapshotTestBase {

  /**
   * {@inheritdoc}
   */
  protected array \$recipes = [__DIR__ . '/../../../'];

  /**
   * {@inheritdoc}
   */
  protected string \$snapshotDirectory = __DIR__ . '/../../schemadotorg/config/snapshot';

}
END;

    $recipe_test_path = $recipe_path . '/tests/src/Functional';
    $this->fileSystem->prepareDirectory($recipe_test_path, FileSystemInterface::CREATE_DIRECTORY);
    file_put_contents($recipe_test_path . '/' . $recipe_camel_name . 'ConfigSnapshotTest.php', $test);
  }

  /**
   * Write the starter kit's recipe.yml file.
   *
   * @param string $module_name
   *   The starter kit's module name.
   */
  protected function writeRecipe(string $module_name): void {
    // Convert module's info to recipe info.
    $module_data = $this->moduleExtensionList->get($module_name);
    $data = [
      'name' => $this->replaceText($module_data->info['name']),
      'description' => $this->replaceText($module_data->info['description'] ?? ''),
      'type' => 'Schema.org Blueprints Recipe',
    ];

    // Add recipes.
    $data['recipes'] = array_values($this->getRecipes($module_data))
      ?: ['schemadotorg_recipe_base'];

    // Get dependencies.
    $dependencies = $this->getDependencies($module_data);

    // Add dependencies to install.
    $data['install'] = $dependencies;

    // Add config string.
    $data['config']['strict'] = FALSE;

    // Add config import.
    $import = $this->getConfigImport($data['install']);
    if ($import) {
      $data['config']['import'] = $import;
    }

    // Add config actions.
    $config_install = $this->getInstallConfigActions($dependencies);
    $config_rewrites = $this->getConfigRewrites($module_name);
    $config_actions = $this->getConfigActions($module_name);
    $actions = $config_install
      + $config_rewrites['before']
      + $config_actions
      + $config_rewrites['after'];
    if ($actions) {
      $data['config']['actions'] = $actions;
    }

    // Write recipe.yml.
    $filename = $this->getRecipePath($module_name) . '/recipe.yml';
    file_put_contents($filename, Yaml::encode($data));
  }

  /**
   * Get core, contrib, and schemadotorg dependencies.
   *
   * @param \Drupal\Core\Extension\Extension $module_data
   *   Module data.
   *
   * @return array
   *   Core, contrib, and schemadotorg dependencies.
   */
  protected function getDependencies(Extension $module_data): array {
    $dependencies = [];

    $has_recipes = FALSE;
    // Include dependencies in *.info.yml.
    foreach ($module_data->info['dependencies'] as $name) {
      $parts = explode(':', $name);
      $name = end($parts);
      if (str_starts_with($name, 'schemadotorg_starterkit_')) {
        $has_recipes = TRUE;
      }
      else {
        $dependencies[$name] = $name;
      }
    }

    // Include calculated dependencies that install config entities.
    if (!$has_recipes) {
      // @phpstan-ignore-next-line property.notFound
      foreach (array_keys($module_data->requires) as $name) {
        if ($this->installsConfigEntities($name)) {
          $dependencies[$name] = $name;
        }
      }
    }

    // Remove ignored dependencies.
    $ignored = [
      // Core.
      'file',
      'node',
      'path',
      'system',
      'taxonomy',
      'user',
      // Contrib.
      'config_rewrite',
      'default_content',
      // Starter kit.
      'schemadotorg_starterkit',
      // Schema.org Blueprints.
      'block_content',
      'views',
      'field_group',
      'pathauto',
      'type_tray',
      'schemadotorg',
      'schemadotorg_additional_mappings',
      'schemadotorg_additional_type',
      'schemadotorg_allowed_formats',
      'schemadotorg_block_content',
      'schemadotorg_descriptions',
      'schemadotorg_diagram',
      'schemadotorg_export',
      'schemadotorg_field_group',
      'schemadotorg_field_prefix',
      'schemadotorg_help',
      'schemadotorg_media',
      'schemadotorg_node',
      'schemadotorg_options',
      'schemadotorg_pathauto',
      'schemadotorg_report',
      'schemadotorg_taxonomy',
      'schemadotorg_type_tray',
      'schemadotorg_ui',
    ];
    $dependencies = array_diff_key($dependencies, array_combine($ignored, $ignored));

    $core = [];
    $contrib = [];
    $schemadotorg = [];
    foreach ($dependencies as $name) {
      $parts = explode(':', $name);
      $name = end($parts);

      /** @var \Drupal\Core\Extension\Dependency $data */
      // @phpstan-ignore-next-line property.notFound
      $data = $module_data->requires[$name];
      if ($data->getProject() === 'drupal') {
        $core[] = $name;
      }
      elseif (str_starts_with($name, 'schemadotorg')) {
        $schemadotorg[] = $name;
      }
      else {
        $contrib[] = $name;
      }
    }
    return array_merge($core, $contrib, $schemadotorg);
  }

  /**
   * Get parent recipes.
   *
   * @param \Drupal\Core\Extension\Extension $module_data
   *   Module data.
   *
   * @return array
   *   Parent recipes.
   */
  protected function getRecipes(Extension $module_data): array {
    $recipes = [];
    foreach ($module_data->info['dependencies'] as $name) {
      $parts = explode(':', $name);
      $name = end($parts);
      if (str_starts_with($name, 'schemadotorg_starterkit_')) {
        $name = $this->replaceText($name);
        $recipes[$name] = $name;
      }
    }
    return $recipes;
  }

  /**
   * Get core.extension install hooks config action.
   *
   * @param array $dependencies
   *   Module dependencies.
   *
   * @return array[]
   *   The core.extension install hooks config action.
   */
  protected function getInstallConfigActions(array $dependencies): array {
    $this->moduleHandler->loadAllIncludes('install');
    $hooks = [];
    foreach ($dependencies as $dependency_name) {
      $dependency_path = $this->moduleExtensionList->getPath($dependency_name);
      $install_path = $dependency_path . '/' . $dependency_name . '.install';
      if (file_exists($install_path)) {
        require_once $install_path;
      }

      $hook_install = $dependency_name . '_install';
      if (function_exists($hook_install)) {
        $hook_function = new \ReflectionFunction($hook_install);
        if ($hook_function->getParameters()) {
          $hooks[] = $dependency_name;
        }
      }
    }
    return $hooks ? ['core.extension' => ['executeInstallHook' => $hooks]] : [];
  }

  /**
   * Get config import for dependencies.
   *
   * @param array $dependencies
   *   Module dependencies.
   *
   * @return array
   *   Config import for dependencies.
   */
  protected function getConfigImport(array $dependencies): array {
    $import = [];
    foreach ($dependencies as $name) {
      // Import all config for module's with config entities.
      if ($this->installsConfigEntities($name)) {
        $import[$name] = '*';
      }
    }
    return $import;
  }

  /**
   * Determine if a module installs config entities.
   *
   * @param string $module_name
   *   A module name.
   *
   * @return bool
   *   TRUE if a module installs config entities.
   */
  protected function installsConfigEntities(string $module_name): bool {
    $files = $this->getConfigFiles($module_name);
    foreach (array_keys($files) as $config_file) {
      // Remove simple configuration (w/o id) from dependency config files.
      $dependency_config_data = Yaml::decode(file_get_contents($config_file));
      if (!isset($dependency_config_data['id'])) {
        unset($files[$config_file]);
      }
    }
    return (bool) $files;
  }

  /**
   * Get a starter kit Schema.org type converted to config actions.
   *
   * @param string $module_name
   *   A starter kit module name.
   *
   * @return array
   *   A starter kit Schema.org type converted to config actions.
   */
  protected function getConfigActions(string $module_name): array {
    $actions = [];
    // Convert starter kit types to config actions.
    $settings_data = $this->starterkitManager->getStarterkitSettingsData($module_name);
    $settings = $this->starterkitManager->getStarterkitSettings($module_name);
    foreach ($settings_data['types'] as $type => $type_defaults) {
      [$entity_type_id, $bundle, $schema_type] = $this->getMappingStorage()->parseType($type);

      $bundle = $bundle
        ?? $settings['types'][$type]['entity']['id']
        ?? $this->configFactory->get('schemadotorg.settings')->get("schema_types.default_types.$schema_type.name")
        ?? $this->schemaTypeManager->getType($schema_type)['drupal_name'];

      $config_name = "schemadotorg.schemadotorg_mapping.$entity_type_id.$bundle";
      $actions[$config_name] = [
        'createSchemaType' => ['schema_type' => $schema_type] + $type_defaults,
      ];
    }
    return $actions;
  }

  /**
   * Get a starter kit's config rewrites converted to config actions.
   *
   * @param string $module_name
   *   A starter kit module name.
   *
   * @return array[]
   *   A starter kit's config rewrites converted to config actions.
   *
   * @see \Drupal\config_rewrite\ConfigRewriter::rewriteConfig
   */
  protected function getConfigRewrites(string $module_name): array {
    $files = $this->getConfigFiles($module_name, ['rewrite']);

    $before = [];
    $after = [];
    foreach ($files as $file_path => $file) {

      $contents = file_get_contents($file_path);

      $config_name = $file->name;

      $rewrite = Yaml::decode($contents);
      $replace = NestedArray::getValue($rewrite, ['config_rewrite', 'replace']) ?? NULL;
      if (is_array($replace)) {
        foreach ($replace as $key) {
          $parts = explode('.', $key);
          $key_exists = NULL;
          $value = NestedArray::getValue($rewrite, $parts, $key_exists);
          if ($key_exists) {
            if (str_starts_with($config_name, 'schemadotorg')) {
              $before[$config_name]['simpleConfigUpdate'][$key] = $value;
            }
            else {
              $after[$config_name]['simpleConfigUpdate'][$key] = $value;
            }
          }
        }
      }
      else {
        $after[$config_name]['simpleConfigUpdate'] = $rewrite;
      }
    }

    return ['before' => $before, 'after' => $after];
  }

  /**
   * Get recipe path from a starter kit module name.
   *
   * @param string $module_name
   *   A starter kit module name.
   *
   * @return string
   *   The recipe path from a starter kit module name.
   */
  protected function getRecipePath(string $module_name): string {
    $recipe_name = $this->replaceText($module_name);
    $recipe_path = $this->root . '/recipes/sandbox/' . $recipe_name;
    $this->fileSystem->prepareDirectory($recipe_path, FileSystemInterface::CREATE_DIRECTORY);
    return $recipe_path;
  }

  /**
   * Get a module's config files.
   *
   * @param string $module_name
   *   A starter kit module name.
   * @param array $directories
   *   Config directories to get files from.
   *   Defaults to /install and /optional.
   *
   * @return array
   *   A module's config files.
   */
  protected function getConfigFiles(string $module_name, array $directories = ['install', 'optional']): array {
    $module_path = $this->moduleExtensionList->getPath($module_name);

    $files = [];
    foreach ($directories as $directory) {
      if (file_exists($module_path . '/config/' . $directory)) {
        $files += $this->fileSystem->scanDirectory($module_path . '/config/' . $directory, '#\.yml$#');
      }
    }

    ksort($files);
    return $files;
  }

  /**
   * Replace 'starter kit' with 'recipe' in a string of text.
   *
   * @param mixed $text
   *   A string of text.
   *
   * @return mixed
   *   A string of text with 'starter kit' replaced with 'recipe'.
   */
  protected function replaceText(mixed $text): mixed {
    if (is_array($text)) {
      foreach ($text as $key => $value) {
        $text[$key] = $this->replaceText($value);
      }
      return $text;
    }
    elseif (is_string($text)) {
      $replace = [
        'Starter Kit' => 'Recipe',
        'Starter kit' => 'Recipe',
        'starter kit' => 'recipe',
        'Starterkit' => 'recipe',
        'starterkit' => 'recipe',
      ];
      return strtr($text, $replace);
    }
    else {
      return $text;
    }
  }

  /**
   * Recursively copy directory contents from source to destination.
   *
   * @param string $source
   *   The source directory path.
   * @param string $destination
   *   The destination directory path.
   */
  protected function recursiveDirectoryCopy(string $source, string $destination): void {
    $dir = opendir($source);
    if (!file_exists($destination)) {
      mkdir($destination);
    }
    while (($file = readdir($dir)) !== FALSE) {
      if ($file != '.' && $file != '..') {
        if (is_dir($source . '/' . $file)) {
          $this->recursiveDirectoryCopy($source . '/' . $file, $destination . '/' . $file);
        }
        else {
          copy($source . '/' . $file, $destination . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

}
