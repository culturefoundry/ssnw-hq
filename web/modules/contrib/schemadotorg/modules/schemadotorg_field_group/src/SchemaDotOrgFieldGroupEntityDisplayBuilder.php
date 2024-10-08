<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_field_group;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field_group\Form\FieldGroupAddForm;
use Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org field group entity display builder service.
 */
class SchemaDotOrgFieldGroupEntityDisplayBuilder implements SchemaDotOrgFieldGroupEntityDisplayBuilderInterface {

  /**
   * Constructs a SchemaDotOrgFieldGroupEntityDisplayBuilder object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder
   *   The Schema.org entity display builder service.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingPreSave(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    if (!$mapping->isNew() || $mapping->getTargetEntityTypeId() !== 'node') {
      return;
    }

    // Set form and view display for existing title and body fields.
    $schema_type = $mapping->getSchemaType();
    $schema_properties = array_intersect_key(
      $mapping->getNewSchemaProperties(),
      ['title' => 'title', 'body' => 'body'],
    );
    if (!$schema_properties) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $field_defaults = [];

    foreach ($schema_properties as $field_name => $schema_property) {
      // Form display.
      $form_modes = $this->schemaEntityDisplayBuilder->getFormModes($entity_type_id, $bundle);
      foreach ($form_modes as $form_mode) {
        $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, $form_mode);
        $this->setFieldGroup($form_display, $field_name, $schema_type, $schema_property, $field_defaults);
        $form_display->save();
      }

      // View display.
      $view_modes = $this->schemaEntityDisplayBuilder->getViewModes($entity_type_id, $bundle);
      foreach ($view_modes as $view_mode) {
        $view_display = $this->entityDisplayRepository->getViewDisplay($entity_type_id, $bundle, $view_mode);
        $this->setFieldGroup($view_display, $field_name, $schema_type, $schema_property, $field_defaults);
        $view_display->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function entityDisplayPreSave(EntityDisplayInterface $display): void {
    if ($display->isSyncing()) {
      return;
    }

    $field = $display->schemaDotOrgField ?? NULL;
    if (!$field) {
      return;
    }

    $field_name = $field['field_name'];
    $schema_type = $field['schema_type'];
    $schema_property = $field['schema_property'];

    $modes = $this->schemaEntityDisplayBuilder->getModes($display);
    // Only support field groups in the default and full view modes.
    if ($display instanceof EntityViewDisplayInterface) {
      $modes = array_intersect_key($modes, ['default' => 'default', 'full' => 'full']);
    }
    if (isset($modes[$display->getMode()])) {
      $this->setFieldGroup($display, $field_name, $schema_type, $schema_property, $field);
    }
  }

  /**
   * Set entity display field groups for a Schema.org property.
   *
   * @param \Drupal\Core\Entity\Display\EntityDisplayInterface $display
   *   The entity display.
   * @param string $field_name
   *   The field name to be set.
   * @param string $schema_type
   *   The field name's associated Schema.org type.
   * @param string $schema_property
   *   The field name's associated Schema.org property.
   * @param array $field_defaults
   *   The field defaults.
   *
   * @see field_group_group_save()
   * @see field_group_field_overview_submit()
   * @see \Drupal\field_group\Form\FieldGroupAddForm::submitForm
   */
  protected function setFieldGroup(EntityDisplayInterface $display, string $field_name, string $schema_type, string $schema_property, array $field_defaults): void {
    if (!$this->hasFieldGroup($display, $field_name, $schema_type, $schema_property)) {
      return;
    }

    $entity_type_id = $display->getTargetEntityTypeId();
    $bundle = $display->getTargetBundle();
    $display_type = ($display instanceof EntityFormDisplayInterface) ? 'form' : 'view';
    $field_group = $this->getFieldGroup(
      $entity_type_id,
      $field_name,
      $schema_type,
      $schema_property,
      $field_defaults
    );
    if (!$field_group) {
      return;
    }

    $field_weight = $this->getFieldWeight(
      $entity_type_id,
      $bundle,
      $field_name,
      $schema_type,
      $schema_property,
      $field_defaults
    );

    // Prefix group name.
    $group_name = FieldGroupAddForm::GROUP_PREFIX . $field_group['name'];
    $group_label = $field_group['label'];
    $group_weight = $field_group['weight'];
    $group_description = $field_group['description'] ?? '';

    // Remove field name from an existing groups, so that it can be reset.
    $existing_groups = $display->getThirdPartySettings('field_group');
    foreach ($existing_groups as $existing_group_name => $existing_group) {
      $index = array_search($field_name, $existing_group['children']);
      if ($index !== FALSE) {
        array_splice($existing_group['children'], $index, 1);
        $display->setThirdPartySetting('field_group', $existing_group_name, $existing_group);
      }
    }

    // Get existing group.
    $group = $display->getThirdPartySetting('field_group', $group_name);
    if (!$group) {
      $default_format_type = $this->configFactory
        ->get('schemadotorg_field_group.settings')
        ->get('default_' . $display_type . '_type') ?: '';
      $default_format_settings = ($default_format_type === 'details') ? ['open' => TRUE] : [];
      if ($display instanceof EntityFormDisplayInterface) {
        $default_format_settings['description'] = $group_description;
      }
      $group = [
        'label' => $group_label,
        'children' => [],
        'parent_name' => '',
        'weight' => $group_weight,
        'format_type' => $default_format_type,
        'format_settings' => $default_format_settings,
        'region' => 'content',
      ];
    }

    // Append the field to the children.
    $group['children'][] = $field_name;
    $group['children'] = array_unique($group['children']);

    // Set field group in the entity display.
    $display->setThirdPartySetting('field_group', $group_name, $group);

    // Set field component's weight.
    $component = $display->getComponent($field_name);
    $component['weight'] = $field_weight;
    $display->setComponent($field_name, $component);
  }

