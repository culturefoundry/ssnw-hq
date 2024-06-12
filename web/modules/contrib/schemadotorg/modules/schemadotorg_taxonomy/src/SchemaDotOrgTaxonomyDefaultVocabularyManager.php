<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org taxonomy vocabulary property manager.
 */
class SchemaDotOrgTaxonomyDefaultVocabularyManager implements SchemaDotOrgTaxonomyDefaultVocabularyManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgTaxonomyTrait;

  /**
   * Constructs a SchemaDotOrgTaxonomyDefaultVocabularyManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger channel factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\content_translation\ContentTranslationManagerInterface|null $contentTranslationManager
   *   The content translation manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected MessengerInterface $messenger,
    protected LoggerChannelFactoryInterface $logger,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected ?ContentTranslationManagerInterface $contentTranslationManager = NULL,
  ) {}

  /**
   * Add default vocabulary to content types when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    $schema_type = $mapping->getSchemaType();
    $entity_type = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();

    // Make sure we are adding default vocabularies to nodes.
    if ($entity_type !== 'node') {
      return;
    }

    $default_field_groups = $this->configFactory->get('schemadotorg_taxonomy.settings')
      ->get('default_field_groups');
    $default_vocabularies = $this->configFactory->get('schemadotorg_taxonomy.settings')
      ->get('default_vocabularies');
    foreach ($default_vocabularies as $vocabulary_id => $vocabulary_settings) {
      // Check if the default vocabulary is for a specific Schema.org type.
      if (str_contains($vocabulary_id, '--')) {
        [$vocabulary_schema_type, $vocabulary_id] = explode('--', $vocabulary_id);
        if ($vocabulary_schema_type !== $schema_type) {
          continue;
        }
      }

      // Make sure the vocabulary ID is a machine name.
      $vocabulary_id = preg_replace('/[^a-z0-9_]+/', '_', $vocabulary_id);

      $field_name = 'field_' . $vocabulary_id;

      $group_name = 'group_' . ($vocabulary_settings['group'] ?? 'taxonomy');
      $group_label = $default_field_groups[$vocabulary_settings['group'] ?? 'taxonomy'] ?? NULL;

      // Create vocabulary.
      $vocabulary = $this->createVocabulary($vocabulary_id, $vocabulary_settings);

      // Create the field storage.
      $field_storage = FieldStorageConfig::loadByName('node', $field_name);
      if (!FieldStorageConfig::loadByName('node', $field_name)) {
        $field_storage = FieldStorageConfig::create([
          'field_name' => $field_name,
          'entity_type' => $entity_type,
          'type' => 'entity_reference',
          'settings' => ['target_type' => 'taxonomy_term'],
          'cardinality' => FieldStorageConfig::CARDINALITY_UNLIMITED,
        ]);
        $field_storage->save();
      }

      // Create the field instance.
      $field_config = FieldConfig::loadByName('node', $bundle, $field_name);
      if (!$field_config) {
        FieldConfig::create([
          'field_storage' => $field_storage,
          'bundle' => $bundle,
          'label' => $vocabulary->label(),
          'settings' => [
            'handler' => 'default:taxonomy_term',
            'handler_settings' => [
              'target_bundles' => [$vocabulary_id => $vocabulary_id],
              'auto_create' => $vocabulary_settings['auto_create'] ?? FALSE,
            ],
          ],
        ])->save();
      }

      // Create the form display component.
      $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type, $bundle);
      if ($this->moduleHandler->moduleExists('entity_reference_tree')) {
        $form_display->setComponent($field_name, [
          'type' => 'entity_reference_tree',
          'settings' => [
            'theme' => 'default',
            'dots' => 0,
            'size' => 60,
            'placeholder' => '',
            'match_operator' => 'CONTAINS',
            'match_limit' => 10,
            'dialog_title' => (string) $this->t('Select items'),
            'label' => (string) $this->t('Select items'),
          ],
        ]);
      }
      else {
        $form_display->setComponent($field_name, [
          'type' => 'entity_reference_autocomplete_tags',
        ]);
      }
      if ($this->moduleHandler->moduleExists('field_group') && $group_label) {
        $group = $form_display->getThirdPartySetting('field_group', $group_name);
        if (!$group) {
          $group = [
            'label' => $group_label,
            'children' => [],
            'parent_name' => '',
            // Same weight as meta tag sidebar.
            'weight' => 99,
            'format_type' => 'details',
            'format_settings' => ['open' => TRUE],
            'region' => 'content',
          ];
        }
        $group['children'][] = $field_name;
        $group['children'] = array_unique($group['children']);
        $form_display->setThirdPartySetting('field_group', $group_name, $group);
      }
      $form_display->save();

      // Create the view display component.
      $view_display = $this->entityDisplayRepository->getViewDisplay($entity_type, $bundle);
      $view_display->setComponent($field_name, [
        'type' => 'entity_reference_label',
      ]);
      if ($this->moduleHandler->moduleExists('field_group') && $group_label) {
        $group = $view_display->getThirdPartySetting('field_group', $group_name);
        if (!$group) {
          $group = [
            'label' => $group_label,
            'children' => [$field_name],
            'parent_name' => '',
            // Before links.
            'weight' => 99,
            'format_type' => 'fieldset',
            'format_settings' => [],
            'region' => 'content',
          ];
        }
        $group['children'][] = $field_name;
        $group['children'] = array_unique($group['children']);
        $view_display->setThirdPartySetting('field_group', $group_name, $group);
      }
      $view_display->save();
    }
  }

}
