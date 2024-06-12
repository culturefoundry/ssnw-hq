<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit;

use Drupal\Component\Serialization\Yaml;
use Drupal\config_rewrite\ConfigRewriter;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\devel_generate\DevelGeneratePluginManager;
use Drupal\schemadotorg\SchemaDotOrgConfigManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgDevelGenerateTrait;

/**
 * Schema.org Starter kit manager service.
 */
class SchemaDotOrgStarterkitManager implements SchemaDotOrgStarterkitManagerInterface {
  use SchemaDotOrgDevelGenerateTrait;

  /**
   * Constructs a SchemaDotOrgStarterkitManager object.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system service.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extensionListModule
   *   The module extension list.
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $moduleInstaller
   *   The module installer service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\config_rewrite\ConfigRewriter|null $configRewriter
   *   The configuration rewrite.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $schemaMappingManager
   *   The Schema.org mapping manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgConfigManagerInterface $schemaConfigManager
   *   The Schema.org config manager.
   * @param \Drupal\devel_generate\DevelGeneratePluginManager|null $develGenerateManager
   *   The Devel generate manager.
   */
  public function __construct(
    protected FileSystemInterface $fileSystem,
    protected ModuleExtensionList $extensionListModule,
    protected ModuleInstallerInterface $moduleInstaller,
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected ?ConfigRewriter $configRewriter,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgMappingManagerInterface $schemaMappingManager,
    protected SchemaDotOrgConfigManagerInterface $schemaConfigManager,
    protected ?DevelGeneratePluginManager $develGenerateManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isStarterkit(string $module): bool {
    $extensions = $this->extensionListModule->getList();
    if (!isset($extensions[$module])) {
      return FALSE;
    }

    $module_path = $this->extensionListModule->getPath($module);
    $module_schemadotorg_path = "$module_path/$module.schemadotorg_starterkit.yml";
    return file_exists($module_schemadotorg_path);
  }

  /**
   * {@inheritdoc}
   */
  public function getStarterkit($module): ?array {
    return $this->getStarterkits()[$module] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStarterkits(bool $installed = FALSE): array {
    $modules = $this->extensionListModule->getAllAvailableInfo();
    foreach ($modules as $module_name => $module_info) {
      if (!$this->isStarterkit($module_name)) {
        unset($modules[$module_name]);
      }
      elseif ($installed && !$this->moduleHandler->moduleExists($module_name)) {
        unset($modules[$module_name]);
      }
    }
    return $modules;
  }

  /**
   * {@inheritdoc}
   */
  public function getStarterkitSettings(string $module): FALSE|array {
    $settings = $this->getStarterkitSettingsData($module);
    if ($settings && !empty($settings['types'])) {
      foreach ($settings['types'] as $type => $type_defaults) {
        $settings['types'][$type] = $this->getStarterSettingsTypeDefaults($type, $type_defaults);
        // Unset empty type defaults.
        if (empty($settings['types'][$type])) {
          unset($settings['types'][$type]);
        }
      }
    }
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function install(string $module): void {
    $this->moduleInstaller->install([$module]);
  }

  /**
   * {@inheritdoc}
   */
  public function update(string $module): void {
    if (!$this->isStarterkit($module)) {
      return;
    }

    $this->rewriteConfig($module);
    $this->setupSchemaTypes($module);
    $this->rewriteConfig($module, '/\.yml$/i');
  }

  /**
   * Generate a Schema.org starter kit's content.
   *
   * @param string $module
   *   A Schema.org starter kit module name.
   */
  public function generate(string $module): void {
    $settings = $this->getStarterkitSettings($module);
    $types = array_keys($settings['types']);
    $this->develGenerate($types, 5);
  }

  /**
   * Kill a Schema.org starter kit's content.
   *
   * @param string $module
   *   A Schema.org starter kit module name.
   */
  public function kill(string $module): void {
    $settings = $this->getStarterkitSettings($module);
    $types = array_keys($settings['types']);
    $this->develGenerate($types, 0);
  }

  /**
   * {@inheritdoc}
   */
  public function preinstall(string $module): void {
    if (!$this->isStarterkit($module)) {
      return;
    }

    $this->rewriteConfig($module);
    $this->installDependencies($module);
    $this->setupSchemaTypes($module);
  }

  /**
   * {@inheritdoc}
   */
  public function installed(array $modules): void {
    if (!$this->configRewriter) {
      return;
    }

    $has_schema_config_rewrite = FALSE;
    foreach ($modules as $module) {
      if (!$this->isStarterkit($module)) {
        continue;
      }
      $module_path = $this->extensionListModule->getPath($module);
      $rewrite_dir = "$module_path/config/rewrite";
      $has_schema_config_rewrite = file_exists($rewrite_dir)
        && $this->fileSystem->scanDirectory($rewrite_dir, '/^schemadotorg.*\.yml$/i', ['recurse' => FALSE]);
      if ($has_schema_config_rewrite) {
        break;
      }
    }

    // Repair configuration if the starter kit has written any
    // schemadotorg* configuration.
    // @see https://www.drupal.org/project/config_rewrite/issues/3152228
    if ($has_schema_config_rewrite) {
      $this->schemaConfigManager->repair();
    }
  }

  /**
   * Rewrite Schema.org Blueprints related configuration.
   *
   * Scan the rewrite directory for schemadotorg.* config rewrites that need
   * to be installed before any Schema.org types are created.
   *
   * @param string $module
   *   A module.
   * @param string $mask
   *   The preg_match() regular expression for files to be included.
   *   Default to 'schemadotorg.*.yml' filed.
   */
  protected function rewriteConfig(string $module, string $mask = '/^schemadotorg.*\.yml$/i'): void {
    if (is_null($this->configRewriter)) {
      return;
    }

    $module_path = $this->extensionListModule->getPath($module);
    $rewrite_dir = "$module_path/config/rewrite";
    if (!file_exists($rewrite_dir)) {
      return;
    }

    $files = $this->fileSystem->scanDirectory($rewrite_dir, $mask, ['recurse' => FALSE]) ?: [];
    if (empty($files)) {
      return;
    }

    foreach ($files as $file) {
      $contents = file_get_contents($rewrite_dir . DIRECTORY_SEPARATOR . $file->name . '.yml');
      $rewrite = Yaml::decode($contents);
      $config = $this->configFactory->getEditable($file->name);
      $original_data = $config->getRawData();
      $rewrite = $this->configRewriter->rewriteConfig($original_data, $rewrite, $file->name, $module);
      $config->setData($rewrite)->save();
    }
  }

  /**
   * Install dependencies.
   *
   * @param string $module
   *   A module.
   */
  protected function installDependencies(string $module): void {
    $settings = $this->getStarterkitSettingsData($module);
    if ($settings && !empty($settings['dependencies'])) {
      $this->moduleInstaller->install($settings['dependencies']);
    }
  }

  /**
   * Set up a starter kit module based on the module's settings.
   *
   * @param string $module
   *   A module.
   */
  protected function setupSchemaTypes(string $module): void {
    $settings = $this->getStarterkitSettingsData($module);
    if ($settings && !empty($settings['types'])) {
      foreach ($settings['types'] as $type => $type_defaults) {
        [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);
        $defaults = $this->getStarterSettingsTypeDefaults($type, $type_defaults);
        $this->schemaMappingManager->createType($entity_type_id, $schema_type, $defaults);
      }
    }
  }

  /**
   * Get Schema.org starter kit settings from module's YAML file.
   *
   * @param string $module
   *   The module.
   *
   * @return false|array
   *   Schema.org starter kit settings for a module.
   */
  protected function getStarterkitSettingsData(string $module): FALSE|array {
    $module_path = $this->extensionListModule->getPath($module);
    $module_schemadotorg_path = "$module_path/$module.schemadotorg_starterkit.yml";
    if (!file_exists($module_schemadotorg_path)) {
      return FALSE;
    }

    $settings = Yaml::decode(file_get_contents($module_schemadotorg_path));
    return ($settings !== TRUE ? $settings : [])
      + ['hidden' => FALSE, 'dependencies' => [], 'types' => []];
  }

  /**
   * Get the Schema.org starter kit type defaults merged with the preconfigured defaults.
   *
   * @param string $type
   *   The entity type and Schema.org type.
   * @param array $type_defaults
   *   The Schema.org starter kit type defaults.
   *
   * @return array
   *   Schema.org starter kit type defaults merged with the preconfigured defaults.
   */
  protected function getStarterSettingsTypeDefaults(string $type, array $type_defaults): array {
    [$entity_type_id, $bundle, $schema_type] = $this->getMappingStorage()->parseType($type);
    if (!$this->entityTypeManager->hasDefinition($entity_type_id)) {
      return [];
    }

    $mapping_type = $this->loadMappingType($entity_type_id);
    if (!$mapping_type) {
      return [];
    }

    $mapping = $this->getMappingStorage()->loadByType($type);
    if ($mapping) {
      $bundle = $mapping->getTargetBundle();
      // Don't allow properties to be unexpectedly removed.
      if (!empty($type_defaults['properties'])) {
        $type_defaults['properties'] = array_filter($type_defaults['properties']);
      }
    }

    // Add properties that are explicitly set.
    if (isset($type_defaults['properties'])) {
      foreach ($type_defaults['properties'] as $property_name => $property) {
        // Skip adding properties that are already mapped.
        // @todo Skip custom fields.
        if ($mapping && $mapping->getSchemaPropertyFieldName($property_name)) {
          continue;
        }

        if (is_array($property)
          && empty($type_defaults['properties'][$property_name]['name'])) {
          $type_defaults['properties'][$property_name]['name'] = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;
        }
      }
    }

    return $this->schemaMappingManager->getMappingDefaults($entity_type_id, $bundle, $schema_type, $type_defaults);
  }

}
