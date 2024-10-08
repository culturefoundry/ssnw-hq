<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org taxonomy vocabulary property manager.
 */
class SchemaDotOrgTaxonomyDefaultVocabularyManager implements SchemaDotOrgTaxonomyDefaultVocabularyManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgTaxonomyTrait;

  /**
   * Constructs a SchemaDotOrgTaxonomyDefaultVocabularyManager object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger channel factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilderInterface $schemaEntityTypeBuilder
   *   The Schema.org entity type builder.
   * @param \Drupal\content_translation\ContentTranslationManagerInterface|null $contentTranslationManager
   *   The content translation manager.
   */
  public function __construct(
    protected MessengerInterface $messenger,
    protected LoggerChannelFactoryInterface $logger,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgEntityTypeBuilderInterface $schemaEntityTypeBuilder,
    protected ?ContentTranslationManagerInterface $contentTranslationManager = NULL,
  ) {}

  /**
   * Add default vocabulary to content types when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    $entity_type = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();

    // Make sure we are adding default vocabularies to nodes.
    if ($entity_type !== 'node') {
      return;
    }

    $default_vocabularies = $this->configFactory->get('schemadotorg_taxonomy.settings')
      ->get('default_vocabularies');
    foreach ($default_vocabularies as $vocabulary_id => $vocabulary_settings) {
      $schema_types = $vocabulary_settings['schema_types'] ?? NULL;
      // Check if the default vocabulary is for a specific Schema.org type.
      if ($schema_types
        && !$this->schemaTypeManager->getSetting($schema_types, $mapping)) {
        continue;
      }

      // Make sure the vocabulary ID is a machine name.
      $vocabulary_id = preg_replace('/[^a-z0-9_]+/', '_', $vocabulary_id);

      // Create vocabulary.
      $vocabulary = $this->createVocabulary($vocabulary_id, $vocabulary_settings);

      $field = $vocabulary_settings + [
        // Default field settings.
        'type' => 'field_ui:entity_reference:taxonomy_term',
        'label' => $vocabulary->label(),
        'unlimited' => TRUE,
        // Entity type and bundle.
        'entity_type' => $entity_type,
        'bundle' => $bundle,
        'field_name' => 'field_' . $vocabulary_id,
        // Schema.org type and property.
        'schema_type' => $mapping->getSchemaType(),
        'schema_property' => '',
        // Additional defaults.
        'group' => $vocabulary_settings['group'] ?? NULL,
        'handler' => 'default:taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [$vocabulary_id => $vocabulary_id],
          'auto_create' => $vocabulary_settings['auto_create'] ?? FALSE,
        ],
      ];

      $this->schemaEntityTypeBuilder->addFieldToEntity(
        $entity_type,
        $bundle,
        $field
      );
    }
  }

}
