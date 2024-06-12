<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Schema.org entity type builder service.
 *
 * The Schema.org entity type builder service handle the creation of an entity
 * bundle for Schema.org along with adding fields to the entity bundle.
 */
class SchemaDotOrgEntityTypeBuilder implements SchemaDotOrgEntityTypeBuilderInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgEntityTypeBuilder object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $fieldTypePluginManager
   *   The field type plugin manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder
   *   The Schema.org entity display builder.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected MessengerInterface $messenger,
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected FieldTypePluginManagerInterface $fieldTypePluginManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgEntityDisplayBuilderInterface $schemaEntityDisplayBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function addEntityBundle(string $entity_type_id, string $schema_type, array &$values): EntityInterface {
    $entity_values =& $values['entity'];
    $entity_type_definition = $this->entityTypeManager->getDefinition($entity_type_id);

    // Get bundle entity values and map id and label keys.
    // (i.e, A node's label is saved in the database as its title)
    $keys = ['id', 'label'];
    foreach ($keys as $key) {
      $key_name = $entity_type_definition->getKey($key);
      if ($key_name !== $key) {
        $entity_values[$key_name] = $entity_values[$key];
        unset($entity_values[$key]);
      }
    }

    // Alter Schema.org bundle entity values.
    $this->moduleHandler->invokeAll('schemadotorg_bundle_entity_alter', [&$values, $schema_type, $entity_type_id]);

    /** @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage $bundle_entity_storage */
    $bundle_entity_storage = $this->entityTypeManager->getStorage($entity_type_id);
    $bundle_entity = $bundle_entity_storage->create($entity_values);
    // @phpstan-ignore-next-line
    $bundle_entity->schemaDotOrgType = $schema_type;
    // @phpstan-ignore-next-line
    $bundle_entity->schemaDotOrgValues =& $values;
    $bundle_entity->save();

    $bundle_of = $bundle_entity->getEntityType()->getBundleOf();
    $bundle = $bundle_entity->id();

    // Add default 'teaser' and 'content_browser' view modes to node types.
    // @see node_add_body_field()
    // @todo Determine if default view modes should be a 'mapping type' setting.
    if ($bundle_of === 'node') {
      $default_view_modes = ['teaser', 'content_browser'];
      $view_modes = $this->entityDisplayRepository->getViewModes($bundle_of);
      foreach ($default_view_modes as $default_view_mode) {
        if (isset($view_modes[$default_view_mode])) {
          $this->entityDisplayRepository
            ->getViewDisplay($bundle_of, $bundle, $default_view_mode)
            ->save();
        }
      }
    }

    return $bundle_entity;
  }

  /* ************************************************************************ */
  // Field creation methods copied from FieldStorageAddForm.
  // @see \Drupal\field_ui\Form\FieldStorageAddForm
  /* ************************************************************************ */

  /**
   * {@inheritdoc}
   */
  public function addFieldToEntity(string $entity_type_id, string $bundle, array $field): void {
    // Set field defaults.
    $field += [
      // Default field settings.
      // @see \Drupal\schemadotorg_ui\Form\SchemaDotOrgUiMappingForm::buildSchemaPropertyFieldForm
      'machine_name' => NULL,
      'type' => NULL,
      'label' => NULL,
      'description' => '',
      'unlimited' => NULL,
      'required' => NULL,
      // Extra field settings.
      'max_length' => NULL,
      'default_value' => NULL,
      'allowed_values' => [],
      // Schema.org type and property.
      'schema_type' => NULL,
      'schema_property' => NULL,
      // Additional defaults.
      'field_values' => [],
      'field_storage_values' => [],
      'widget_id' => NULL,
      'widget_settings' => [],
      'formatter_id' => NULL,
      'formatter_settings' => [],
    ];

    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load($entity_type_id . '.' . $field['machine_name']);

    // Default field settings.
    $field_name = $field['machine_name'];
    $field_type = ($field_storage_config) ? $field_storage_config->getType() : $field['type'];
    $field_label = $field['label'];
    $field_description = $field['description'];
    $field_unlimited = $field['unlimited'];
    $field_required = $field['required'];
    // Extra field settings.
    $field_max_length = $field['max_length'];
    $field_default_value = $field['default_value'];
    $field_allowed_values = $field['allowed_values'];
    // Schema.org type and property.
    $schema_type = $field['schema_type'];
    $schema_property = $field['schema_property'];

    // Set field storage values.
    $field_storage_values = ($field_storage_config)
      ? $field_storage_config->toArray()
      : [];
    $field_storage_values += [
      'field_name' => $field_name,
      'entity_type' => $entity_type_id,
      'type' => $field_type,
      'cardinality' => $field_unlimited ? -1 : 1,
      'allowed_values' => $field_allowed_values,
      'max_length' => $field_max_length,
    ];
    $field_storage_values = NestedArray::mergeDeep($field['field_storage_values'], $field_storage_values);

    // Set field instance values.
    $field_values = [
      'field_name' => $field_name,
      'entity_type' => $entity_type_id,
      'bundle' => $bundle,
      'label' => $field_label,
      'description' => $field_description,
      'required' => $field_required,
    ];
    if (!is_null($field_default_value)) {
      $field_values['default_value'] = (is_array($field_default_value))
        ? $field_default_value
        : ['value' => $field_default_value];
    }
    $field_values = NestedArray::mergeDeep($field['field_values'], $field_values);

    // Initialize widget and formatter id and settings.
    $widget_id = $field['widget_id'];
    $widget_settings = $field['widget_settings'];
    $formatter_id = $field['formatter_id'];
    $formatter_settings = $field['formatter_settings'];

    // If new field UI field we need to get the preconfigured field.
    // These preconfigured field are typically used for entity references.
    $is_field_ui = str_contains((string) $field_storage_values['type'], 'field_ui:');
    if (!$field_storage_config && $is_field_ui) {
      [, $field_type, $option_key] = explode(':', $field_storage_values['type'], 3);
      $field_storage_values['type'] = $field_type;

      $field_definition = $this->fieldTypePluginManager->getDefinition($field_type);
      $options = $this->fieldTypePluginManager->getPreconfiguredOptions($field_definition['id']);
      $field_options = $options[$option_key];
      // Merge in preconfigured field storage options.
      if (isset($field_options['field_storage_config'])) {
        foreach (['settings'] as $key) {
          if (isset($field_options['field_storage_config'][$key])) {
            $field_storage_values[$key] = $field_options['field_storage_config'][$key];
          }
        }
      }

      // Merge in preconfigured field options.
      if (isset($field_options['field_config'])) {
        foreach (['required', 'settings'] as $key) {
          if (isset($field_options['field_config'][$key])) {
            $field_values[$key] = $field_options['field_config'][$key];
          }
        }
      }

      // Get widget and format id and settings.
      $widget_id = $field_options['entity_form_display']['type'] ?? $widget_id;
      $widget_settings = $field_options['entity_form_display']['settings'] ?? $widget_settings;
      $formatter_id = $field_options['entity_view_display']['type'] ?? $formatter_id;
      $formatter_settings = $field_options['entity_view_display']['settings'] ?? $formatter_settings;
    }

    // Alter field values.
    $this->alterFieldValues(
      $schema_type,
      $schema_property,
      $field_storage_values,
      $field_values,
      $widget_id,
      $widget_settings,
      $formatter_id,
      $formatter_settings
    );

    try {
      // Create new field storage.
      if (!$field_storage_config) {
        $field_storage_config = $this->entityTypeManager
          ->getStorage('field_storage_config')
          ->create($field_storage_values);
        $field_storage_config->schemaDotOrgType = $schema_type;
        $field_storage_config->schemaDotOrgProperty = $schema_property;
        $field_storage_config->save();
      }

      // Create new field instance storage.
      $field = $this->entityTypeManager
        ->getStorage('field_config')
        ->create($field_values);
      $field->schemaDotOrgType = $schema_type;
      $field->schemaDotOrgProperty = $schema_property;
      $field->save();

      // Set new field's form and view displays.
      $this->schemaEntityDisplayBuilder->setFieldDisplays(
        $schema_type,
        $schema_property,
        $field_storage_values,
        $field_values,
        $widget_id,
        $widget_settings,
        $formatter_id,
        $formatter_settings
      );
    }
    catch (\Exception $e) {
      $this->messenger->addError($this->t('There was a problem creating field %label: @message', ['%label' => $field_label, '@message' => $e->getMessage()]));
    }
  }

  /**
   * Alter field storage and field values before they are created.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param array $field_storage_values
   *   Field storage config values.
   * @param array $field_values
   *   Field config values.
   * @param string|null $widget_id
   *   The plugin ID of the widget.
   * @param array $widget_settings
   *   An array of widget settings.
   * @param string|null $formatter_id
   *   The plugin ID of the formatter.
   * @param array $formatter_settings
   *   An array of formatter settings.
   */
  protected function alterFieldValues(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void {
    // Don't copy existing field values for generic Schema.org properties used
    // to manage different types of data.
    if (!$this->schemaTypeManager->isPropertyMainEntity($schema_property)) {
      $this->copyExistingFieldValues(
        $field_values,
        $widget_id,
        $widget_settings,
        $formatter_id,
        $formatter_settings
      );
    }

    $this->setDefaultFieldValues(
      $schema_type,
      $schema_property,
      $field_storage_values,
      $field_values,
      $widget_id,
      $widget_settings,
      $formatter_id,
      $formatter_settings
    );

    $this->moduleHandler->invokeAll('schemadotorg_property_field_alter', [
      $schema_type,
      $schema_property,
      &$field_storage_values,
      &$field_values,
      &$widget_id,
      &$widget_settings,
      &$formatter_id,
      &$formatter_settings,
    ]);
  }

  /**
   * Copy existing field, form, and view settings.
   *
   * Issue #2717319: Provide better default configuration when re-using
   * an existing field.
   * https://www.drupal.org/project/drupal/issues/2717319
   *
   * @param array $field_values
   *   Field config values.
   * @param string|null $widget_id
   *   The plugin ID of the widget.
   * @param array $widget_settings
   *   An array of widget settings.
   * @param string|null $formatter_id
   *   The plugin ID of the formatter.
   * @param array $formatter_settings
   *   An array of formatter settings.
   */
  protected function copyExistingFieldValues(
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void {
    // Get the entity type id and field.
    $entity_type_id = $field_values['entity_type'];
    $field_name = $field_values['field_name'];

    // Look for existing field instance and copy field, form, and view settings.
    /** @var \Drupal\field\FieldConfigStorage $field_config_storage */
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    $existing_field_configs = $field_config_storage->loadByProperties([
      'entity_type' => $entity_type_id,
      'field_name' => $field_name,
    ]);
    if (!$existing_field_configs) {
      return;
    }

    /** @var \Drupal\field\FieldConfigInterface $existing_field_config */
    $existing_field_config = reset($existing_field_configs);
    $existing_bundle = $existing_field_config->getTargetBundle();

    // Set field properties.
    $field_property_names = [
      'required',
      'default_value',
      'default_value_callback',
      'settings',
    ];
    foreach ($field_property_names as $field_property_name) {
      $field_values[$field_property_name] = $existing_field_config->get($field_property_name);
    }
    // Only set the description if a custom one is not set.
    if (empty($field_values['description'])) {
      $field_values['description'] = $existing_field_config->get('description');
    }

    // Set widget id and settings and third_party_settings
    // from the existing form display.
    $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $existing_bundle);
    $existing_form_component = $form_display->getComponent($field_name);
    if ($existing_form_component) {
      $widget_id = $existing_form_component['type'];
      $widget_settings = $existing_form_component['settings'];
      if (!empty($existing_form_component['third_party_settings'])) {
        $widget_settings['third_party_settings'] = $existing_form_component['third_party_settings'];
      }
    }

    // Set formatter id and settings, label, and third_party_settings
    // from the existing view display.
    $view_display = $this->entityDisplayRepository->getViewDisplay($entity_type_id, $existing_bundle);
    $existing_view_component = $view_display->getComponent($field_name);
    if ($existing_view_component) {
      $formatter_id = $existing_view_component['type'];
      $formatter_settings = $existing_view_component['settings'];
      $formatter_settings['label'] = $existing_view_component['label'];
      if (!$existing_view_component['third_party_settings']) {
        $formatter_settings['third_party_settings'] = $existing_view_component['third_party_settings'];
      }
    }
  }

  /**
   * Default default field, form, and view settings.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param array $field_storage_values
   *   Field storage config values.
   * @param array $field_values
   *   Field config values.
   * @param string|null $widget_id
   *   The plugin ID of the widget.
   * @param array $widget_settings
   *   An array of widget settings.
   * @param string|null $formatter_id
   *   The plugin ID of the formatter.
   * @param array $formatter_settings
   *   An array of formatter settings.
   */
  protected function setDefaultFieldValues(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void {

    // Set default formatter settings by schema type and property.
    $default_field_formatter_settings = $this->configFactory
      ->get('schemadotorg.settings')
      ->get('schema_properties.default_field_formatter_settings');
    $formatter_settings += $default_field_formatter_settings["$schema_type--$schema_property"]
      ?? $default_field_formatter_settings[$schema_property]
      ?? [];

    // Set default by field storage type.
    switch ($field_storage_values['type']) {
      case 'boolean':
        $field_values['settings'] = [
          'on_label' => $this->t('Yes'),
          'off_label' => $this->t('No'),
        ];
        break;

      case 'datetime':
        switch ($schema_property) {
          case 'expires':
          case 'dateCreated':
          case 'dateDeleted':
          case 'dateIssued':
          case 'dateModified':
          case 'datePosted':
          case 'datePublished':
          case 'dateVehicleFirstRegistered':
          case 'dissolutionDate':
          case 'paymentDueDate':
          case 'validFrom':
          case 'validThrough':
            $is_date = TRUE;
            break;

          case 'startDate':
          case 'endDate':
            $is_date = (!$this->schemaTypeManager->isSubTypeOf($schema_type, ['Event', 'Schedule']));
            break;

          default:
            $range_includes = $this->schemaTypeManager->getPropertyRangeIncludes($schema_property);
            $is_date = (in_array('Date', $range_includes) && !in_array('DateTime', $range_includes));
            break;
        }
        $field_storage_values['settings']['datetime_type'] = $is_date ? 'date' : 'datetime';
        break;

      case 'entity_reference':
      case 'entity_reference_revisions':
        $target_type = $field_storage_values['settings']['target_type'] ?? 'node';
        $range_includes = $this->getMappingStorage()->getSchemaPropertyRangeIncludes($schema_type, $schema_property)
          ?: ['Thing'];

        // Make sure that the ranges includes only includes Things
        // and not DataTypes or Enumerations.
        foreach ($range_includes as $range_include_type) {
          if (!$this->schemaTypeManager->isThing($range_include_type)) {
            unset($range_includes[$range_include_type]);
          }
        }

        $handler_settings = [];
        $handler_settings['target_type'] = $target_type;
        $handler_settings['schema_types'] = $range_includes;

        $field_values['settings'] = [
          'handler' => 'schemadotorg:' . $target_type,
          'handler_settings' => $handler_settings,
        ];
        break;

      case 'integer':
      case 'float':
      case 'decimal':
        $unit_plural = $this->schemaTypeManager->getPropertyUnit($schema_property, 0);
        if ($unit_plural) {
          $unit_singular = $this->schemaTypeManager->getPropertyUnit($schema_property, 1);
          if ((string) $unit_singular != (string) $unit_plural) {
            $field_values['settings']['suffix'] = ' ' . $unit_singular . '| ' . $unit_plural;
          }
          else {
            $field_values['settings']['suffix'] = ' ' . $unit_singular;
          }
        }
        break;

      case 'email':
        $formatter_id = 'email_mailto';
        break;

      case 'string':
        if (!empty($field_storage_values['max_length'])) {
          $field_storage_values['settings'] = [
            'max_length' => $field_storage_values['max_length'],
          ];
          unset($field_storage_values['max_length']);
        }
        break;

      case 'list_string':
        if (!empty($field_storage_values['allowed_values'])) {
          $field_storage_values['settings'] = [
            'allowed_values' => $field_storage_values['allowed_values'],
            'allowed_values_function' => '',
          ];
          unset($field_storage_values['allowed_values']);
        }
        break;
    }
  }

}
