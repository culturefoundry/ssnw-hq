<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_paragraphs;

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\file\Entity\File;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\paragraphs\Plugin\Field\FieldWidget\ParagraphsWidget;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org paragraphs manager.
 */
class SchemaDotOrgParagraphsManager implements SchemaDotOrgParagraphsManagerInterface {

  /**
   * Constructs a SchemaDotOrgParagraphsManager object.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleExtensionList $moduleExtensionList
   *   The module extension list.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected FileSystemInterface $fileSystem,
    protected ConfigFactoryInterface $configFactory,
    protected ModuleExtensionList $moduleExtensionList,
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

    if (!$this->useParagraphsLibrary($mapping)) {
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
    $widget_id = $widget_id ?? 'paragraphs';

    // Set the default paragraph type to 'none', to provide a cleaner initial UX
    // because all Schema.org fields/properties are optional.
    $widget_settings['default_paragraph_type'] = $widget_settings['default_paragraph_type'] ?? '_none';
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
    $handler_id = $field_config->getSetting('handler');
    $handler_settings = $field_config->getSetting('handler_settings');
    $target_bundles = $handler_settings['target_bundles'] ?? [];
    $is_schema_handler = ($handler_id === 'schemadotorg:paragraph');

    if ($field_config->isNew() || $is_schema_handler) {
      $target_type = $field_config->getSetting('target_type');
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
        if ($this->useParagraphsLibrary($target_mapping)) {
          $target_bundles['from_library'] = 'from_library';
          break;
        }
      }
    }

    // Set the target bundles drag and drop order.
    if (!$is_schema_handler) {
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
   * {@inheritdoc}
   */
  public function fieldWidgetCompleteFormAlter(array &$field_widget_complete_form, FormStateInterface $form_state, array $context): void {
    // Make sure the paragraphs field widget that is built using the
    // 'schemadotorg:paragraph' selection handler has the 'add from library' link
    // last because the paragraphs field widget only support sorted bundled the
    // 'default:paragraphs' selection handler.
    // @see \Drupal\paragraphs\Plugin\Field\FieldWidget\ParagraphsWidget::getAllowedTypes
    if ($context['widget'] instanceof ParagraphsWidget
      && $context['items'] instanceof EntityReferenceRevisionsFieldItemList
      && $context['items']->getFieldDefinition()->getSetting('handler') === 'schemadotorg:paragraph') {
      $parents = ['widget', 'add_more', 'operations', '#links', 'add_more_button_from_library'];
      $add_from_library_link = NestedArray::getValue($field_widget_complete_form, $parents);
      if ($add_from_library_link) {
        NestedArray::unsetValue($field_widget_complete_form, $parents);
        NestedArray::setValue($field_widget_complete_form, $parents, $add_from_library_link);
      }
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_presave().
   *
   * Save paragraph icons programmatically.
   *
   * @see \Drupal\paragraphs\Form\ParagraphsTypeForm::validateForm
   */
  public function paragraphsTypePresave(ParagraphsTypeInterface $paragraphs_type): void {
    if ($paragraphs_type->isSyncing()) {
      return;
    }

    if ($paragraphs_type->getIconFile()) {
      return;
    }

    // Get installed module names with the 'schemadotorg_paragraphs' module last.
    $module_names = array_keys($this->moduleHandler->getModuleList());
    $module_names = array_combine($module_names, $module_names);
    unset($module_names['schemadotorg_paragraphs']);
    $module_names['schemadotorg_paragraphs'] = 'schemadotorg_paragraphs';

    foreach ($module_names as $module_name) {
      $paragraphs_type_id = $paragraphs_type->id();
      while ($paragraphs_type_id) {
        $icon_path = $this->moduleExtensionList->getPath($module_name) . '/images/schemadotorg_paragraphs/' . $paragraphs_type_id . '.svg';
        if (file_exists($icon_path)) {
          $icon_directory = 'public://paragraphs_type_icon';
          $this->fileSystem->prepareDirectory($icon_directory, FileSystemInterface::MODIFY_PERMISSIONS | FileSystemInterface::CREATE_DIRECTORY);
          $file_uri = DeprecationHelper::backwardsCompatibleCall(
            currentVersion: \Drupal::VERSION,
            deprecatedVersion: '10.3',
            currentCallable: fn() => $this->fileSystem->copy($icon_path, $icon_directory . '/' . $this->fileSystem->basename($icon_path), FileExists::Replace),
            deprecatedCallable: fn() => $this->fileSystem->copy($icon_path, $icon_directory . '/' . $this->fileSystem->basename($icon_path), $this->fileSystem::EXISTS_REPLACE),
          );
          $file_entity = File::create(['uri' => $file_uri]);
          $file_entity->save();

          $paragraphs_type->set('icon_uuid', $file_entity->uuid());
          $paragraphs_type->set(
            'icon_default',
            'data:' . $file_entity->getMimeType() . ';base64,' . base64_encode(file_get_contents($file_entity->getFileUri())));
          break;
        }
        elseif (!str_contains($paragraphs_type_id, '_')) {
          break;
        }
        else {
          $paragraphs_type_id = preg_replace('/_[a-z]*$/', '', $paragraphs_type_id);
        }
      }
    }
  }

  /**
   * Check if a Schema.org mapping should be added to Paragraphs library.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return bool
   *   TRUE if a Schema.org mapping should be added to Paragraphs library.
   */
  protected function useParagraphsLibrary(SchemaDotOrgMappingInterface $mapping): bool {
    if (!$this->moduleHandler->moduleExists('paragraphs_library')) {
      return FALSE;
    }

    $paragraphs_library = $this->configFactory
      ->get('schemadotorg_paragraphs.settings')
      ->get('paragraphs_library');

    return (bool) $this->schemaTypeManager->getSetting($paragraphs_library, $mapping);
  }

}
