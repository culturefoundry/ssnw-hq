<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_metatag;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\metatag\MetatagTagPluginManager;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\token\TokenInterface;

/**
 * Schema.org meta tag manager.
 */
class SchemaDotOrgMetatagManager implements SchemaDotOrgMetatagManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgMetatagManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\token\TokenInterface $token
   *   The token service.
   * @param \Drupal\metatag\MetatagTagPluginManager $tagManager
   *   The metatag tag plugin manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected TokenInterface $token,
    protected MetatagTagPluginManager $tagManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * Implements hook_schemadotorg_mapping_insert().
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $field_name = 'field_metatag';

    // Only add the meta tags field to node types.
    if ($entity_type_id !== 'node') {
      return;
    }

    // Create meta tag field storage.
    $field_storage_config_storage = $this->entityTypeManager->getStorage('field_storage_config');
    if (!$field_storage_config_storage->load("$entity_type_id.$field_name")) {
      $field_storage_config_storage->create([
        'field_name' => $field_name,
        'entity_type' => $entity_type_id,
        'type' => 'metatag',
      ])->save();
    }

    // Create meta tag field instance.
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    if (!$field_config_storage->load("$entity_type_id.$bundle.$field_name")) {
      $field_config_storage->create([
        'label' => $this->t('Meta tags'),
        'field_name' => $field_name,
        'entity_type' => $entity_type_id,
        'bundle' => $bundle,
        'type' => 'metatag',
      ])->save();
    }

    // Set meta tag component in the default form display.
    $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, 'default');
    $form_display->setComponent($field_name, [
      'type' => 'metatag_firehose',
      'settings' => [
        'sidebar' => TRUE,
        'use_details' => TRUE,
      ],
      'weight' => 99,
    ]);
    $form_display->save();

    // Set metatag default groups for the content type.
    $default_groups = $this->configFactory
      ->get('schemadotorg_metatag.settings')
      ->get('default_groups');
    if ($default_groups) {
      $this->configFactory
        ->getEditable('metatag.settings')
        ->set("entity_type_groups.node.$bundle", array_combine($default_groups, $default_groups))
        ->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function mappingPresave(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    // Make sure the mapping's target entity supports metatags.
    $entity_type_id = $mapping->getTargetEntityTypeId();
    if (!$this->getMetatagDefaultsStorage()->load($entity_type_id)) {
      return;
    }

    $this->setEntityTypeDefaultTags($mapping);
    $this->setBundleDefaultTags($mapping);
  }

  /**
   * Sets the default tags for a Schema.org mapping's entity type.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   */
  protected function setEntityTypeDefaultTags(SchemaDotOrgMappingInterface $mapping): void {
    $entity_type_id = $mapping->getTargetEntityTypeId();

    // Get default tags.
    $default_tags = $this->configFactory
      ->get('schemadotorg_metatag.settings')
      ->get("default_tags.$entity_type_id");

    // Get new Schema.org properties.
    $schema_properties = $mapping->getNewSchemaProperties();
    $schema_properties = array_combine($schema_properties, $schema_properties);
    // Append custom properties include url, Schema.type, and types.
    if ($mapping->isNew()) {
      $schema_properties['url'] = 'url';
    }

    $default_tags = array_intersect_key($default_tags, $schema_properties);
    $tags = [];
    foreach ($default_tags as $value) {
      $tags += $value;
    }

    $id = $mapping->getTargetEntityTypeId();
    $label = (string) $mapping->getTargetEntityTypeDefinition()->getLabel();
    $this->overwriteDefaultTags($entity_type_id, $id, $label, $tags);
  }

  /**
   * Sets the default tags for a Schema.org mapping's bundle.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   */
  protected function setBundleDefaultTags(SchemaDotOrgMappingInterface $mapping): void {
    $entity_type_id = $mapping->getTargetEntityTypeId();

    // Get default tags.
    $default_tags = $this->configFactory
      ->get('schemadotorg_metatag.settings')
      ->get("default_tags.$entity_type_id");

    // Set parts and patterns for collecting tags.
    $parts = [
      'bundle' => $mapping->getTargetBundle(),
      'schema_type' => $mapping->getSchemaType(),
    ];
    $patterns = [
      ['schema_type', 'schema_property'],
      ['bundle', 'schema_property'],
      ['schema_type'],
      ['schema_type', 'bundle'],
    ];

    $tags = [];
    $schema_properties = $mapping->getNewSchemaProperties();
    foreach ($schema_properties as $schema_property) {
      $settings = $this->schemaTypeManager->getSetting(
        $default_tags,
        $parts + ['schema_property' => $schema_property],
        ['multiple' => TRUE],
        $patterns,
      ) ?? [];
      foreach ($settings as $value) {
        $tags += $value;
      }
    }

    $id = $mapping->getTargetEntityTypeId() . '--' . $mapping->getTargetBundle();
    $label = $mapping->getTargetEntityTypeDefinition()->getLabel()
      . ': '
      . $mapping->getTargetEntityBundleEntity()->label();
    $this->overwriteDefaultTags($entity_type_id, $id, $label, $tags);
  }

  /**
   * Overwrites the default meta tags.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $id
   *   The ID of the metatag defaults.
   * @param string $label
   *   The label of the metatag defaults.
   * @param array $tags
   *   The array of tags to overwrite.
   */
  protected function overwriteDefaultTags(string $entity_type_id, string $id, string $label, array $tags): void {
    // Load or create meta tag defaults.
    /** @var \Drupal\metatag\Entity\MetatagDefaults $metatag_defaults */
    $metatag_defaults = $this->getMetatagDefaultsStorage()->load($id)
      ?? $this->getMetatagDefaultsStorage()->create([
        'id' => $id,
        'label' => $label,
      ]);

    // Make sure tags exist by intersecting with tag definitions.
    $tags = array_intersect_key($tags, $this->tagManager->getDefinitions());

    // Make sure the tags tokens are valid.
    foreach ($tags as $name => $value) {
      if ($this->token->getInvalidTokensByContext($value, [$entity_type_id])) {
        unset($tags[$name]);
      }
    }

    // Don't overwrite any existing tags.
    $tags = array_diff_key($tags, $metatag_defaults->get('tags'));

    if ($tags) {
      $metatag_defaults->overwriteTags($tags);
      $metatag_defaults->save();
    }
  }

  /**
   * Gets the metatag defaults storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The metatag defaults storage.
   */
  protected function getMetatagDefaultsStorage(): EntityStorageInterface {
    return $this->entityTypeManager
      ->getStorage('metatag_defaults');
  }

}