  /**
   * Get the field group for a given entity type, field name, schema type, schema property, and mapping values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $field_name
   *   The field name.
   * @param string $schema_type
   *   The schema type.
   * @param string $schema_property
   *   The schema property.
   * @param array $field_defaults
   *   The field defaults.
   *
   * @return array
   *   An array containing the field group name, label, and weight.
   */
  protected function getFieldGroup(string $entity_type_id, string $field_name, string $schema_type, string $schema_property, array $field_defaults): array {
    // Automatically generate a default catch all field group for
    // the current Schema.org type.
    $group_name = $this->getFieldGroupName($entity_type_id, $field_name, $schema_type, $schema_property, $field_defaults);
    if ($group_name === FALSE) {
      return [];
    }
    elseif (is_null($group_name)) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
      $mapping_type = $this->entityTypeManager
        ->getStorage('schemadotorg_mapping_type')
        ->load($entity_type_id);
      // But don't generate a group for default fields.
      $base_field_names = $mapping_type->getBaseFieldNames();
      if (isset($base_field_names[$field_name])) {
        return [];
      }

      return [
        'name' => $this->schemaNames->schemaIdToDrupalName('types', $schema_type),
        'label' => $this->schemaNames->camelCaseToSentenceCase($schema_type),
        'description ' => '',
        'weight' => 0,
      ];
    }
    else {
      $default_field_groups = $this->configFactory
        ->get('schemadotorg_field_group.settings')
        ->get('default_field_groups.' . $entity_type_id) ?? [];
      return [
        'name' => $group_name,
        'label' => $default_field_groups[$group_name]['label'] ?? $group_name,
        'description' => $default_field_groups[$group_name]['description'] ?? '',
        'weight' => $default_field_groups[$group_name]['weight'] ?? 0,
      ];
    }
  }

  /**
   * Get the field group name for a given entity, field, schema type, schema property, and mapping values.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $field_name
   *   The field name.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param array $field_defaults
   *   The field defaults.
   *
   * @return string|bool|null
   *   The field group name, FALSE for no group, or null if not found.
   */
  protected function getFieldGroupName(string $entity_type_id, string $field_name, string $schema_type, string $schema_property, array $field_defaults): string|bool|null {
    // Get group name from the field's defaults.
    if (array_key_exists('group', $field_defaults)
      && !is_null($field_defaults['group'])) {
      return $field_defaults['group'];
    }

    // Get group name and field weight from entity type
    // field group configuration.
    $default_field_groups = $this->configFactory
      ->get('schemadotorg_field_group.settings')
      ->get('default_field_groups.' . $entity_type_id) ?? [];
    foreach ($default_field_groups as $default_field_group_name => $default_field_group) {
      $properties = array_flip($default_field_group['properties']);
      $parts = [
        'field_name' => $field_name,
        'schema_type' => $schema_type,
        'schema_property' => explode(':', $schema_property)[0],
      ];
      $field_group_setting = $this->schemaTypeManager->getSetting($properties, $parts);
      if (!is_null($field_group_setting)) {
        return $default_field_group_name;
      }
    }

    // Set group name for sub properties of identifier.
    if (isset($default_field_groups['identifiers'])
      && $this->schemaTypeManager->isSubPropertyOf($schema_property, 'identifier')
    ) {
      return 'identifiers';
    }

    // Set group name by field type.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load("$entity_type_id.$field_name");
    if ($field_storage) {
      $field_type = $field_storage->getType();
      $field_target_type = $field_storage->getSetting('target_type');

      if ($field_type === 'link' && isset($default_field_groups['links'])) {
        return 'links';
      }
      elseif ($field_type === 'entity_reference') {
        if ($field_target_type === 'taxonomy_term' && isset($default_field_groups['taxonomy'])) {
          return 'taxonomy';
        }
        elseif (isset($default_field_groups['relationships'])) {
          return 'relationships';
        }
      }
    }

    // Set group name by the parent Schema.org type.
    $default_schema_type_field_groups = $this->configFactory
      ->get('schemadotorg_field_group.settings')
      ->get('default_schema_type_field_groups');
    foreach ($default_schema_type_field_groups as $default_schema_type => $default_field_group_name) {
      if (isset($default_field_groups[$default_field_group_name])
        && $this->schemaTypeManager->isSubTypeOf($schema_type, $default_schema_type)) {
        return $default_field_group_name;
      }
    }

    return NULL;
  }

  /**
   * Retrieves the weight of a field in the field group.
   *
   * @param string $entity_type_id
   *   The ID of the entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The name of the field.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param array $field_defaults
   *   The field defaults.
   *
   * @return int
   *   The weight of the field in the field group.
   */
  protected function getFieldWeight(string $entity_type_id, string $bundle, string $field_name, string $schema_type, string $schema_property, array $field_defaults): int {
    $field_weight = $field_defaults['group_field_weight'] ?? NULL;
    if ($field_weight) {
      return $field_weight;
    }

    $default_field_groups = $this->configFactory
      ->get('schemadotorg_field_group.settings')
      ->get('default_field_groups.' . $entity_type_id) ?? [];
    foreach ($default_field_groups as $default_field_group) {
      $properties = array_flip($default_field_group['properties']);
      $parts = [
        'field_name' => $field_name,
        'schema_type' => $schema_type,
        // Get the main Schema.org property.
        // (i.e., 'name' is the main property for 'name:prefix'.)
        'schema_property' => explode(':', $schema_property)[0],
      ];
      $weight = $this->schemaTypeManager->getSetting($properties, $parts);
      if (!is_null($weight)) {
        return $weight;
      }
    }

    return $this->schemaEntityDisplayBuilder->getSchemaPropertyDefaultFieldWeight($entity_type_id, $bundle, $field_name, $schema_type, $schema_property);
  }

  /**
   * Determine if the Schema.org property/field name has field group.
   *
   * @param \Drupal\Core\Entity\Display\EntityDisplayInterface $display
   *   The entity display.
   * @param string $field_name
   *   The field name to be set.
   * @param string $schema_type
   *   The field name's associated Schema.org type.
   * @param string $schema_property
   *   The field name's associated Schema.org property.
   *
   * @return bool
   *   TRUE if the Schema.org property/field name has field group
   */
  protected function hasFieldGroup(EntityDisplayInterface $display, string $field_name, string $schema_type, string $schema_property): bool {
    if (!$display->getComponent($field_name)) {
      return FALSE;
    }

    $disable_field_groups = $this->configFactory
      ->get('schemadotorg_field_group.settings')
      ->get('disable_field_groups');
    if (empty($disable_field_groups)) {
      return TRUE;
    }

    $parts = [
      'entity_type_id' => $display->getTargetEntityTypeId(),
      'bundle' => $display->getTargetBundle(),
      'schema_type' => $schema_type,
      'schema_property' => explode(':', $schema_property)[0],
      'field_name' => $field_name,
      'display_type' => ($display instanceof EntityFormDisplayInterface) ? 'form' : 'view',
      'display_mode' => $display->getMode(),
    ];

    $patterns = [
      ['entity_type_id'],
      ['entity_type_id', 'display_type'],
      ['entity_type_id', 'display_type', 'bundle'],
      ['entity_type_id', 'display_type', 'bundle', 'field_name'],
      ['entity_type_id', 'display_type', 'schema_type'],
      ['entity_type_id', 'display_type', 'schema_type', 'schema_property'],
      ['entity_type_id', 'display_type', 'schema_property'],
      ['entity_type_id', 'display_type', 'field_name'],
      ['entity_type_id', 'display_type', 'display_mode'],
      ['entity_type_id', 'display_type', 'display_mode', 'bundle'],
      ['entity_type_id', 'display_type', 'display_mode', 'bundle', 'field_name'],
      ['entity_type_id', 'display_type', 'display_mode', 'schema_type'],
      ['entity_type_id', 'display_type', 'display_mode', 'schema_type', 'schema_property'],
      ['entity_type_id', 'display_type', 'display_mode', 'schema_property'],
      ['entity_type_id', 'bundle'],
      ['entity_type_id', 'bundle', 'field_name'],
      ['entity_type_id', 'schema_type'],
      ['entity_type_id', 'schema_type', 'schema_property'],
      ['entity_type_id', 'schema_property'],
      ['entity_type_id', 'field_name'],
    ];

    return !$this->schemaTypeManager->getSetting(
      settings: $disable_field_groups,
      parts: $parts,
      patterns: $patterns,
    );
  }

}
