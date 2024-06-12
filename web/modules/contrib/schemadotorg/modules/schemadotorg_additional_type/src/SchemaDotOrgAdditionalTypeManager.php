<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_type;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Utility\SchemaDotOrgElementHelper;

/**
 * Schema.org additional type manager.
 */
class SchemaDotOrgAdditionalTypeManager implements SchemaDotOrgAdditionalTypeManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgAdditionalTypeManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The schema dot org names.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The schema dot org schema type manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void {
    // Handle existing additional type property mapping.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $this->entityTypeManager
      ->getStorage('schemadotorg_mapping')
      ->load("$entity_type_id.$bundle");
    if ($mapping && $mapping->hasSchemaPropertyMapping('additionalType')) {
      return;
    }

    $allowed_values = $this->getAllowedValues($schema_type);
    if (empty($allowed_values)) {
      return;
    }

    $additional_type_property =& $defaults['properties']['additionalType'];

    // Set add field for Schema.org types that should default to
    // using the additional type property.
    $default_types = $this->configFactory
      ->get('schemadotorg_additional_type.settings')
      ->get('default_types');
    $is_default_type = (!$mapping && in_array($schema_type, $default_types));
    $additional_type_property['name'] = $is_default_type ? SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD : '';

    // Set field label.
    $additional_type_property['label'] = (string) $this->t('Type');

    // Set field type.
    $additional_type_property['type'] = 'list_string';

    // Set allowed values.
    $additional_type_property['allowed_values'] = $allowed_values;

