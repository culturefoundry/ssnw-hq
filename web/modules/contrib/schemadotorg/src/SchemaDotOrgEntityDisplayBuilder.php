<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Schema.org entity display builder service.
 *
 * The Schema.org entity display builder service sets a Schema.org property's
 * field's entity display component settings ana weight.
 */
class SchemaDotOrgEntityDisplayBuilder implements SchemaDotOrgEntityDisplayBuilderInterface {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgEntityDisplayBuilder object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected SchemaDotOrgNamesInterface $schemaNames,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getDefaultFieldWeights(): array {
    $weights = $this->configFactory
      ->get('schemadotorg.settings')
      ->get('schema_properties.default_field_weights');
    $weights = array_flip($weights);
    // Start field weights at 1 since most default fields are set to 0.
    array_walk(
      $weights,
      fn (&$weight) => ($weight += 1)
    );
    return $weights;
  }

  /**
   * Get the default field weight for Schema.org property.
   *
   * @param string $entity_type_id
   *   The Schema.org property.
   * @param string $field_name
   *   The entity type.
   * @param string $schema_property
   *   The field name.
   *
   * @return int
   *   The default field weight for Schema.org property.
   */
  public function getSchemaPropertyDefaultFieldWeight(string $entity_type_id, string $field_name, string $schema_property): int {
    // Check default field weights.
    $default_field_weights = $this->getDefaultFieldWeights();
    if (isset($default_field_weights[$schema_property])) {
      return $default_field_weights[$schema_property];
    }

    // Determine max field weight rounded up to 10.
    $max_field_weight = ($default_field_weights)
      ? (int) ceil(max($default_field_weights) / 10) * 10
      : 0;

    // Get the field storage entity.
    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage */
    $field_storage = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load("$entity_type_id.$field_name");
    if (!$field_storage) {
      return $max_field_weight;
    }

    // Determine the field weight by the field type.
    $field_type_weights = [
      // Text fields.
      'string' => 10 + $max_field_weight,
      'integer' => 10 + $max_field_weight,
      'float'  => 10 + $max_field_weight,
      'decimal' => 10 + $max_field_weight,
      'datetime' => 11 + $max_field_weight,
      'duration' => 12 + $max_field_weight,
      // Text areas.
      'string_long' => 20 + $max_field_weight,
      'text_long' => 20 + $max_field_weight,
      'text_with_summary' => 20 + $max_field_weight,
      // Options.
      'list_string' => 30 + $max_field_weight,
      'list_integer' => 30 + $max_field_weight,
      'list_float' => 30 + $max_field_weight,
      'list_decimal' => 30 + $max_field_weight,
      'boolean' => 31 + $max_field_weight,
      // File and Links.
      'file' => 40 + $max_field_weight,
      'link' => 41 + $max_field_weight,
      'custom_field' => 42 + $max_field_weight,
      // Custom (50 - 55).
      // Entity references.
      'entity_reference' => 60 + $max_field_weight,
      'entity_reference_revisions' => 61 + $max_field_weight,
      'entity_reference_entity_modify' => 62 + $max_field_weight,
    ];
    $field_weight = $field_type_weights[$field_storage->getType()]
      ?? 50 + $max_field_weight;

    // Add 5 to weight for multiple value fields..
    if ($field_storage->getCardinality() !== 1) {
      $field_weight += 5;
    }

    return $field_weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setFieldDisplays(
    string $schema_type,
    string $schema_property,
    array $field_storage_values,
    array $field_values,
    ?string $widget_id,
    array $widget_settings,
    ?string $formatter_id,
    array $formatter_settings,
  ): void {
    $entity_type_id = $field_values['entity_type'];
    $bundle = $field_values['bundle'];
    $field_name = $field_values['field_name'];

    $mapping_type = $this->loadMappingType($entity_type_id);

    // Form display.
    if ($widget_id !== static::COMPONENT_HIDDEN) {
      $form_modes = $this->getFormModes($entity_type_id, $bundle);
      foreach ($form_modes as $form_mode) {
        $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, $form_mode);
        $this->setComponent($form_display, $field_name, $widget_id, $widget_settings);
        $form_display->schemaDotOrgType = $schema_type;
        $form_display->schemaDotOrgProperty = $schema_property;
        $form_display->save();
      }
    }

    // View display.
    if ($formatter_id !== static::COMPONENT_HIDDEN) {
      $view_modes = $this->getViewModes($entity_type_id, $bundle);
      foreach ($view_modes as $view_mode) {
        $view_display = $this->entityDisplayRepository->getViewDisplay($entity_type_id, $bundle, $view_mode);

        $display_properties = $mapping_type->getSchemaTypeViewDisplayProperties($schema_type, $view_mode);
        if ($display_properties) {
          if (!isset($display_properties[$schema_property])) {
            continue;
          }

          // Alter text with summary field to show trimmed summary.
          $type = $field_storage_values['type'] ?? NULL;
          if ($type === 'text_with_summary') {
            $formatter_id = 'text_summary_or_trimmed';
            $formatter_settings = ['label' => 'hidden'];
          }
        }

        $this->setComponent($view_display, $field_name, $formatter_id, $formatter_settings);
        $view_display->schemaDotOrgType = $schema_type;
        $view_display->schemaDotOrgProperty = $schema_property;
        $view_display->save();
      }
    }
  }

