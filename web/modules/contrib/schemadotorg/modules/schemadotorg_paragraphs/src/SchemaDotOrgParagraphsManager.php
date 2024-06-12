<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_paragraphs;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldConfigInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org paragraphs manager.
 */
class SchemaDotOrgParagraphsManager implements SchemaDotOrgParagraphsManagerInterface {

  /**
   * Constructs a SchemaDotOrgParagraphsManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingPresave(SchemaDotOrgMappingInterface $mapping): void {
    if (!$mapping->isNew() || $mapping->getTargetEntityTypeId() !== 'paragraph') {
      return;
    }

    $schema_type = $mapping->getSchemaType();
    if (!$this->useParagraphsLibrary($schema_type)) {
      return;
    }

    /** @var \Drupal\paragraphs\ParagraphsTypeInterface $paragraph_type */
    $paragraph_type = $mapping->getTargetEntityBundleEntity();
    $paragraph_type->setThirdPartySetting('paragraphs_library', 'allow_library_conversion', TRUE);
    $paragraph_type->save();
  }

  /**
   * {@inheritdoc}
   */
  public function propertyFieldAlter(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void {
    // Check that the field is an entity_reference_revisions type that is
    // targeting paragraphs.
    if ($field_storage_values['type'] !== 'entity_reference_revisions'
      || $field_storage_values['settings']['target_type'] !== 'paragraph') {
      return;
    }

    // Widget.
    $widget_id = 'paragraphs';

    // Set the default paragraph type to 'none', to provide a cleaner initial UX
    // because all Schema.org fields/properties are optional.
    $widget_settings['default_paragraph_type'] = '_none';
  }

  /**
   * {@inheritdoc}
   */
  public function fieldConfigPresave(FieldConfigInterface $field_config): void {
    // Check that the field is an entity_reference_revisions type that is
    // targeting paragraphs.
    if ($field_config->getType() !== 'entity_reference_revisions'
      || $field_config->getSetting('target_type') !== 'paragraph') {
      return;
    }

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');

    // If any of the target bundles use the Paragraphs library,
    // append 'from_library' to target bundles.
    $target_type = $field_config->getSetting('target_type');

    $handler_id = $field_config->getSetting('handler');
    $handler_settings = $field_config->getSetting('handler_settings');

    $target_bundles = $handler_settings['target_bundles'] ?? [];
    foreach ($target_bundles as $target_bundle) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $target_mapping */
      $target_mappings = $mapping_storage->loadByProperties([
        'target_entity_type_id' => $target_type,
        'target_bundle' => $target_bundle,
      ]);
      if (!$target_mappings) {
        continue;
      }

      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $target_mapping */
      $target_mapping = reset($target_mappings);
      $target_schema_type = $target_mapping->getSchemaType();
      if ($this->useParagraphsLibrary($target_schema_type)) {
        $target_bundles['from_library'] = 'from_library';
        break;
      }
    }

    // Set the target bundles drag and drop order.
    if (!str_starts_with($handler_id, 'schemadotorg')) {
      $handler_settings['target_bundles_drag_drop'] = [];
      $weight = 0;
      foreach ($target_bundles as $target_bundle) {
        $handler_settings['target_bundles_drag_drop'][$target_bundle] = [
          'weight' => $weight,
          'enabled' => TRUE,
        ];
        $weight++;
      }
    }

    $handler_settings['target_bundles'] = $target_bundles;
    $field_config->setSetting('handler_settings', $handler_settings);
  }

  /**
   * Check if a Schema.org type should be added to Paragraphs library.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return bool
   *   TRUE if a Schema.org type should be added to Paragraphs library.
   */
  protected function useParagraphsLibrary(string $type): bool {
    if (!$this->moduleHandler->moduleExists('paragraphs_library')) {
      return FALSE;
    }

    $paragraphs_library = $this->configFactory
      ->get('schemadotorg_paragraphs.settings')
      ->get('paragraphs_library');

    return $this->schemaTypeManager->isSubTypeOf($type, $paragraphs_library);
  }

}
