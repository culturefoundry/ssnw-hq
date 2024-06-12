<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

use Drupal\Component\Utility\NestedArray;
use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org taxonomy vocabulary property manager.
 */
class SchemaDotOrgTaxonomyPropertyVocabularyManager implements SchemaDotOrgTaxonomyPropertyVocabularyManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgTaxonomyTrait;

  /**
   * Constructs a SchemaDotOrgTaxonomyPropertyVocabularyManager object.
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
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\content_translation\ContentTranslationManagerInterface|null $contentTranslationManager
   *   The content translation manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected MessengerInterface $messenger,
    protected LoggerChannelFactoryInterface $logger,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected ?ContentTranslationManagerInterface $contentTranslationManager = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function propertyFieldTypeAlter(array &$field_types, string $schema_type, string $schema_property): void {
    $property_vocabulary_settings = $this->getPropertyVocabularySettings($schema_property);
    if ($property_vocabulary_settings) {
      $field_types = ['field_ui:entity_reference:taxonomy_term' => 'field_ui:entity_reference:taxonomy_term'] + $field_types;
    }
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
    // Make sure the field type is set to 'entity_reference' with the target type
    // set to 'taxonomy_term'.
    $target_type = NestedArray::getValue($field_storage_values, ['settings', 'target_type']);
    $is_entity_reference_taxonomy_term = ($field_storage_values['type'] === 'entity_reference'
      && $target_type === 'taxonomy_term');

    if (!$is_entity_reference_taxonomy_term) {
      return;
    }

    // Check to see if the Schema.org property has vocabulary settings.
    $property_vocabulary_settings = $this->getPropertyVocabularySettings($schema_property);
    if (!$property_vocabulary_settings) {
      return;
    }

    // Set default vocabulary id and label from field name and field label.
    $property_definition = $this->schemaTypeManager->getProperty($schema_property);
    $property_vocabulary_settings += [
      'id ' => $field_storage_values['field_name'],
      'label' => $field_values['label'],
      'description' => $property_definition['comment'],
    ];

    $vocabulary_id = $property_vocabulary_settings['id'];
    $this->createVocabulary($vocabulary_id, $property_vocabulary_settings);

    // Set the term reference's default handler, target bundle, and allow
    // the creation of terms if they don't already exist.
    // @see \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilder::setDefaultFieldValues
    $field_values['settings'] = [
      'handler' => 'default:taxonomy_term',
      'handler_settings' => [
        'target_bundles' => [$vocabulary_id => $vocabulary_id],
        'auto_create' => TRUE,
      ],
    ];

    // Use the entity reference tree and tags widget.
    if ($this->moduleHandler->moduleExists('entity_reference_tree')) {
      $widget_id = 'entity_reference_tree';
      $widget_settings = [
        'theme' => 'default',
        'dots' => 0,
        'size' => 60,
        'placeholder' => '',
        'match_operator' => 'CONTAINS',
        'match_limit' => 10,
        'dialog_title' => (string) $this->t('Select items'),
        'label' => (string) $this->t('Select items'),
      ];
    }
    else {
      $widget_id = 'entity_reference_autocomplete_tags';
    }
  }

  /**
   * Get a taxonomy Schema.org default property.
   *
   * @param string $property
   *   The Schema.org property.
   *
   * @return array|null
   *   A Schema.org default property vocabulary definition.
   */
  protected function getPropertyVocabularySettings(string $property): ?array {
    return $this->configFactory->get('schemadotorg_taxonomy.settings')
      ->get("schema_property_vocabularies.$property");
  }

}
