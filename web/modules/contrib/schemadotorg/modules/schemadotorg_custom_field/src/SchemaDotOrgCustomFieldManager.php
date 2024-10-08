<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_custom_field;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\custom_field\Plugin\CustomFieldTypeManager;
use Drupal\custom_field\Plugin\CustomFieldWidgetManager;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org Custom Field manager.
 */
class SchemaDotOrgCustomFieldManager implements SchemaDotOrgCustomFieldManagerInterface {

  /**
   * Cache of Schema.org properties that support custom fields.
   *
   * @var array
   */
  protected $customFieldProperties;

  /**
   * Constructs a SchemaDotOrgCustomFieldManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface $schemaEntityFieldManager
   *   The Schema.org entity field manager.
   * @param \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface $selectionPluginManager
   *   The entity reference selection manager.
   * @param \Drupal\custom_field\Plugin\CustomFieldTypeManager $customFieldTypeManager
   *   The custom field type manager.
   * @param \Drupal\custom_field\Plugin\CustomFieldWidgetManager $customFieldWidgetManager
   *   The custom field type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgEntityFieldManagerInterface $schemaEntityFieldManager,
    protected SelectionPluginManagerInterface $selectionPluginManager,
    protected CustomFieldTypeManager $customFieldTypeManager,
    protected CustomFieldWidgetManager $customFieldWidgetManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void {
    $schema_properties = array_intersect_key($this->getCustomFieldSchemaProperties(), $defaults['properties']);
    foreach ($schema_properties as $schema_property) {
      $has_default_properties = $this->hasDefaultProperties(
        entity_type_id: $entity_type_id,
        bundle: $bundle,
        schema_type: $schema_type,
        schema_property: $schema_property,
      );
      if ($has_default_properties) {
        $defaults['properties'][$schema_property]['type'] = 'custom';
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function propertyFieldTypeAlter(array &$field_types, string $schema_type, string $schema_property): void {
    $has_default_properties = $this->hasDefaultProperties(
      schema_type: $schema_type,
      schema_property: $schema_property,
    );
    if ($has_default_properties) {
      $field_types = ['custom' => 'custom'] + $field_types;
    }
  }

  /**
   * Prepare a property's field data before the Schema.org mapping form.
   *
   * @param array &$default_field
   *   The default values used in the Schema.org mapping form.
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   */
  public function propertyFieldPrepare(array &$default_field, string $schema_type, string $schema_property): void {
    // Make sure the main entity field has a unique name by prefixing it with
    // the bundle name.
    $has_default_properties = $this->hasDefaultProperties(
      schema_type: $schema_type,
      schema_property: $schema_property,
    );
    if ($has_default_properties && $schema_property === 'mainEntity') {
      $default_type = $this->configFactory
        ->get('schemadotorg.settings')
        ->get("schema_types.default_types.$schema_type") ?? [];
      $type_definition = $this->schemaTypeManager->getType($schema_type);

      $type_name = $default_type['name'] ?? $type_definition['drupal_name'];
      $field_name = $default_field['name'];
      $default_field['name'] = $type_name . '_' . $field_name;
    }
  }

