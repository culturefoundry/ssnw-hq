<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_translation;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldConfigBase;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Schema.org translate manager.
 */
class SchemaDotOrgTranslationManager implements SchemaDotOrgTranslationManagerInterface {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgTranslationManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $fieldManager
   *   The entity field manager.
   * @param \Drupal\content_translation\ContentTranslationManagerInterface $contentTranslationManager
   *   The content translation manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityFieldManagerInterface $fieldManager,
    protected ContentTranslationManagerInterface $contentTranslationManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function enableMapping(SchemaDotOrgMappingInterface $mapping): void {
    if (!$this->isMappingTranslated($mapping)) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();

    $this->enableEntityType($entity_type_id, $bundle);
    $this->enableEntityFields($entity_type_id, $bundle);
  }

  /**
   * {@inheritdoc}
   */
  public function enableMappingField(FieldConfigBase $field_config): void {
    // Check that field is associated with Schema.org type mapping.
    $entity_type_id = $field_config->getTargetEntityTypeId();
    $bundle = $field_config->getTargetBundle();
    if (!$this->loadMapping($entity_type_id, $bundle)) {
      return;
    }

    // Check that the field supports translations.
    if (!$this->supportsFieldTranslations($field_config)) {
      return;
    }

    // Check that the field is translated.
    if (!$this->isFieldTranslated($field_config)) {
      $field_config->setTranslatable(FALSE);
      $field_config->save();
      return;
    }

    // Set translatable.
    $field_config->setTranslatable(TRUE);

    // Set third party settings.
    $field_type = $field_config->getType();
    switch ($field_type) {
      case 'image':
        $column_settings = [
          'alt' => 'alt',
          'title' => 'title',
          'file' => 0,
        ];
        $field_config->setThirdPartySetting('content_translation', 'translation_sync', $column_settings);
        break;
    }

    // Save config.
    $field_config->save();
  }

  /**
   * Enable translation for an entity type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The entity bundle.
   */
  protected function enableEntityType(string $entity_type_id, string $bundle): void {
    $this->contentTranslationManager->setEnabled($entity_type_id, $bundle, TRUE);
  }

  /**
   * Enable translation for an entity field.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The entity bundle.
   */
  protected function enableEntityFields(string $entity_type_id, string $bundle): void {
    $field_definitions = $this->fieldManager->getFieldDefinitions($entity_type_id, $bundle);
    foreach ($field_definitions as $field_definition) {
      $field_config = $field_definition->getConfig($bundle);
      $this->enableMappingField($field_config);
    }
  }

  /**
   * Determine if an entity supports translations.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return bool
   *   TRUE if an entity supports translations.
   */
  protected function supportsEntityTranslation(EntityInterface $entity): bool {
    $entity_type_id = $entity->getEntityTypeId();

    // Make sure the field is associate with a content entity.
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    return ($entity_type instanceof ContentEntityTypeInterface);
  }

  /**
   * Determine if a Schema.org mapping entity should be translated.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return bool
   *   TRUE if a Schema.org mapping entity should be translated.
   */
  protected function isMappingTranslated(SchemaDotOrgMappingInterface $mapping): bool {
    $config = $this->configFactory->get('schemadotorg_translation.settings');

    // Check excluded Schema.org type.
    $excluded_schema_types = $config->get('excluded_schema_types');
    $schema_type = $mapping->getSchemaType();
    if ($this->schemaTypeManager->isSubTypeOf($schema_type, $excluded_schema_types)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Determine if a field supports translation.
   *
   * @param \Drupal\Core\Field\FieldConfigBase $field_config
   *   The field.
   *
   * @return bool
   *   TRUE if a field supports translation.
   *
   * @see _content_translation_form_language_content_settings_form_alter()
   */
  protected function supportsFieldTranslations(FieldConfigBase $field_config): bool {
    $field_name = $field_config->getName();
    $entity_type_id = $field_config->getTargetEntityTypeId();

    // Computed field always support translations.
    if ($field_config->isComputed()) {
      return TRUE;
    }

    // Make sure the field is associate with a content entity.
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    if (!$entity_type instanceof ContentEntityTypeInterface) {
      return FALSE;
    }

    // Get field storage definition.
    $storage_definitions = $this->fieldManager->getFieldStorageDefinitions($entity_type_id);
    $storage_definition = $storage_definitions[$field_name] ?? NULL;
    if (!$storage_definition) {
      return FALSE;
    }

    // Check whether translatability should be configurable for a field.
    // @see _content_translation_is_field_translatability_configurable
    return $storage_definition->isTranslatable() &&
      $storage_definition->getProvider() != 'content_translation' &&
      !in_array($storage_definition->getName(), [$entity_type->getKey('langcode'), $entity_type->getKey('default_langcode'), 'revision_translation_affected']);
  }

  /**
   * Determine if a field should be translated.
   *
   * @param \Drupal\Core\Field\FieldConfigBase $field_config
   *   The field.
   *
   * @return bool
   *   TRUE if a field should be translated.
   */
  protected function isFieldTranslated(FieldConfigBase $field_config): bool {
    $entity_type_id = $field_config->getTargetEntityTypeId();
    $bundle = $field_config->getTargetBundle();
    $field_name = $field_config->getName();
    $field_type = $field_config->getType();

    // Check that the entity has translation enabled.
    if (!$this->contentTranslationManager->isEnabled($entity_type_id, $bundle)) {
      return FALSE;
    }

    $config = $this->configFactory->get('schemadotorg_translation.settings');

    // Check excluded Schema.org properties.
    $schema_type = $field_config->schemaDotOrgType ?? NULL;
    $schema_property = $field_config->schemaDotOrgProperty ?? NULL;
    if (!$schema_type || !$schema_property) {
      $mapping = $this->loadMapping($entity_type_id, $bundle);
      if ($mapping) {
        $schema_type = $mapping->getSchemaType();
        $schema_properties = $mapping->getSchemaProperties();
        $schema_property = $schema_properties[$field_name] ?? '';
      }
    }
    if ($schema_type && $schema_property) {
      $excluded_schema_properties = $config->get('excluded_schema_properties');
      if (in_array($schema_property, $excluded_schema_properties)
        || in_array("$schema_type--$schema_property", $excluded_schema_properties)) {
        return FALSE;
      }
    }

    // Check excluded field names.
    if (in_array($field_name, $config->get('excluded_field_names'))) {
      return FALSE;
    }

    // Check included field names.
    if (in_array($field_name, $config->get('included_field_names'))) {
      return TRUE;
    }

    // Check included field types.
    if (in_array($field_type, $config->get('included_field_types'))) {
      return TRUE;
    }

    return FALSE;
  }

}
