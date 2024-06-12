<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_field_group;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
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
  public function setFieldGroups(SchemaDotOrgMappingInterface $mapping, array $properties = []): void {
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $schema_type = $mapping->getSchemaType();
    $properties = $properties ?: $mapping->getNewSchemaProperties();
    $mapping_values = $mapping->getMappingDefaults();

    // Form display.
    $form_modes = $this->schemaEntityDisplayBuilder->getFormModes($entity_type_id, $bundle);
    foreach ($form_modes as $form_mode) {
      $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, $form_mode);
      foreach ($properties as $field_name => $property) {
        $this->setFieldGroup($form_display, $field_name, $schema_type, $property, $mapping_values);
      }
      $form_display->save();
    }

    // View display.
    $view_modes = $this->schemaEntityDisplayBuilder->getViewModes($entity_type_id, $bundle);
    // Only support field groups in the default and full view modes.
    $view_modes = array_intersect_key($view_modes, ['default' => 'default', 'full' => 'full']);
    foreach ($view_modes as $view_mode) {
      $view_display = $this->entityDisplayRepository->getViewDisplay($entity_type_id, $bundle, $view_mode);
      foreach ($properties as $field_name => $property) {
        $this->setFieldGroup($view_display, $field_name, $schema_type, $property, $mapping_values);
      }
      $view_display->save();
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
   * @param array $mapping_values
   *   The Schema.org mapping values.
   *
   * @see field_group_group_save()
   * @see field_group_field_overview_submit()
   * @see \Drupal\field_group\Form\FieldGroupAddForm::submitForm
   */
  protected function setFieldGroup(EntityDisplayInterface $display, string $field_name, string $schema_type, string $schema_property, array $mapping_values): void {
    if (!$this->hasFieldGroup($display, $field_name, $schema_type, $schema_property)) {
      return;
    }

    $entity_type_id = $display->getTargetEntityTypeId();
    $display_type = ($display instanceof EntityFormDisplayInterface) ? 'form' : 'view';

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
    $mapping_type = $this->entityTypeManager
      ->getStorage('schemadotorg_mapping_type')
      ->load($entity_type_id);

    $config = $this->configFactory->get('schemadotorg_field_group.settings');
    $default_field_groups = $config->get('default_field_groups.' . $entity_type_id) ?? [];
    $group_weights = array_flip(array_keys($default_field_groups));
    foreach ($group_weights as $group_name => $group_weight) {
      $group_weights[$group_name] = $group_weight - 5;
    }
    $max_group_weight = ($group_weights)
      ? (int) ceil(max($group_weights) / 10) * 10
      : 0;

    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load("$entity_type_id.$field_name");
    $field_type = ($field_storage) ? $field_storage->getType() : NULL;
    $field_target_type = ($field_storage) ? $field_storage->getSetting('target_type') : NULL;

    // Get group name and field weight from entity type
    // field group configuration.
    $group_name = NestedArray::getValue($mapping_values, ['properties', $schema_property, 'group']);
    $field_weight = NestedArray::getValue($mapping_values, ['properties', $schema_property, 'group_field_weight']);
    if (!$group_name) {
      foreach ($default_field_groups as $default_field_group_name => $default_field_group) {
        $properties = array_flip($default_field_group['properties']);
        $setting_parts = [
          'schema_type' => $schema_type,
          'schema_property' => $schema_property,
        ];
        $field_group_weight = $this->schemaTypeManager->getSetting($properties, $setting_parts);
        if (!is_null($field_group_weight)) {
          $group_name = $default_field_group_name;
          $field_weight = $field_group_weight;
          break;
        }
      }
    }

    // Set group name for sub properties of identifier.
    if (!$group_name
      && isset($default_field_groups['identifiers'])
      && (
        $this->schemaTypeManager->isSubPropertyOf($schema_property, 'identifier')
      )
    ) {
      $group_name = 'identifiers';
    }

    // Set group name by field type.
    if (!$group_name && $field_type) {
      // Set links field groups.
      if ($field_storage->getType() === 'link' && isset($default_field_groups['links'])) {
        $group_name = 'links';
      }
      // Set entity reference and taxonomy field groups.
      elseif ($field_type === 'entity_reference') {
        if ($field_target_type === 'taxonomy_term' && isset($default_field_groups['taxonomy'])) {
          $group_name = 'taxonomy';
        }
        elseif (isset($default_field_groups['relationships'])) {
          $group_name = 'relationships';
        }
      }
    }

    // Set group name by the parent Schema.org type.
    if (!$group_name) {
      $default_schema_type_field_groups = $config->get('default_schema_type_field_groups');
      foreach ($default_schema_type_field_groups as $default_schema_type => $default_field_group_name) {
        if (isset($default_field_groups[$default_field_group_name])
          && $this->schemaTypeManager->isSubTypeOf($schema_type, $default_schema_type)) {
          $group_name = $default_field_group_name;
        }
      }
    }

    // Automatically generate a default catch all field group for
    // the current Schema.org type.
    if (!$group_name) {
      // But don't generate a group for default fields.
      $base_field_names = $mapping_type->getBaseFieldNames();
      if (isset($base_field_names[$field_name])) {
        return;
      }
      $group_name = $this->schemaNames->schemaIdToDrupalName('types', $schema_type);
      $group_label = $this->schemaNames->camelCaseToSentenceCase($schema_type);
      $group_weight = $max_group_weight;
    }
    else {
      $group_label = $default_field_groups[$group_name]['label'];
      $default_group_weights = [
        'links' => 10 + $max_group_weight,
        'relationships'  => 20 + $max_group_weight,
        'taxonomy' => 30 + $max_group_weight,
        'identifiers' => 40 + $max_group_weight,
      ];
      $group_weight = $default_group_weights[$group_name]
        ?? $group_weights[$group_name]
        ?? $max_group_weight;
    }

    // Set default field weight.
    $field_weight = $field_weight ?? $this->schemaEntityDisplayBuilder->getSchemaPropertyDefaultFieldWeight($entity_type_id, $field_name, $schema_property);

    // Prefix group name.
    $group_name = FieldGroupAddForm::GROUP_PREFIX . $group_name;

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
      $default_format_type = $config->get('default_' . $display_type . '_type') ?: '';
      $default_format_settings = ($default_format_type === 'details') ? ['open' => TRUE] : [];
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

    $entity_type_id = $display->getTargetEntityTypeId();
    $bundle = $display->getTargetBundle();
    $display_type = ($display instanceof EntityFormDisplayInterface) ? 'form' : 'view';
    $display_mode = $display->getMode();

    $disabled_patterns = [
      $entity_type_id,
      "$entity_type_id--$display_type",
      "$entity_type_id--$display_type--$bundle",
      "$entity_type_id--$display_type--$bundle--$field_name",
      "$entity_type_id--$display_type--$schema_type",
      "$entity_type_id--$display_type--$schema_type--$schema_property",
      "$entity_type_id--$display_type--$schema_property",
      "$entity_type_id--$display_type--$field_name",
      "$entity_type_id--$display_type--$display_mode",
      "$entity_type_id--$display_type--$display_mode--$bundle",
      "$entity_type_id--$display_type--$display_mode--$bundle--$field_name",
      "$entity_type_id--$display_type--$display_mode--$schema_type",
      "$entity_type_id--$display_type--$display_mode--$schema_type--$schema_property",
      "$entity_type_id--$display_type--$display_mode--$schema_property",
      "$entity_type_id--$bundle",
      "$entity_type_id--$bundle--$field_name",
      "$entity_type_id--$schema_type",
      "$entity_type_id--$schema_type--$schema_property",
      "$entity_type_id--$schema_property",
      "$entity_type_id--$field_name",
    ];

    $disabled = (bool) array_intersect($disable_field_groups, $disabled_patterns);
    return !$disabled;
  }

}
