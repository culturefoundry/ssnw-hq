<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_type_tray;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org type tray manager service.
 */
class SchemaDotOrgTypeTrayManager implements SchemaDotOrgTypeTrayManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgTypeTrayManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Extension\ModuleExtensionList $moduleExtensionList
   *   The module extension list.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected ModuleExtensionList $moduleExtensionList,
    protected ModuleHandlerInterface $moduleHandler,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    // Type tray is only applicable to nodes.
    $entity_type_id = $mapping->getTargetEntityTypeId();
    if ($entity_type_id !== 'node') {
      return;
    }

    // Sync categories.
    $this->syncCategories();

    // Get type tray values.
    $categories = $this->configFactory
      ->get('schemadotorg.settings')
      ->get('schema_types.categories');
    $settings = [];
    foreach ($categories as $category_name => $category_definition) {
      $type_weight = -20;
      foreach ($category_definition['types'] as $category_type) {
        $settings[$category_type] = [
          'type_category' => $category_name,
          'type_weight' => (string) $type_weight,
        ];
        $type_weight++;
      }
    }
    $values = $this->schemaTypeManager->getSetting($settings, $mapping) ?? [];
    $values += [
      'type_category' => '',
      'type_weight' => (string) 0,
      'type_icon' => $this->getFilePath($mapping, 'icon'),
      'type_thumbnail' => $this->getFilePath($mapping, 'thumbnail'),
      'existing_nodes_link_text' => $this->getLinkText($mapping),
    ];

    // Add tray type values to the node type's third party settings.
    // @see type_tray_form_node_type_form_alter()
    // @see type_tray_entity_builder()
    $node_type = $mapping->getTargetEntityBundleEntity();
    foreach ($values as $key => $value) {
      $node_type->setThirdPartySetting('type_tray', $key, $value);
    }
    $node_type->save();
  }

  /**
   * {@inheritdoc}
   */
  public function syncCategories(): void {
    $config = $this->configFactory->getEditable('type_tray.settings');
    $existing_categories = $config->get('categories') ?? [];

    $categories = [];
    $schema_type_categories = $this->configFactory->get('schemadotorg.settings')
      ->get('schema_types.categories');
    foreach ($schema_type_categories as $category_name => $category_definition) {
      $categories[$category_name] = $existing_categories[$category_name]
        ?? $category_definition['label'];
    }

    $config->set('categories', $categories + $existing_categories);
    $config->save();
  }

  /**
   * Get a file path for a Schema.org type by breadcrumb and module.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   * @param string $type
   *   The type tray file type.
   *
   * @return string
   *   A file path for a Schema.org type by breadcrumb and module.
   */
  protected function getFilePath(SchemaDotOrgMappingInterface $mapping, string $type): string {
    // Get installed module names with the 'schemadotorg_type_tray' module last.
    $module_names = array_keys($this->moduleHandler->getModuleList());
    $module_names = array_combine($module_names, $module_names);
    unset($module_names['schemadotorg_type_tray']);
    $module_names['schemadotorg_type_tray'] = 'schemadotorg_type_tray';

    // Look for the file path by bundle.
    $bundle = $mapping->getTargetBundle();
    foreach ($module_names as $module_name) {
      $file_path = $this->moduleExtensionList->getPath($module_name) . "/images/schemadotorg_type_tray/$type/$bundle.png";
      if (file_exists($file_path)) {
        return $this->getBasePath() . $file_path;
      }
    }

    // Look for the file path by breadcrumb.
    $breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs($mapping->getSchemaType());
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb_types = array_reverse($breadcrumb);
      foreach ($breadcrumb_types as $breadcrumb_type) {
        $file_name = $this->schemaNames->camelCaseToSnakeCase($breadcrumb_type);
        foreach ($module_names as $module_name) {
          $file_path = $this->moduleExtensionList->getPath($module_name) . "/images/schemadotorg_type_tray/$type/$file_name.png";
          if (file_exists($file_path)) {
            return $this->getBasePath() . $file_path;
          }
        }
      }
    }
    return '';
  }

  /**
   * Get the base path.
   *
   * This method accounts for recipes that set '/core/scripts' as the base path.
   *
   * @return string
   *   The base path.
   */
  protected function getBasePath(): string {
    global $base_path;
    return ($base_path === 'core/scripts/') ? '/' : $base_path;
  }

  /**
   * Get the link text for the given Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The link text
   */
  protected function getLinkText(SchemaDotOrgMappingInterface $mapping): TranslatableMarkup|string {
    $node_type = $mapping->getTargetEntityBundleEntity();
    $existing_nodes_link_text = $this->configFactory
      ->get('schemadotorg_type_tray.settings')
      ->get('existing_nodes_link_text');
    return $existing_nodes_link_text
      ? $this->t($existing_nodes_link_text, ['%type_label' => $node_type->label()])
      : '';
  }

}