  /**
   * Set entity display component.
   *
   * @param \Drupal\Core\Entity\Display\EntityDisplayInterface $display
   *   The entity display.
   * @param string $field_name
   *   The field name to be set.
   * @param string|null $type
   *   The component's plugin id.
   * @param array $settings
   *   The component's plugin settings.
   */
  protected function setComponent(EntityDisplayInterface $display, string $field_name, ?string $type, array $settings): void {
    $options = [];

    // Set custom component type.
    if ($type) {
      $options['type'] = $type;
    }

    // Converted some $settings to $options.
    if (!empty($settings)) {
      if ($display instanceof EntityViewDisplayInterface) {
        $option_names = ['label', 'third_party_settings'];
      }
      else {
        $option_names = ['third_party_settings'];
      }
      foreach ($option_names as $option_name) {
        if (isset($settings[$option_name])) {
          $options[$option_name] = $settings[$option_name];
          unset($settings[$option_name]);
        }
      }
      $options['settings'] = $settings;
    }

    $display->setComponent($field_name, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function setFieldWeights(SchemaDotOrgMappingInterface $mapping, array $properties = []): void {
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $properties = $properties ?: $mapping->getNewSchemaProperties();

    // Form display.
    $form_modes = $this->getFormModes($entity_type_id, $bundle);
    foreach ($form_modes as $form_mode) {
      $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, $form_mode);
      foreach ($properties as $field_name => $property) {
        $this->setFieldWeight($form_display, $field_name, $property);
      }
      $form_display->save();
    }

    // View display.
    $view_modes = $this->getViewModes($entity_type_id, $bundle);
    foreach ($view_modes as $view_mode) {
      $view_display = $this->entityDisplayRepository->getViewDisplay($entity_type_id, $bundle, $view_mode);
      foreach ($properties as $field_name => $property) {
        $this->setFieldWeight($view_display, $field_name, $property);
      }
      $view_display->save();
    }
  }

  /**
   * Set entity display field weight for a Schema.org property.
   *
   * @param \Drupal\Core\Entity\Display\EntityDisplayInterface $display
   *   The entity display.
   * @param string $field_name
   *   The field name to be set.
   * @param string $schema_property
   *   The field name's associated Schema.org property.
   */
  protected function setFieldWeight(EntityDisplayInterface $display, string $field_name, string $schema_property): void {
    // Make sure the field component exists.
    if (!$display->getComponent($field_name)) {
      return;
    }

    $entity_type_id = $display->getTargetEntityTypeId();
    $field_weight = $this->getSchemaPropertyDefaultFieldWeight($entity_type_id, $field_name, $schema_property);

    $component = $display->getComponent($field_name);
    $component['weight'] = $field_weight;
    $display->setComponent($field_name, $component);
  }

  /**
   * {@inheritdoc}
   */
  public function setComponentWeights(SchemaDotOrgMappingInterface $mapping): void {
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();

    $mapping_type = $this->loadMappingType($entity_type_id);
    if (!$mapping_type) {
      return;
    }

    $component_weights = $mapping_type->getDefaultComponentWeights();
    if (empty($component_weights)) {
      return;
    }

    $this->setDisplayComponentWeights($entity_type_id, $bundle, 'form', $component_weights);
    $this->setDisplayComponentWeights($entity_type_id, $bundle, 'view', $component_weights);
  }

  /**
   * Set the display component weights.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle
   *   The entity bundle.
   * @param string $display_type
   *   The entity display type.
   * @param array $component_weights
   *   The entity display component weights.
   */
  protected function setDisplayComponentWeights(string $entity_type_id, string $bundle, string $display_type, array $component_weights): void {
    $display_type = ucfirst($display_type);
    $modes_method = "get{$display_type}Modes";
    $display_method = "get{$display_type}Display";

    $modes = $this->$modes_method($entity_type_id, $bundle);
    foreach ($modes as $mode) {
      $display = $this->entityDisplayRepository->$display_method($entity_type_id, $bundle, $mode);
      $is_updated = FALSE;
      foreach ($component_weights as $component_name => $component_weight) {
        $component = $display->getComponent($component_name);
        if ($component && isset($component['region'])) {
          $component['weight'] = $component_weight;
          $display->setComponent($component_name, $component);
          $is_updated = TRUE;
        }
      }
      if ($is_updated) {
        $display->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormModes(string $entity_type_id, string $bundle): array {
    return $this->getModes(
      $entity_type_id,
      $bundle,
      'Form',
      []
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getViewModes(string $entity_type_id, string $bundle): array {
    $default_view_modes = ['teaser', 'content_browser'];
    return $this->getModes(
      $entity_type_id,
      $bundle,
      'View',
      $default_view_modes
    );
  }

  /**
   * Get display modes for a specific entity type.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle
   *   The bundle.
   * @param string $type
   *   The display modes.
   * @param array $default_modes
   *   An array of default display modes.
   *
   * @return array
   *   An array of display modes.
   */
  protected function getModes(string $entity_type_id, string $bundle, string $type = 'View', array $default_modes = []): array {
    $mode_method = "get{$type}ModeOptionsByBundle";
    $mode_options = $this->entityDisplayRepository->$mode_method($entity_type_id, $bundle);

    if ($default_modes) {
      $modes = array_intersect_key(
        array_combine($default_modes, $default_modes),
        $mode_options
      );
    }
    else {
      $mode_keys = array_keys($mode_options);
      $modes = array_combine($mode_keys, $mode_keys);
    }

    return ['default' => 'default'] + $modes;
  }

}
