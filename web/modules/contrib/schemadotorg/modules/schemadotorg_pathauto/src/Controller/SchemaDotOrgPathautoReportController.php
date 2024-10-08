<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_pathauto\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\schemadotorg_pathauto\SchemaDotOrgPathautoManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Schema.org Blueprints Pathauto routes.
 */
class SchemaDotOrgPathautoReportController extends ControllerBase {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org pathauto manager.
   */
  protected SchemaDotOrgPathautoManagerInterface $schemaPathAutoManager;

  /**
   * An associative array containing Schema.org mapping categories.
   */
  protected array $mappingCategories;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaPathAutoManager = $container->get('schemadotorg_pathauto.manager');
    return $instance;
  }

  /**
   * Builds the response.
   */
  public function index(): array {
    // Header.
    $header = [];
    $header['category'] = $this->t('Category');
    $header['bundle'] = $this->t('Content type');
    $header['schema_type'] = $this->t('Schema.org type');
    if ($this->moduleHandler()->moduleExists('schemadotorg_additional_type')) {
      $header['schema_additional_type'] = $this->t('Schema.org additional type');
    }
    $header['schema_pattern'] = $this->t('Schema.org pattern');
    $header['schema_base_path'] = $this->t('Schema.org base path');

    $patterns = $this->config('schemadotorg_pathauto.settings')->get('patterns');
    $base_paths = $this->config('schemadotorg_pathauto.settings')->get('base_paths');

    // Rows.
    $rows = [];
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
    $mappings = $this->getMappingStorage()->loadMultiple();
    foreach ($mappings as $mapping) {
      if ($mapping->getTargetEntityTypeId() !== 'node') {
        continue;
      }

      $mapping_id = $mapping->id();

      $parts = [
        'entity_type_id' => $mapping->getTargetEntityTypeId(),
        'bundle' => $mapping->getTargetBundle(),
        'schema_type' => $mapping->getSchemaType(),
      ];
      $schema_pattern = $this->schemaTypeManager->getSetting($patterns, $parts);
      $schema_base_path = $this->schemaTypeManager->getSetting($base_paths, $mapping);

      $category = $this->getMappingCategory($mapping);
      $category_name = $category['name'];

      $row = [];
      $row['category'] = $category['label'];
      $bundle_entity = $mapping->getTargetEntityBundleEntity();
      $row['bundle'] = [
        'data' => ($bundle_entity)
          ? $bundle_entity->toLink($bundle_entity->label(), 'edit-form')->toRenderable()
          : $mapping->getTargetBundle(),
      ];
      $row['schema_type'] = $mapping->getSchemaType();
      if ($this->moduleHandler()->moduleExists('schemadotorg_additional_type')) {
        $row['schema_additional_type'] = '';
      }
      $row['schema_pattern'] = $schema_pattern;
      $row['schema_base_path'] = $schema_base_path;
      $rows["$category_name:$mapping_id"] = $row;

      if ($this->moduleHandler()->moduleExists('schemadotorg_additional_type')) {
        $additional_types = $this->getAdditionalTypes($mapping);
        if ($additional_types) {
          foreach ($additional_types as $additional_type_value => $additional_type_text) {
            $additional_type_base_path = $this->schemaTypeManager->getSetting($base_paths, $parts + ['additional_type' => $additional_type_value]);
            if ($additional_type_base_path && $additional_type_base_path !== $schema_base_path) {
              $row['schema_additional_type'] = $additional_type_text;
              $row['schema_base_path'] = $additional_type_base_path;
              $rows["$category_name:$mapping_id:$additional_type_value"] = $row;
              // Unset additional type with a base path.
              unset($additional_types[$additional_type_value]);
            }
          }
          // If all tbe additional types have base paths,
          // remove the Schema.org mapping without a base path.
          if (empty($additional_types) && empty($schema_base_path)) {
            unset($rows["$category_name:$mapping_id"]);
          }
        }
      }

    }
    ksort($rows);

    $build = [];
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#sticky' => TRUE,
      '#empty' => $this->t('No Schema.org mappings found.'),
      '#attributes' => ['class' => ['schemadotorg-report-table']],
    ];
    $build['#attached']['library'][] = 'schemadotorg_report/schemadotorg_report';
    return $build;
  }

  /**
   * Retrieves the additional types for a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mappings.
   *
   * @return array|null
   *   The additional types if found, otherwise NULL.
   */
  protected function getAdditionalTypes(SchemaDotOrgMappingInterface $mapping): ?array {
    $additional_type_field_name = $mapping->getSchemaPropertyFieldName('additionalType');
    if (!$additional_type_field_name) {
      return NULL;
    }

    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load("node.$additional_type_field_name");
    if (!$field_storage_config) {
      return NULL;
    }
    return options_allowed_values($field_storage_config);
  }

  /**
   * Get the category definition for a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   *
   * @return array
   *   The category definition for a Schema.org mapping.
   */
  protected function getMappingCategory(SchemaDotOrgMappingInterface $mapping): array {
    if (!isset($this->mappingCategories)) {
      $categories = $this->config('schemadotorg.settings')
        ->get('schema_types.categories');
      $this->mappingCategories = [];
      foreach ($categories as $category_name => $category_definition) {
        foreach ($category_definition['types'] as $category_type) {
          $this->mappingCategories[$category_type] = $category_definition + ['name' => $category_name];
        }
      }
    }
    $setting = $this->schemaTypeManager->getSetting($this->mappingCategories, $mapping) ?? [];
    return $setting + [
      'name' => 'zzz_other',
      'label' => (string) $this->t('Other'),
      'color' => '#ffffcc',
    ];
  }

}