    // Set machine name with additional type suffix.
    $machine_name_suffix = '_type';
    $machine_name_max_length = $this->schemaNames->getNameMaxLength('properties') - strlen($machine_name_suffix);
    $options = [
      'maxlength' => $machine_name_max_length,
      'truncate' => TRUE,
    ];
    $machine_name = $bundle ?: $this->schemaNames->camelCaseToDrupalName($schema_type, $options);
    $machine_name .= $machine_name_suffix;
    $additional_type_property['machine_name'] = $machine_name;
  }

  /**
   * {@inheritdoc}
   */
  public function alterMappingForm(array &$form, FormStateInterface &$form_state): void {
    if (!$this->moduleHandler->moduleExists('schemadotorg_ui')) {
      return;
    }

    /** @var \Drupal\schemadotorg\Form\SchemaDotOrgMappingForm $form_object */
    $form_object = $form_state->getFormObject();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $form_object->getEntity();

    // Exit if no Schema.org type has been selected.
    $schema_type = $mapping->getSchemaType();
    if (!$schema_type) {
      return;
    }

    // Make sure the Schema.org type has subtypes or default allowed values.
    $allowed_values = $this->getAllowedValues($schema_type);
    if (empty($allowed_values)) {
      return;
    }

    // Set additional type defaults from mapping defaults in $form_state.
    // @see \Drupal\schemadotorg_ui\Form\SchemaDotOrgUiMappingForm::buildFieldTypeForm
    $mapping_defaults = $form_state->get('mapping_defaults');
    $additional_type_defaults = $mapping_defaults['properties']['additionalType'] ?? NULL;

    // Make sure the current Schema.org type supports additional typing.
    if (empty($additional_type_defaults)) {
      return;
    }

    // Store reference to ADD_FIELD.
    $add_field = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;

    // Determine if Schema.org type already has additional typing enabled and
    // display additional typing information.
    if ($additional_type_defaults['name'] && $additional_type_defaults['name'] !== $add_field) {
      $form['mapping']['additional_type'] = [
        '#type' => 'item',
        '#title' => $this->t('Schema.org additional type'),
        '#markup' => $this->t('Enabled'),
        '#input' => FALSE,
        '#weight' => -5,
      ];
      $form['mapping']['additional_type']['name'] = [
        '#type' => 'value',
        '#parents' => ['mapping', 'properties', 'additionalType', 'field', 'name'],
        '#default_value' => $additional_type_defaults['name'],
      ];
      return;
    }

    // Get the add element.
    $add_element = $form['mapping']['properties']['additionalType']['field'][$add_field];

    // Remove the additionalType from the properties table because the field will
    // be mapped here.
    unset($form['mapping']['properties']['additionalType']);

    // Recreate additional type add field widget.
    $form['mapping']['additional_type'] = [
      '#type' => 'details',
      '#title' => $this->t('Schema.org additional type'),
      '#open' => ($mapping->isNew() && $additional_type_defaults['name']),
      '#weight' => -5,
    ];
    $form['mapping']['additional_type']['name'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Schema.org additional type'),
      '#description' => $this->t("If checked, a 'Type' field is added to the entity which allows content authors to specify a more specific type for the entity."),
      '#return_value' => $add_field,
      '#parents' => ['mapping', 'properties', 'additionalType', 'field', 'name'],
      '#default_value' => $additional_type_defaults['name'],
    ];

    // Append the additional type's add field properties.
    $form['mapping']['additional_type'][$add_field] = $add_element;

    // Adjust the #states trigger to use the checkbox.
    $form['mapping']['additional_type'][$add_field]['#states'] = [
      'visible' => [
        ':input[name="mapping[properties][additionalType][field][name]"]' => ['checked' => TRUE],
      ],
    ];

    // Set the details summary to display the hard coded field type.
    $form['mapping']['additional_type'][$add_field]['#attributes'] = ['data-schemadotorg-ui-summary' => $this->t('List (text)')];

    // Do not allow unlimited and required to be set.
    $form['mapping']['additional_type'][$add_field]['unlimited']['#access'] = FALSE;
    $form['mapping']['additional_type'][$add_field]['unlimited']['#default_value'] = FALSE;
    $form['mapping']['additional_type'][$add_field]['required']['#access'] = FALSE;
    $form['mapping']['additional_type'][$add_field]['required']['#default_value'] = FALSE;

    // Hard code the field type.
    $form['mapping']['additional_type'][$add_field]['type'] = [
      '#type' => 'item',
      '#title' => $this->t('Type'),
      '#markup' => $this->t('List (text)'),
      '#value' => $additional_type_defaults['type'],
    ];

    // Add the allowed values property.
    $form['mapping']['additional_type'][$add_field]['allowed_values'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Allowed values'),
      '#description' => '<p>' . $this->t('Enter allowed Schema.org additional types.') . '</p>'
        . '<p>'
        . $this->t('The possible values this field can contain. Enter one value per line, in the format key|label.') . '<br/>'
        . $this->t('The key is the stored value. The label will be used in displayed values and edit forms.') . '<br/>'
        . $this->t('The label is optional: if a line contains a single string, it will be used as key and label.')
        . '</p>'
        . '<p>' . $this->t('Allowed HTML tags in labels: @tags', ['@tags' => FieldFilteredMarkup::displayAllowedTags()]) . '</p>',
      '#required' => TRUE,
      '#default_value' => $additional_type_defaults['allowed_values'],
      '#example' => '
SubType: Sub Type
AdditionalType: Additional Type
',
    ];

    // Set the #parents for all the add field properties.
    SchemaDotOrgElementHelper::setElementParents(
      $form['mapping']['additional_type'][$add_field],
      ['mapping', 'properties', 'additionalType', 'field', $add_field]
    );
  }

  /**
   * Retrieves the allowed values for a Schema.org type.
   *
   * @param string $schema_type
   *   The schema type.
   *
   * @return array
   *   An array of allowed values for the Schema.org type.
   */
  protected function getAllowedValues(string $schema_type): array {
    $allowed_values = $this->configFactory
      ->get('schemadotorg_additional_type.settings')
      ->get('default_allowed_values');
    return $allowed_values[$schema_type]
      ?? $this->schemaTypeManager->getAllTypeChildrenAsOptions($schema_type);
  }

}
