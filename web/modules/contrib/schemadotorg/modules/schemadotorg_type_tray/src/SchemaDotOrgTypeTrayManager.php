<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_type_tray;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
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
   * @param \Drupal\Core\Extension\ModuleExtensionList $extensionListModule
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
    protected ModuleExtensionList $extensionListModule,
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

    $schema_type = $mapping->getSchemaType();

    // Get the Schema.org type's category and weight.
    [$type_category, $type_weight] = $this->getCategoryAndWeight($schema_type);

    // Exit if no type tray category is found for the Schema.org type.
    if (!$type_category) {
      return;
    }

    // Look for the Schema.org type's icon.
    $type_icon = $this->getFilePath($schema_type, 'icon');

    // Look for the Schema.org type's thumbnail.
    $type_thumbnail = $this->getFilePath($schema_type, 'thumbnail');

    // Add tray type settings to the node type's third party settings.
    // @see type_tray_form_node_type_form_alter()
    // @see type_tray_entity_builder()
    $node_type = $mapping->getTargetEntityBundleEntity();
    $existing_nodes_link_text = $this->configFactory
      ->get('schemadotorg_type_tray.settings')
      ->get('existing_nodes_link_text');
    $values = [
      'type_category' => $type_category,
      'type_thumbnail' => $type_thumbnail,
      'type_icon' => $type_icon,
      'existing_nodes_link_text' => $existing_nodes_link_text
        ? $this->t($existing_nodes_link_text, ['%type_label' => $node_type->label()])
        : '',
      'type_weight' => (string) $type_weight,
    ];
    foreach ($values as $key => $value) {
      $node_type->setThirdPartySetting('type_tray', $key, $value);
    }
    $node_type->save();
  }

  /**
   * {@inheritdoc}
   */
  public function syncCategories(array $schema_types): void {
    $config = $this->configFactory->getEditable('type_tray.settings');
    $existing_categories = $config->get('categories') ?? [];

    $schema_categories = [];
    foreach ($schema_types as $key => $schema_type) {
      $schema_categories[$key] = $existing_categories[$key] ?? $schema_type['label'];
    }

    $config->set('categories', $schema_categories + $existing_categories);
    $config->save();
  }

  /**
   * Get the type tray category and weight for a Schema.org type.
   *
   * @param string $schema_type
   *   A Schema.org type.
   *
   * @return array
   *   An array containing the type tray category and weight
   *   for a Schema.org type.
   */
  protected function getCategoryAndWeight(string $schema_type): array {
    // Build Schema.org type to type tray category lookup.
    $category_lookup = [];
    $type_tray_schema_types = $this->configFactory
      ->get('schemadotorg_type_tray.settings')
      ->get('schema_types');
    foreach ($type_tray_schema_types as $name => $type_tray_schema_type) {
      $category_lookup += array_fill_keys($type_tray_schema_type['types'], $name);
    }

    // Look for the Schema.org type's category.
    $breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs($schema_type);
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb_types = array_reverse($breadcrumb);
      foreach ($breadcrumb_types as $breadcrumb_type) {
        if (isset($category_lookup[$breadcrumb_type])) {
          $type_category = $category_lookup[$breadcrumb_type];
          $type_weights = array_flip($type_tray_schema_types[$type_category]['types']);
          $type_weight = $type_weights[$breadcrumb_type] - 20;
          return [$type_category, $type_weight];
        }
      }
    }
    return [NULL, NULL];
  }

  /**
   * Get a file path for a Schema.org type by breadcrumb and module.
   *
   * @param string $schema_type
   *   A Schema.org type.
   * @param string $type
   *   The type tray file type.
   *
   * @return string
   *   A file path for a Schema.org type by breadcrumb and module.
   */
  protected function getFilePath(string $schema_type, string $type): string {
    global $base_path;

    // Get installed module names with the 'schemadotorg_type_tray' module last.
    $module_names = array_keys($this->moduleHandler->getModuleList());
    $module_names = array_combine($module_names, $module_names);
    unset($module_names['schemadotorg_type_tray']);
    $module_names['schemadotorg_type_tray'] = 'schemadotorg_type_tray';

    // Look for the file path by breadcrumb and module.
    $breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs($schema_type);
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb_types = array_reverse($breadcrumb);
      foreach ($breadcrumb_types as $breadcrumb_type) {
        $file_name = $this->schemaNames->camelCaseToSnakeCase($breadcrumb_type);
        foreach ($module_names as $module_name) {
          $file_path = $this->extensionListModule->getPath($module_name) . "/images/schemadotorg_type_tray/$type/$file_name.png";
          if (file_exists($file_path)) {
            return $base_path . $file_path;
          }
        }
      }
    }
    return '';
  }

}