  /**
   * Get an array of Schema.org properties that support custom fields.
   *
   * @return array
   *   An array of Schema.org properties that support custom fields.
   */
  protected function getCustomFieldSchemaProperties(): array {
    if ($this->customFieldProperties) {
      return $this->customFieldProperties;
    }

    $this->customFieldProperties = [];
    $default_schema_properties = $this->configFactory
      ->get('schemadotorg_custom_field.settings')
      ->get('default_schema_properties');
    foreach (array_keys($default_schema_properties) as $schema_property) {
      if (str_contains($schema_property, '--')) {
        $parts = explode('--', $schema_property);
        $schema_property = end($parts);
      }
      $this->customFieldProperties[$schema_property] = $schema_property;
    }
    return $this->customFieldProperties;
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
    // Make sure the field type is set to 'custom' (field).
    if ($field_storage_values['type'] !== 'custom') {
      return;
    }

    // Check to see if the property has custom field settings.
    $default_schema_properties = $this->getDefaultProperties(
      entity_type_id: $field_values['entity_type'],
      bundle: $field_values['bundle'],
      schema_type: $schema_type,
      schema_property: $schema_property,
    );
    if (!$default_schema_properties) {
      return;
    }

    $custom_field_schema_type = $default_schema_properties['schema_type'] ?? '';
    $custom_field_schema_properties = $default_schema_properties['schema_properties'] ?? [];

    $weight = 0;

    $entity_type_id = $field_values['entity_type'];
    $field_storage_columns = [];
    $field_settings = [];

    $formatter_id = 'custom_formatter';
    $formatter_settings = [];

    foreach ($custom_field_schema_properties as $schema_property => $settings) {
      $data_type = $settings['data_type'] ?? 'string';
      /** @var \Drupal\custom_field\Plugin\CustomFieldTypeInterface $field_type */
      $field_type = $this->customFieldTypeManager->createInstance($data_type);

      if ($this->schemaTypeManager->isProperty($schema_property)) {
        $default_field = $this->schemaEntityFieldManager->getPropertyDefaultField($entity_type_id, $custom_field_schema_type, $schema_property);
        $name = $default_field['name'];
        $label = $default_field['label'];
        $description = $default_field['description'];
      }
      else {
        $name = $settings['name'] ?? $this->schemaNames->camelCaseToSnakeCase($schema_property);
        $label = $this->schemaNames->camelCaseToSentenceCase($schema_property);
        $description = '';
      }

      $widget_type = $settings['widget_type'] ?? $field_type->getDefaultWidget();
      $default_widget_settings = $this->getDefaultWidgetSettings($widget_type, $schema_type, $schema_property, $settings);

      $field_storage_columns[$name] = [
        'name' => $name,
        'type' => $data_type,
        // Set default storage column values.
        'max_length' => '255',
        'unsigned' => 0,
        'precision' => '10',
        'scale' => '2',
        'datetime_type' => 'datetime',
      ];

      $field_settings[$name] = [
        'type' => $widget_type,
        'widget_settings' => [
          'label' => $label,
          'settings' => [
            'description' => $description,
          ] + $default_widget_settings['settings'],
        ],
        'check_empty' => FALSE,
        'weight' => $weight,
      ];

      // Unset custom allowed values which are handled via widget settings.
      // @see \Drupal\schemadotorg_custom_field\SchemaDotOrgCustomFieldManager::getDefaultWidgetSettings
      unset($settings['allowed_values']);

      // Apply custom settings for field storage.
      // @todo Determine what other columns settings are not required.
      $additional_column_settings = [
        'target_type' => NULL,
      ];
      $field_storage_columns[$name] = array_intersect_key($settings, $field_storage_columns[$name] + $additional_column_settings)
        + $field_storage_columns[$name];

      // Apply custom settings for widget settings.
      $field_settings[$name]['widget_settings'] = array_intersect_key($settings, $field_settings[$name]['widget_settings'])
        + $field_settings[$name]['widget_settings'];
      $field_settings[$name]['widget_settings']['settings'] = array_intersect_key($settings, $field_settings[$name]['widget_settings']['settings'])
        + $field_settings[$name]['widget_settings']['settings'];

      // Display prefix/suffix.
      if (!empty($field_settings[$name]['widget_settings']['settings']['prefix'])
        || !empty($field_settings[$name]['widget_settings']['settings']['suffix'])) {
        $formatter_settings['fields'][$name]['format_type'] = $field_type->getDefaultFormatter();
        $formatter_settings['fields'][$name]['formatter_settings']['prefix_suffix'] = TRUE;
      }

      $weight++;
    }

    $field_storage_values['settings']['columns'] = $field_storage_columns;

    $field_values['settings'] = [
      'field_settings' => $field_settings,
      'field_type' => 'custom',
    ];

    $widget_id = 'custom_stacked';
    $widget_settings = ['wrapper' => 'fieldset'];
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefaultProperties(?string $entity_type_id = NULL, ?string $bundle = NULL, ?string $schema_type = NULL, ?string $schema_property = NULL): bool {
    return (bool) $this->getDefaultProperties(
      entity_type_id: $entity_type_id,
      bundle: $bundle,
      schema_type: $schema_type,
      schema_property: $schema_property,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultProperties(?string $entity_type_id = NULL, ?string $bundle = NULL, ?string $schema_type = NULL, ?string $schema_property = NULL): ?array {
    $default_schema_properties = $this->configFactory->get('schemadotorg_custom_field.settings')
      ->get('default_schema_properties') ?? [];
    $parts = [
      'entity_type_id' => $entity_type_id,
      'bundle' => $bundle,
      'schema_type' => $schema_type,
      'schema_property' => $schema_property,
    ];
    return $this->schemaTypeManager->getSetting($default_schema_properties, $parts, ['parents' => FALSE]);
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldItemSchemaMapping(FieldItemListInterface|FieldItemInterface $item): ?SchemaDotOrgMappingInterface {
    $field_type = $item->getFieldDefinition()->getType();
    return ($field_type === 'custom')
      ? SchemaDotOrgMapping::loadByEntity($item->getEntity())
      : NULL;
  }

  /**
   * Get custom field default widget settings for a custom field widget type.
   *
   * @param string $widget_type
   *   A custom field widget type.
   * @param string $schema_type
   *   A Schema.org type.
   * @param string $schema_property
   *   A Schema.org property.
   * @param array $settings
   *   Custom settings.
   *
   * @return array
   *   An associate array of custom field default widget settings.
   */
  protected function getDefaultWidgetSettings(string &$widget_type, string $schema_type, string $schema_property, array $settings): array {
    // Check for allowed values, if there are allowed values then switch
    // the widget type to a 'select' widget.
    $allowed_values = $settings['allowed_values']
      ?? $this->getAllowedValues($schema_type, $schema_property)
      ?? [];
    if ($allowed_values) {
      $widget_type = 'select';
    }

    /** @var \Drupal\custom_field\Plugin\CustomFieldWidgetInterface $custom_field_widget */
    $custom_field_widget = $this->customFieldWidgetManager->createInstance($widget_type);
    $default_widget_settings = $custom_field_widget::defaultSettings();

    switch ($widget_type) {
      case 'entity_reference_autocomplete':
        $default_widget_settings['settings']['handler'] = $this
          ->selectionPluginManager
          ->getPluginId($settings['target_type'], 'default');
        break;

      case 'decimal':
      case 'float':
        $default_widget_settings['settings']['scale'] = 2;
        break;

      case 'select':
        // Convert key/value pairs to a nested array of key/values.
        // (i.e, ['key' => 'value'] => [[key => key', value => 'value']).
        array_walk(
          $allowed_values,
          fn(&$value, $key) => $value = ['value' => $value, 'key' => $key]
        );
        $allowed_values = array_values($allowed_values);
        $default_widget_settings['settings']['allowed_values'] = $allowed_values;
        break;

      case 'text':
        $default_widget_settings['settings']['maxlength'] = 255;
        break;

      case 'textarea':
        $default_format = $this->configFactory
          ->get('schemadotorg_custom_field.settings')
          ->get('default_format');
        if ($default_format) {
          $default_widget_settings['settings']['formatted'] = TRUE;
          $default_widget_settings['settings']['default_format'] = $default_format;
          $default_widget_settings['settings']['format'] = [
            'guidelines' => FALSE,
            'help' => FALSE,
          ];
        }
        break;
    }

    $default_widget_settings['settings'] += [
      'description_display' => 'after',
      'required' => FALSE,
    ];

    // @todo Apply custom default property settings.
    return $default_widget_settings;
  }

  /**
   * Get allowed values for Schema.org property.
   *
   * @param string $schema_type
   *   A Schema.org type.
   * @param string $schema_property
   *   A Schema.org property.
   *
   * @return array|null
   *   Allowed values for Schema.org property.
   *
   * @see schemadotorg_options_schemadotorg_property_field_type_alter()
   */
  protected function getAllowedValues(string $schema_type, string $schema_property): ?array {
    $schema_property_allowed_values = $this->configFactory
      ->get('schemadotorg_options.settings')
      ->get('schema_property_allowed_values') ?? [];
    $parts = [
      'schema_type' => $schema_type,
      'schema_property' => $schema_property,
    ];
    return $this->schemaTypeManager
      ->getSetting($schema_property_allowed_values, $parts);
  }

}
