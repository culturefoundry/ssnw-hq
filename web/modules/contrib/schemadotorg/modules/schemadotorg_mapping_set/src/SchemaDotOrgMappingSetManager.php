<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mapping_set;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\devel_generate\DevelGeneratePluginManager;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgDevelGenerateTrait;

/**
 * Schema.org mapping set manager.
 */
class SchemaDotOrgMappingSetManager implements SchemaDotOrgMappingSetManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgDevelGenerateTrait;

  /**
   * Constructs a SchemaDotOrgMappingSetCommands object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $schemaMappingManager
   *   The Schema.org mapping manager.
   * @param \Drupal\devel_generate\DevelGeneratePluginManager|null $develGenerateManager
   *   The Devel generate manager.
   */
  public function __construct(
    protected StateInterface $state,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgMappingManagerInterface $schemaMappingManager,
    protected ?DevelGeneratePluginManager $develGenerateManager = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isSetup(string $name): bool {
    $types = $this->getTypes($name);
    foreach ($types as $type) {
      $mapping = $this->getMappingStorage()->loadByType($type);
      if (!$mapping) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypes(string $name, bool $required = FALSE): array {
    $mapping_set = $this->configFactory
      ->get('schemadotorg_mapping_set.settings')
      ->get("sets.$name");
    if (empty($mapping_set)) {
      return [];
    }

    $types = array_combine($mapping_set['types'], $mapping_set['types']);

    // Prepend required types.
    if ($required) {
      $types = $this->getTypes('required') + $types;
    }

    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public function getMappingSets(string $entity_type_id, string $schema_type, ?bool $is_setup = NULL): array {
    $type = "$entity_type_id:$schema_type";

    $mapping_sets = $this->configFactory
      ->get('schemadotorg_mapping_set.settings')
      ->get('sets');
    foreach ($mapping_sets as $name => $mapping_set) {
      if (!in_array($type, $mapping_set['types'])) {
        unset($mapping_sets[$name]);
      }
      elseif ($is_setup === TRUE && !$this->isSetup($name)) {
        unset($mapping_sets[$name]);
      }
      elseif ($is_setup === FALSE && $this->isSetup($name)) {
        unset($mapping_sets[$name]);
      }
    }

    return $mapping_sets;
  }

  /**
   * {@inheritdoc}
   */
  public function setup(string $name): array {
    if ($this->isSetup($name)) {
      return [$this->t('Schema.org mapping set @name is already setup.', ['@name' => $name])];
    }

    // Setup required.
    if ($name !== 'required'
      && !$this->isSetup('required')
      && $this->getTypes('required')) {
      $this->setup('required');
    }

    $messages = [];

    $types = $this->getTypes($name);
    foreach ($types as $type) {
      [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);
      $existing_mapping = $this->getMappingStorage()->loadByType($type);
      if ($existing_mapping) {
        $t_args = ['@type' => $type];
        $messages[] = $this->t("Schema.org type '@type' already exists.", $t_args);
        unset($types[$type]);
      }
      else {
        $this->schemaMappingManager->createType($entity_type_id, $schema_type);
      }
    }

    if ($types) {
      // Display message.
      $t_args = ['@types' => implode(', ', $types)];
      $messages[] = $this->t('Schema.org types (@types) created.', $t_args);
    }

    return $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function teardown($name): array {
    if (!$this->isSetup($name)) {
      return [$this->t('Schema.org mapping set $name is not setup.')];
    }

    if ($this->develGenerateManager) {
      $this->kill($name);
    }

    $messages = [];

    // Reverse types to prevent entity reference errors.
    $types = $this->getTypes($name);
    $types = array_reverse($types, TRUE);

    // Filter the list of types to be deleted by removing used
    // or not mapped types.
    foreach ($types as $type) {
      [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);

      // Only delete the mapping and entity type is there is one remaining
      // instance setup.
      $mapping_sets = $this->getMappingSets($entity_type_id, $schema_type, TRUE);
      if (count($mapping_sets) > 1) {
        unset($types[$type]);
      }

      // Make sure the mapping exists.
      $mapping = $this->getMappingStorage()->loadBySchemaType($entity_type_id, $schema_type);
      if (!$mapping) {
        $t_args = ['@type' => $type];
        $messages[] = $this->t("Schema.org type '@type' already removed.", $t_args);
        unset($types[$type]);
      }
    }

    foreach ($types as $type) {
      [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);
      $mapping = $this->getMappingStorage()->loadByType($type);

      // Determine if the entity type bundle is default entity type that should
      // not be deleted.
      // (i.e. node:article, node:page, taxonomy_term:tags, etc...)
      $target_entity_id = $mapping->getTargetEntityTypeId();
      $target_entity_bundle = $mapping->getTargetEntityBundleEntity();
      $mapping_type = $this->loadMappingType($target_entity_id);
      $default_bundles = $mapping_type->getDefaultSchemaTypeBundles($schema_type);
      $is_default_bundle = isset($default_bundles[$target_entity_bundle->id()]);

      if ($is_default_bundle) {
        $options = ['delete-fields' => TRUE];
      }
      else {
        $options = ['delete-entity' => TRUE];
      }

      $this->schemaMappingManager->deleteType($entity_type_id, $schema_type, $options);
    }

    if ($types) {
      $t_args = ['@type' => implode(', ', $types)];
      $messages[] = $this->t('Schema.org types (@types) deleted.', $t_args);
    }

    return $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function generate($name): void {
    $types = $this->getTypes($name, TRUE);
    $this->develGenerate($types);
  }

  /**
   * {@inheritdoc}
   */
  public function kill($name): void {
    $types = $this->getTypes($name, TRUE);
    $this->develGenerate($types, 0);
  }

}
