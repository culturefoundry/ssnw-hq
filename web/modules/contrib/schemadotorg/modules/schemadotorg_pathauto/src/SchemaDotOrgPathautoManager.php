<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_pathauto;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\pathauto\Entity\PathautoPattern;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\token\Token;

/**
 * Schema.org pathauto manager.
 */
class SchemaDotOrgPathautoManager implements SchemaDotOrgPathautoManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgPathautoManager object.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected Token $token,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $schema_type = $mapping->getSchemaType();

    $patterns = $this->configFactory->get('schemadotorg_pathauto.settings')->get('patterns');
    $parts = ['entity_type_id' => $entity_type_id, 'schema_type' => $schema_type];
    $pattern = $this->schemaTypeManager->getSetting($patterns, $parts);
    if (!$pattern) {
      return;
    }

    $pattern_name = array_search($pattern, $patterns);
    [, $pattern_schema_type] = explode('--', $pattern_name);

    // Define pathauto pattern id and label.
    $entity_type_definition = $mapping->getTargetEntityTypeDefinition();
    $schema_type_definition = $this->schemaTypeManager->getType($pattern_schema_type);
    $pathauto_pattern_id = 'schema_' . $entity_type_id . '_' . $schema_type_definition['drupal_name'];
    $pathauto_pattern_label = 'Schema.org: ' . $entity_type_definition->getCollectionLabel() . ' - ' . $schema_type_definition['drupal_label'];

    // Load or create initial pathauto pattern with a selection condition.
    $pathauto_pattern = PathautoPattern::load($pathauto_pattern_id);
    if (!$pathauto_pattern) {
      $pathauto_pattern = PathautoPattern::create([
        'id' => $pathauto_pattern_id,
        'label' => $pathauto_pattern_label,
        'type' => 'canonical_entities:' . $entity_type_id,
        'pattern' => $pattern,
        'weight' => -10,
      ]);
      $pathauto_pattern->addSelectionCondition([
        'id' => 'entity_bundle:' . $entity_type_id,
        'negate' => FALSE,
        'context_mapping' => [
          $entity_type_id => $entity_type_id,
        ],
      ]);
    }

    // Get the default selection condition.
    $selection_conditions_configuration = $pathauto_pattern->getSelectionConditions()->getConfiguration();
    $selection_condition_id = array_key_first($selection_conditions_configuration);
    $selection_condition = $pathauto_pattern->getSelectionConditions()->get($selection_condition_id);

    // Append the Schema.org mapping bundle to the selection condition.
    $configuration = $selection_condition->getConfiguration();
    $configuration['bundles'][$bundle] = $bundle;
    ksort($configuration['bundles']);
    $selection_condition->setConfiguration($configuration);

    $pathauto_pattern->save();
  }

  /**
   * Alter the metadata about available placeholder tokens and token types.
   *
   * @param array $info
   *   The associative array of token definitions from hook_token_info().
   */
  public function tokenInfoAlter(array &$info): void {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface[] $mapping_types */
    $mapping_types = $this->getMappingTypeStorage()->loadMultiple();

    $entity_definitions = $this->entityTypeManager->getDefinitions();
    foreach ($mapping_types as $mapping_type) {
      $entity_type_id = $mapping_type->get('target_entity_type_id');
      $entity_info = $entity_definitions[$entity_type_id] ?? NULL;
      if (!$entity_info || !$entity_info->get('token_type')) {
        continue;
      }

      $token_type = $entity_info->get('token_type');
      $info['tokens'][$token_type]['schemadotorg']['base-path'] = [
        'name' => $this->t('Schema.org type base path'),
        'description' => $this->t('The Schema.org type base path of the @entity.', ['@entity' => mb_strtolower((string) $entity_info->getLabel())]),
      ];
      $info['tokens'][$token_type]['schemadotorg']['alternate-name'] = [
        'name' => $this->t('Schema.org alternate name or entity label'),
        'description' => $this->t("The Schema.org alternate name or the @entity label. When applicable, an alternate name can be used to provide a short label/title for URL aliases.", ['@entity' => mb_strtolower((string) $entity_info->getLabel())]),
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): ?array {
    $entity = $data[$type] ?? NULL;
    if (!$entity instanceof ContentEntityInterface) {
      return NULL;
    }

    $replacements = [];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'schemadotorg:base-path':
          $base_path = $this->getBasePath($entity);
          if ($base_path) {
            $replacements[$original] = $this->token->replace($base_path, [$entity->getEntityTypeId() => $entity], $options, $bubbleable_metadata);
          }
          break;

        case 'schemadotorg:alternate-name':
          $mapping = $this->getMappingStorage()->loadByEntity($entity);
          $alternate_field_name = ($mapping)
            ? $mapping->getSchemaPropertyFieldName('alternateName')
            : NULL;
          if ($alternate_field_name && $entity->hasField($alternate_field_name)) {
            $replacements[$original] = $entity->get($alternate_field_name)->value ?: $entity->label();
          }
          else {
            $replacements[$original] = $entity->label();
          }
          break;

      }
    }

    return $replacements;
  }

  /**
   * Get the base path for a content entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity.
   *
   * @return string|null
   *   The base path for a content entity.
   */
  protected function getBasePath(ContentEntityInterface $entity): ?string {
    // Check that the content entity is mapped to a Schema.org type.
    $mapping = $this->getMappingStorage()->loadByEntity($entity);
    if (!$mapping) {
      return NULL;
    }

    $base_paths = $this->configFactory->get('schemadotorg_pathauto.settings')->get('base_paths');
    $parts = [
      'entity_type_id' => $mapping->getTargetEntityTypeId(),
      'bundle' => $mapping->getTargetBundle(),
      'schema_type' => $mapping->getSchemaType(),
      'additional_type' => $this->getMappingStorage()->getAdditionalType($entity),
    ];
    return $this->schemaTypeManager->getSetting($base_paths, $parts);
  }

}
