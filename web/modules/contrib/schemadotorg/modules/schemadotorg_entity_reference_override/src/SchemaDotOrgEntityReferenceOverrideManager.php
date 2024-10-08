<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_entity_reference_override;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org entity reference override manager.
 */
class SchemaDotOrgEntityReferenceOverrideManager implements SchemaDotOrgEntityReferenceOverrideManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgEntityReferenceOverrideManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

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
    // Make sure the field storage type is an entity reference.
    if (!in_array($field_storage_values['type'], ['entity_reference', 'entity_reference_override'])) {
      return;
    }

    // Check that the Schema.org property should use an entity reference override.
    $setting = $this->getSetting(
      $field_values['entity_type'],
      $field_values['bundle'],
      $schema_type,
      $schema_property,
    );
    if (!$setting) {
      return;
    }

    // Set the entity reference override format and label.
    $field_values['settings']['override_format'] = $setting['override_format'] ?? NULL;
    $field_values['settings']['override_label'] = $setting['override_label'] ?? (string) $this->t('Custom text');

    // Change the field storage type.
    $field_storage_values['type'] = 'entity_reference_override';

    // Update the widget id and settings.
    if (empty($widget_id)) {
      $widget_id = 'entity_reference_override_autocomplete';
      $widget_settings = [];
    }

    // Update the formatter id and settings.
    if (empty($formatter_id)) {
      $formatter_id = 'entity_reference_override_label';
      $formatter_settings = [
        'link' => TRUE,
        'override_action' => $setting['override_action'] ?? 'suffix',
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function singleElementEntityReferenceOverrideFormAlter(array &$element, FormStateInterface $form_state, array $context): void {
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items */
    $items = $context['items'];

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager
      ->getStorage('schemadotorg_mapping');
    $mapping = $mapping_storage->loadByEntity($items->getEntity());
    if (!$mapping) {
      return;
    }

    $schema_property = $mapping->getSchemaPropertyMapping($items->getName(), TRUE);
    if (!$schema_property) {
      return;
    }

    $setting = $this->getSetting(
      $mapping->getTargetEntityTypeId(),
      $mapping->getTargetBundle(),
      $mapping->getSchemaType(),
      $schema_property,
    );
    if (!$setting) {
      return;
    }

    foreach ($setting as $key => $value) {
      if (!str_starts_with($key, 'override_')) {
        $element['override']['#' . $key] = $value;
      }
    }

    // Ensure that #options includes the #default_value to #options.
    if (isset($element['override']['#options'])
      && !empty($element['override']['#default_value'])) {
      $element['override']['#options'][$element['override']['#default_value']] = $element['override']['#default_value'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function jsonLdSchemaPropertyAlter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void {
    // Make sure the field storage type is an entity reference.
    $field_type = $item->getFieldDefinition()->getType();
    if ($field_type !== 'entity_reference_override') {
      return;
    }

    // Get the Schema.org mapping.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager
      ->getStorage('schemadotorg_mapping');
    $mapping = $mapping_storage->loadByEntity($item->getEntity());
    if (!$mapping) {
      return;
    }

    // Get the Schema.org property.
    $field_name = $item->getFieldDefinition()->getName();
    $schema_property = $mapping->getSchemaPropertyMapping($field_name);
    if (!$schema_property) {
      return;
    }

    // Check that the Schema.org property should use an entity reference override.
    $setting = $this->getSetting(
      $mapping->getTargetEntityTypeId(),
      $mapping->getTargetBundle(),
      $mapping->getSchemaType(),
      $schema_property,
    );
    if (!$setting) {
      return;
    }

    // The override value which is the role.
    $override = $item->override;
    if (empty($override)) {
      return;
    }

    // Apply the override format.
    $override_format = $item->override_format;
    if ($override_format) {
      $override = check_markup($override, $override_format);
    }

    // Set the https://schema.org/Role for the value.
    $value = [
      '@type' => 'Role',
      'roleName' => $override,
      $schema_property => $value,
    ];
  }

  /**
   * Get the settings for a specific entity reference override.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   *
   * @return array|null
   *   The settings array for the entity reference override.
   */
  protected function getSetting(string $entity_type_id, string $bundle, string $schema_type, string $schema_property): ?array {
    $overrides_alter = $this->configFactory
      ->get('schemadotorg_entity_reference_override.settings')
      ->get('schema_properties');
    $parts = [
      'entity_type_id' => $entity_type_id,
      'bundle' => $bundle,
      'schema_type' => $schema_type,
      'schema_property' => $schema_property,
    ];
    return $this->schemaTypeManager->getSetting($overrides_alter, $parts);
  }

}
