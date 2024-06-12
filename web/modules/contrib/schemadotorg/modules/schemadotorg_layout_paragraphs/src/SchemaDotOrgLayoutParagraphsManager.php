<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_layout_paragraphs;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\paragraphs\ParagraphsTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\schemadotorg\Utility\SchemaDotOrgElementHelper;

/**
 * Schema.org layout paragraphs manager.
 */
class SchemaDotOrgLayoutParagraphsManager implements SchemaDotOrgLayoutParagraphsManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgLayoutParagraphsManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getMachineName(): string {
    return $this->schemaNames->camelCaseToDrupalName(static::PROPERTY_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldName(): string {
    return $this->schemaNames->getFieldPrefix() . $this->getMachineName();
  }

  /**
   * {@inheritdoc}
   */
  public function alterMappingDefaults(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void {
    if (!$this->isLayoutParagraphsDefaultType($entity_type_id, $bundle, $schema_type)
      && !$this->isLayoutParagraphsEnabled($entity_type_id, $schema_type)) {
      return;
    }

    $schema_property = static::PROPERTY_NAME;
    $field_name = $this->getFieldName();

    // If the field is already set to be created, leave the default values.
    $default_type = NestedArray::getValue($defaults, ['properties', $schema_property, 'type']);
    if ($default_type === SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD) {
      return;
    }

    $mapping = $this->loadMapping($entity_type_id, $bundle);

    // Check for existing field.
    $field_config = $this->entityTypeManager
      ->getStorage('field_config')
      ->load($entity_type_id . '.' . $bundle . '.' . $field_name);
    if ($field_config) {
      $name = $field_name;
    }
    // Check if layout paragraphs should be added to a new mapping.
    elseif (!$mapping && $this->isLayoutParagraphsDefaultType($entity_type_id, $bundle, $schema_type)) {
      $field_config_storage = $this->entityTypeManager
        ->getStorage('field_storage_config')
        ->load($entity_type_id . '.' . $field_name);
      if ($field_config_storage) {
        $name = $field_name;
      }
      else {
        $name = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;
      }
    }
    // Let the user decide to enable layout paragraphs.
    else {
      $name = '';
    }

    $defaults['properties'][$schema_property]['name'] = $name;
    $defaults['properties'][$schema_property]['type'] = 'field_ui:entity_reference_revisions:paragraph';
    $defaults['properties'][$schema_property]['label'] = (string) $this->t('Layout');
    $defaults['properties'][$schema_property]['machine_name'] = $this->getMachineName();
    $defaults['properties'][$schema_property]['unlimited'] = TRUE;
    $defaults['properties'][$schema_property]['required'] = FALSE;
    $defaults['properties'][$schema_property]['description'] = (string) $this->t('A layout built using paragraphs. Layout paragraphs allows site builders to construct a multi-column landing page using Schema.org related paragraphs types.');
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
    if (!$mapping->getSchemaType()) {
      return;
    }

    $mapping_defaults = $form_state->get('mapping_defaults');

    $schema_type = $mapping->getSchemaType();
    $schema_property = static::PROPERTY_NAME;
    $defaults = $mapping_defaults['properties'][$schema_property] ?? NULL;
    if (empty($defaults)) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    if (!$this->isLayoutParagraphsEnabled($entity_type_id, $schema_type)) {
      return;
    }

    $field_name = $this->getFieldName();
    $field_exists = (bool) $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load($entity_type_id . '.' . $field_name);

    // Store reference to ADD_FIELD.
    $add_field = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;

    // Remove mainEntity from properties.
    unset($form['mapping']['properties'][$schema_property]);

    // Determine if Schema.org type already has layout paragraphs enabled.
    if (!$mapping->isNew() && $defaults['name']) {
      $form['mapping'][$schema_property] = [
        '#type' => 'item',
        '#title' => $this->t('Schema.org layout'),
        '#markup' => $this->t('Enabled'),
        '#input' => FALSE,
        '#weight' => -4,
      ];
      $form['mapping'][$schema_property]['name'] = [
        '#type' => 'value',
        '#parents' => ['mapping', 'properties', $schema_property, 'field', 'name'],
        '#default_value' => $defaults['name'],
      ];
      return;
    }

    // Add create and map a layout paragraphs field to a custom
    // Schema.org property form.
    $form['mapping'][$schema_property] = [
      '#type' => 'details',
      '#title' => $this->t('Schema.org layout'),
      '#open' => ($mapping->isNew() && $defaults['name']),
      '#weight' => -4,
    ];
    $form['mapping'][$schema_property]['name'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Schema.org layout paragraphs'),
      '#description' => $this->t("If checked, a 'Layout' field is added to the content type which allows content authors to build layouts using paragraphs."),
      '#return_value' => $field_exists ? $field_name : $add_field,
      '#parents' => ['mapping', 'properties', $schema_property, 'field', 'name'],
      '#default_value' => $defaults['name'],
    ];
    $form['mapping'][$schema_property][$add_field] = [
      '#type' => 'details',
      '#title' => $this->t('Add field'),
      '#attributes' => ['data-schemadotorg-ui-summary' => $this->t('Paragraph')],
      '#access' => !$field_exists,
      '#states' => [
        'visible' => [
          ':input[name="mapping[properties][' . $schema_property . '][field][name]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['mapping'][$schema_property][$add_field]['type'] = [
      '#type' => 'item',
      '#title' => $this->t('Type'),
      '#markup' => $this->t('Paragraph'),
      '#value' => $defaults['type'],
    ];
    $form['mapping'][$schema_property][$add_field]['label'] = [
      '#type' => 'item',
      '#title' => $this->t('Label'),
      '#markup' => $defaults['label'],
      '#value' => $defaults['label'],
    ];
    $form['mapping'][$schema_property][$add_field]['machine_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Machine-readable name'),
      '#markup' => $this->schemaNames->getFieldPrefix() . $defaults['machine_name'],
      '#value' => $defaults['machine_name'],
    ];
    $form['mapping'][$schema_property][$add_field]['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('Instructions to present to the user below this field on the editing form.'),
      '#default_value' => $defaults['description'],
    ];
    $form['mapping'][$schema_property][$add_field]['unlimited'] = [
      '#type' => 'value',
      '#value' => $defaults['unlimited'],
    ];
    SchemaDotOrgElementHelper::setElementParents(
      $form['mapping'][$schema_property][$add_field],
      ['mapping', 'properties', $schema_property, 'field', $add_field]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterPropertyField(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void {
    // Check that the field is an entity_reference_revisions type that is
    // targeting layout paragraphs.
    if ($field_storage_values['type'] !== 'entity_reference_revisions'
      || $field_storage_values['settings']['target_type'] !== 'paragraph'
      || $schema_property !== static::PROPERTY_NAME) {
      return;
    }

    $entity_type_id = $field_storage_values['entity_type'];

    // Make sure the entity type and Schema.org type supports layout paragraphs.
    if (!$this->isLayoutParagraphsEnabled($entity_type_id, $schema_type)) {
      return;
    }

    $handler_settings = [];

    // Add default paragraphs types to the target bundles.
    $default_paragraph_types = $this->configFactory
      ->get('schemadotorg_layout_paragraphs.settings')
      ->get('default_paragraph_types');
    if ($this->moduleHandler->moduleExists('layout_paragraphs_library')) {
      $default_paragraph_types[] = 'from_library';
    }

    // Make sure the paragraph types exists.
    $existing_paragraph_types = $this->entityTypeManager
      ->getStorage('paragraphs_type')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('id', $default_paragraph_types, 'IN')
      ->execute();
    $default_paragraph_types = array_intersect_key(
      array_combine($default_paragraph_types, $default_paragraph_types),
      array_combine($existing_paragraph_types, $existing_paragraph_types)
    );

    // Start weight at -10 to insert these paragraphs type before
    // the existing paragraph types.
    // @see schemadotorg_paragraphs_schemadotorg_property_field_alter()
    $weight = -10;
    foreach ($default_paragraph_types as $paragraph_type) {
      $handler_settings['target_bundles'][$paragraph_type] = $paragraph_type;
      $handler_settings['target_bundles_drag_drop'][$paragraph_type] = [
        'weight' => $weight,
        'enabled' => TRUE,
      ];
      $weight++;
    }
    // Use core's selection plugin.
    $field_values['settings']['handler'] = 'default:paragraphs';
    $field_values['settings']['handler_settings'] = $handler_settings;

    // Set widget to use layout paragraphs.
    $widget_id = 'layout_paragraphs';
    $widget_settings['empty_message'] = $this->t('Click the [+] sign below to choose your first component.');

    // Set formatter to use layout paragraphs builder with no visible label.
    $formatter_id = 'layout_paragraphs_builder';
    $formatter_settings['label'] = 'hidden';
    $formatter_settings['empty_message'] = $widget_settings['empty_message'];
  }

  /**
   * {@inheritdoc}
   */
  public function paragraphsTypePresave(ParagraphsTypeInterface $paragraphs_type): void {
    $default_paragraph_types = $this->configFactory
      ->get('schemadotorg_layout_paragraphs.settings')
      ->get('default_paragraph_types');
    if (empty($default_paragraph_types)
      || !in_array($paragraphs_type->id(), $default_paragraph_types)) {
      return;
    }

    $behavior_plugins = $paragraphs_type->get('behavior_plugins');

    // Set layouts.
    $default_paragraph_layouts = $this->configFactory
      ->get('schemadotorg_layout_paragraphs.settings')
      ->get('default_paragraph_layouts');
    $available_layouts = $default_paragraph_layouts[$paragraphs_type->id()] ?? NULL;
    if ($available_layouts) {
      $behavior_plugins['layout_paragraphs'] = [
        'enabled' => TRUE,
        'available_layouts' => array_combine($available_layouts, $available_layouts),
      ];
    }

    // Set style options.
    if ($this->moduleHandler->moduleExists('style_options')) {
      $behavior_plugins['style_options'] = ['enabled' => TRUE];
    }

    $paragraphs_type->set('behavior_plugins', $behavior_plugins);
  }

  /**
   * Determine if a type should default to using layout paragraphs.
   *
   * Currently, layout paragraphs are only applicable.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string|null $bundle
   *   A bundle.
   * @param string $schema_type
   *   A Schema.org type.
   *
   * @return bool
   *   TRUE if a type should default to using layout paragraphs
   */
  protected function isLayoutParagraphsDefaultType(string $entity_type_id, ?string $bundle, string $schema_type): bool {
    if ($entity_type_id !== 'node') {
      return FALSE;
    }

    $default_types = $this->configFactory
      ->get('schemadotorg_layout_paragraphs.settings')
      ->get('default_types');
    return (in_array($schema_type, $default_types) || in_array($bundle, $default_types));
  }

  /**
   * Determine if the entity type and Schema.org type support layout paragraphs.
   *
   * Currently, layout paragraphs are only applicable to nodes and Schema.org
   * types without a mainEntity property. This only applies to
   * FAQPage and QAPAge.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return bool
   *   TRUE if the entity type support layout paragraphs.
   */
  protected function isLayoutParagraphsEnabled(string $entity_type_id, string $schema_type): bool {
    if ($entity_type_id !== 'node') {
      return FALSE;
    }

    $property_defaults = $this->loadMappingType($entity_type_id)
      ->getDefaultSchemaTypeProperties($schema_type);
    return !in_array(static::PROPERTY_NAME, $property_defaults);
  }

}
