<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Schema.org mapping form.
 *
 * @see \Drupal\field_ui\Form\EntityDisplayFormBase
 *
 * @property \Drupal\schemadotorg\SchemaDotOrgMappingInterface $entity
 */
class SchemaDotOrgUiMappingForm extends EntityForm {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Add new field mapping option.
   */
  public const ADD_FIELD = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;

  /**
   * The service container.
   */
  protected ContainerInterface $container;

  /**
   * The theme manager.
   */
  protected ThemeManagerInterface $themeManager;

  /**
   * The Schema.org schema names services.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org schema type builder service.
   */
  protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder;

  /**
   * The Schema.org entity field manager.
   */
  protected SchemaDotOrgEntityFieldManagerInterface $schemaEntityFieldManager;

  /**
   * The Schema.org mapping manager.
   */
  protected SchemaDotOrgMappingManagerInterface $schemaMappingManager;

  /**
   * Available fields as options.
   */
  protected array $fieldOptions;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->container = $container;
    $instance->themeManager = $container->get('theme.manager');
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    $instance->schemaEntityFieldManager = $container->get('schemadotorg.entity_field_manager');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id): ?EntityInterface {
    $mapping_storage = $this->getMappingStorage();
    $mapping_type_storage = $this->getMappingTypeStorage();

    $route_parameters = $route_match->getParameters()->all();

    $target_entity_type_id = $route_parameters['entity_type_id'] ?? NULL;
    $target_bundle = $route_parameters['bundle'] ?? NULL;
    $schema_type = $this->getRequest()->query->get('type');

    // Validate the Schema.org type before continuing.
    if ($schema_type && !$this->schemaTypeManager->isThing($schema_type)) {
      // Only display a warning when an invalid type is passed via
      // the query string.
      if ($this->getRequest()->isMethod('get')) {
        $t_args = ['%type' => $schema_type];
        $this->messenger()->addWarning($this->t("The Schema.org type %type is not valid.", $t_args));
      }
      $schema_type = NULL;
    }

    // Get the Schema.org mapping from the current route match.
    if (!$target_entity_type_id && !$schema_type) {
      return parent::getEntityFromRouteMatch($route_match, $entity_type_id);
    }

    // Default the target entity type to be a node.
    $target_entity_type_id = $target_entity_type_id ?? 'node';

    // Load mapping type since the target entity type id was just set.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
    $mapping_type = $mapping_type_storage->load($target_entity_type_id);
    $supports_multiple = $mapping_type->supportsMultiple();
    $default_schema_type = $mapping_type->getDefaultSchemaType($target_bundle);

    // Display warning that new Schema.org type is already mapped.
    if ($mapping_storage->isSchemaTypeMapped($target_entity_type_id, $schema_type)
      && !$supports_multiple
      && $this->getRequest()->isMethod('get')) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $entity */
      $entity = $mapping_storage->loadBySchemaType($target_entity_type_id, $schema_type);
      $target_entity = $entity->getTargetEntityBundleEntity();
      $t_args = [
        '%type' => $schema_type,
        ':href' => $target_entity->toUrl()->toString(),
        '@label' => $target_entity->label(),
        '@id' => $target_entity->id(),
      ];
      $this->messenger()->addWarning($this->t('%type is currently mapped to <a href=":href">@label</a> (@id).', $t_args));
    }

    // Set default Schema.org type for the current target entity type and bundle.
    $schema_type = $schema_type ?: $default_schema_type;

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $entity */
    $entity = $mapping_storage->load($target_entity_type_id . '.' . $target_bundle)
      ?: $mapping_storage->create([
        'target_entity_type_id' => $target_entity_type_id,
        'target_bundle' => $target_bundle,
        'schema_type' => $schema_type,
      ]);

    // Make sure the Schema.org mapping entity's Schema.org type is set.
    $entity->setSchemaType($entity->getSchemaType() ?: $schema_type);

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    // Add the active theme name as a class to form.
    $active_theme_name = $this->themeManager->getActiveTheme()->getName();
    $form['#attributes']['class'][] = 'schemadotorg-ui-' . $active_theme_name;

    // Disable inline form errors for CLI (a.k.a Drush).
    // @see \Drupal\schemadotorg\Commands\SchemaDotOrgCommands::createType
    if (PHP_SAPI === 'cli') {
      $form['#disable_inline_form_errors'] = TRUE;
    }

    if ($this->getSchemaType()) {
      // Display Schema.org type property to field mapping form.
      return $this->buildFieldTypeForm($form, $form_state);
    }
    else {
      // Display select Schema.org form.
      $entity_type_id = $this->getTargetEntityTypeId();
      return SchemaDotOrgUiMappingTypeSelectForm::create($this->container)
        ->buildForm($form, $form_state, $entity_type_id);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    // Hide form actions when no Schema.org type is selected.
    if (!$this->getSchemaType()) {
      return [];
    }

    return parent::actions($form, $form_state);
  }

  /* ************************************************************************ */
  // Submit and save methods.
  /* ************************************************************************ */

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $mapping_entity = $this->getEntity();

    $mapping_values = $form_state->getValue('mapping');

    // Validate the bundle entity before it is created.
    if ($mapping_entity->isNewTargetEntityTypeBundle()) {
      $entity_values = $mapping_values['entity'];
      $bundle_entity_type_id = $mapping_entity->getTargetEntityTypeBundleId();
      /** @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage $bundle_entity_storage */
      $bundle_entity_storage = $this->entityTypeManager->getStorage($bundle_entity_type_id);
      $bundle_entity = $bundle_entity_storage->load($entity_values['id']);
      if ($bundle_entity) {
        $target_entity_type_bundle_definition = $this->getEntity()->getTargetEntityTypeBundleDefinition();
        $t_args = [
          '%id' => $bundle_entity->id(),
          '@type' => $target_entity_type_bundle_definition->getSingularLabel(),
        ];
        $message = $this->t('A %id @type already exists. Please enter a different name.', $t_args);
        $element = NestedArray::getValue($form, ['mapping', 'entity', 'id']);
        $form_state->setError($element, $message);
      }
    }

    // Validate the new field names before they are created.
    $entity_type_id = $mapping_entity->getTargetEntityTypeId();
    /** @var \Drupal\field\FieldStorageConfigStorage $field_storage_config_storage */
    $field_storage_config_storage = $this->entityTypeManager->getStorage('field_storage_config');
    $properties = $mapping_values['properties'];
    foreach ($properties as $property_name => $property_values) {
      if ($property_values['field']['name'] === static::ADD_FIELD) {
        // Validate required field properties.
        $required_element_names = ['type', 'label', 'machine_name'];
        foreach ($required_element_names as $required_element_name) {
          if (empty($property_values['field'][static::ADD_FIELD][$required_element_name])) {
            $element = NestedArray::getValue($form, ['mapping', 'properties', $property_name, 'field', static::ADD_FIELD, $required_element_name]);
            $form_state->setError($element, $this->t('@name field is required for the @property property mapping.', ['@name' => $element['#title'], '@property' => $property_name]));
          }
        }

        // Validate that a new field name does not already exist.
        if (!empty($property_values['field'][static::ADD_FIELD]['machine_name'])) {
          $field_name = $this->schemaNames->getFieldPrefix() . $property_values['field'][static::ADD_FIELD]['machine_name'];
          if ($field_storage_config_storage->load($entity_type_id . '.' . $field_name)) {
            $element = NestedArray::getValue($form, ['mapping', 'properties', $property_name, 'field', static::ADD_FIELD, 'machine_name']);
            $t_args = ['%name' => $field_name];
            $message = $this->t('A %name field already exists. Please enter a different name or select the existing field.', $t_args);
            $form_state->setError($element, $message);
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $schema_type = $this->getSchemaType();
    $entity_type_id = $this->getTargetEntityTypeId();
    $mapping_entity = $this->getEntity();

    // Default the redirect to the current page if we are update the
    // Schema.org tab in the field UI.
    if (preg_match('/entity\.[a-z]+\.schemadotorg_mapping/', $this->getRouteMatch()->getRouteName())) {
      $form_state->setRedirect('<current>');
    }

    $mapping_values = $this->getMappingValuesFromFormState($form_state);

    // Track if new entity is being created.
    $is_new_mapping = $mapping_entity->isNew();
    $is_new_entity = $mapping_entity->isNewTargetEntityTypeBundle();

    // Track new property fields being created.
    $new_field_names = [];
    foreach ($mapping_values['properties'] as $field) {
      if ($field['name'] == static::ADD_FIELD) {
        $new_field_names[] = $field['label'];
      }
    }

    // Set the mapping which will create or update it.
    $mapping_entity = $this->schemaMappingManager->saveMapping($entity_type_id, $schema_type, $mapping_values);
    $this->setEntity($mapping_entity);

    // Display message and log new bundle entity.
    if ($is_new_entity) {
      // Display message.
      $bundle_entity = $mapping_entity->getTargetEntityBundleEntity();
      $bundle_entity_type_definition = $mapping_entity->getTargetEntityTypeBundleDefinition();
      $t_args = [
        '@type' => $bundle_entity_type_definition->getSingularLabel(),
        '%name' => $bundle_entity->label(),
      ];
      $this->messenger()->addStatus($this->t('The @type %name has been added.', $t_args));

      // Log new bundle entity.
      $entity_type_id = $this->getTargetEntityTypeId();
      $context = array_merge($t_args, ['link' => $bundle_entity->toLink($this->t('View'), 'collection')->toString()]);
      $this->logger($entity_type_id)->notice('Added @type %name.', $context);

      // Set redirect to bundle entity collection.
      $form_state->setRedirectUrl($bundle_entity->toUrl('collection'));
    }

    // Display message about new fields.
    if ($new_field_names) {
      $message = $this->formatPlural(
        count($new_field_names),
        'Added %field_names field.',
        'Added %field_names fields.',
        ['%field_names' => implode('; ', $new_field_names)]
      );
      $this->messenger()->addStatus($message);
    }

    // Display message about mapping.
    $t_args = ['%label' => $this->getEntity()->label()];
    $message = ($is_new_mapping)
      ? $this->t('Created %label mapping.', $t_args)
      : $this->t('Updated %label mapping.', $t_args);
    $this->messenger()->addStatus($message);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    // Do nothing and allow the entity to be saved via ::submitForm.
    return 1;
  }

  /**
   * Get Schema.org mapping values from form state.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   Schema.org mapping values.
   */
  protected function getMappingValuesFromFormState(FormStateInterface $form_state): array {
    $mapping_defaults = $form_state->get('mapping_defaults');
    $mapping_values = $form_state->getValue('mapping');

    // Entity.
    $mapping_values['entity'] = NestedArray::mergeDeep(
      $mapping_defaults['entity'] ?? [],
      $mapping_values['entity'] ?? []
    );

    // Properties.
    foreach ($mapping_values['properties'] as $property => $property_values) {
      $mapping_values['properties'][$property] = [
        'name' => $property_values['field']['name'],
      ];
      if (isset($property_values['field'][static::ADD_FIELD])) {
        $mapping_values['properties'][$property] += $property_values['field'][static::ADD_FIELD];
      }
    }
    $mapping_values['properties'] = NestedArray::mergeDeep(
      $mapping_defaults['properties'] ?? [],
      $mapping_values['properties'],
    );

    // Additional mappings.
    $mapping_values['additional_mappings'] = $mapping_values['additional_mappings'] ?? [];

    // Third party settings.
    $mapping_values['third_party_settings'] = ($mapping_values['third_party_settings'] ?? []) +
      ($mapping_defaults['third_party_settings'] ?? []);

    return $mapping_values;
  }

  /* ************************************************************************ */
  // Form build methods.
  /* ************************************************************************ */

  /**
   * Build the Schema.org type form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function buildFieldTypeForm(array &$form, FormStateInterface $form_state): array {
    // Get mapping defaults.
    $defaults = $this->schemaMappingManager->getMappingDefaults(
      $this->getTargetEntityTypeId(),
      $this->getTargetBundle(),
      $this->getSchemaType()
    );

    // Set mapping defaults in $form_state.
    $form_state->set('mapping_defaults', $defaults);

    // Build the entity type summary form.
    $this->buildEntityTypeForm($form);

    // Build the Schema.org type summary form.
    $this->buildSchemaTypeForm($form);

    // Schema.org entity type/bundle and properties mapping form.
    $form['mapping'] = ['#tree' => TRUE];
    $this->buildAddEntityForm($form['mapping'], $defaults['entity']);
    $this->buildSchemaPropertiesForm($form['mapping'], $defaults['properties']);
    $this->buildThirdPartySettingsForm($form['mapping'], $defaults['third_party_settings']);

    // Load the jsTree before the Schema.org UI library to ensure that
    // jsTree loads and works inside modal dialogs.
    $form['#attached']['library'][] = 'schemadotorg/schemadotorg.jstree';
    $form['#attached']['library'][] = 'schemadotorg_ui/schemadotorg_ui';

    // Display warning when creating a new entity or fields to UI and not CLI.
    $is_new = $this->getEntity()->isNew();
    $is_get = $this->getRequest()->isMethod('get');
    $is_cli = (PHP_SAPI === 'cli');
    if ($is_new && $is_get && !$is_cli) {
      if ($this->getEntity()->isTargetEntityTypeBundle()) {
        $type_definition = $this->getSchemaTypeDefinition();
        $target_entity_type_bundle_definition = $this->getEntity()->getTargetEntityTypeBundleDefinition();
        $t_args = [
          '%schema_type' => $type_definition['drupal_label'],
          '@entity_type' => $target_entity_type_bundle_definition->getSingularLabel(),
        ];
        $this->messenger()->addWarning($this->t('Please review the %schema_type @entity_type and the new fields that will be created below.', $t_args));
      }
      else {
        $this->messenger()->addWarning($this->t('Please review the new fields that will be created below.'));
      }
    }

    return $form;
  }

  /**
   * Build the entity type summary form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   */
  protected function buildEntityTypeForm(array &$form): void {
    $entity = $this->getEntity();
    $entity_type_bundle = $entity->getTargetEntityBundleEntity();
    if ($entity_type_bundle) {
      // Display bundle entity information. (i.e. node, media, etc...)
      $target_entity_type_bundle_definition = $entity->getTargetEntityTypeBundleDefinition();
      $link = $entity_type_bundle->toLink($entity_type_bundle->label(), 'edit-form')->toRenderable();
      $form['entity_type'] = [
        '#type' => 'item',
        '#title' => $target_entity_type_bundle_definition->getLabel(),
        'link' => $link + ['#suffix' => ' (' . $entity_type_bundle->id() . ')'],
        '#weight' => -20,
      ];
    }
    else {
      // Display entity information. (i.e. user)
      $target_entity_type_definition = $entity->getTargetEntityTypeDefinition();
      $form['entity_type'] = [
        '#type' => 'item',
        '#title' => $this->t('Entity type'),
        '#markup' => $entity->isTargetEntityTypeBundle()
          ? $target_entity_type_definition->getBundleLabel()
          : $target_entity_type_definition->getLabel(),
        '#weight' => -20,
      ];
    }
  }

  /**
   * Build the Schema.org type summary form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   */
  protected function buildSchemaTypeForm(array &$form): void {
    $type_definition = $this->getSchemaTypeDefinition();

    // Pending warning.
    if ($type_definition['is_part_of'] === 'https://pending.schema.org') {
      $t_args = [':href' => 'https://schema.org/docs/pending.home.html'];
      $form['schema_pending'] = [
        '#markup' => $this->t('This term is proposed for full integration into Schema.org, <a href=":href">pending</a> implementation feedback and adoption from applications and websites.', $t_args),
        '#prefix' => '<p><em>',
        '#suffix' => '</em></p>',
        '#weight' => -100,
      ];
    }

    // Schema.org type.
    $form['schema_type'] = [
      '#type' => 'item',
      '#title' => $this->t('Schema.org type'),
      // Make sure this item does not set a $form_state value.
      '#input' => FALSE,
      '#weight' => -20,
    ];
    $form['schema_type']['label'] = [
      '#type' => 'link',
      '#title' => $type_definition['label'],
      '#url' => $this->schemaTypeBuilder->getItemUrl($type_definition['label']),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];
    $form['schema_type']['comment'] = [
      '#markup' => $this->schemaTypeBuilder->formatComment($type_definition['drupal_description']),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];
  }

  /**
   * Build the add entity type form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $defaults
   *   The entity type default values.
   */
  protected function buildAddEntityForm(array &$form, array $defaults): void {
    if (!$this->getEntity()->isNewTargetEntityTypeBundle()) {
      $form['entity'] = [
        '#type' => 'value',
        '#value' => $defaults,
      ];
      return;
    }

    $target_entity_type_bundle_definition = $this->getEntity()->getTargetEntityTypeBundleDefinition();
    $t_args = ['@name' => $target_entity_type_bundle_definition->getSingularLabel()];

    $form['entity'] = [
      '#type' => 'details',
      '#title' => $this->t('Add @name', $t_args),
      '#open' => TRUE,
      '#weight' => -10,
    ];
    $form['entity']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('The human-readable name of this content type. This text will be displayed as part of the list on the Add content page. This name must be unique.'),
      '#required' => TRUE,
      '#default_value' => $defaults['label'],
    ];
    $form['entity']['id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine-readable name'),
      '#description' => $this->t('A unique machine-readable name for this content type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the Add content page.'),
      '#required' => TRUE,
      '#pattern' => '[_0-9a-z]+',
      '#maxlength' => $this->schemaNames->getNameMaxLength('types'),
      '#default_value' => $defaults['id'],
    ];
    $form['entity']['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('This text will be displayed on the <em>Add new content</em> page.'),
      '#default_value' => $defaults['description'],
    ];
  }

  /**
   * Build Schema.org properties table.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $defaults
   *   The Schema.org properties default values.
   */
  protected function buildSchemaPropertiesForm(array &$form, array $defaults): void {
    $type = $this->getSchemaType();
    $fields = ['label', 'comment', 'range_includes', 'superseded_by'];
    $property_definitions = $this->schemaTypeManager->getTypeProperties($type, $fields);

    $ignored_properties = $this->schemaMappingManager->getIgnoredProperties();
    $ignored_properties = array_intersect_key($ignored_properties, $property_definitions);
    $property_definitions = array_diff_key($property_definitions, $ignored_properties);

    // Header.
    $header = [];
    $header['property'] = ['data' => $this->t('Schema.org property'), 'width' => '50%'];
    $header['field'] = ['data' => $this->t('Drupal field'), 'width' => '50%'];

    // Rows.
    $rows = [];
    foreach ($property_definitions as $property => $property_definition) {
      // Skip properties without defaults which are usually
      // superseded or ignored properties.
      if (empty($defaults[$property])) {
        continue;
      }

      $row = [];

      // Property.
      $row['property'] = $this->buildSchemaPropertyDefinitionInformation($property_definition);

      // Field.
      $row['field'] = $this->buildSchemaPropertyFieldForm($property_definition, $defaults[$property]);

      // Highlight mapped properties using custom '#row_class' property.
      if (isset($row['field']['#row_class'])) {
        $row['#attributes'] = ['class' => [$row['field']['#row_class']]];
        unset($row['field']['#row_class']);
      }

      $rows[$property] = $row;
    }

    // Filter form.
    $form['filter'] = [
      '#type' => 'search',
      '#title' => $this->t('Filter'),
      '#title_display' => 'invisible',
      '#size' => 30,
      '#placeholder' => $this->t('Filter by Schema.org property'),
      '#attributes' => [
        'class' => ['schemadotorg-ui-properties-filter-text'],
        'title' => $this->t('Enter a keyword to filter properties by.'),
      ],
      '#wrapper_attributes' => [
        'class' => ['schemadotorg-ui-properties-filter'],
      ],
      '#parents' => ['filter'],
      '#weight' => 0,
    ];

    // Properties table.
    $form['properties'] = [
      '#type' => 'table',
      '#header' => $header,
      '#attributes' => ['class' => ['schemadotorg-ui-properties']],
      '#weight' => 0,
    ] + $rows;

    // Ignored properties.
    if ($ignored_properties) {
      $form['ignored_properties'] = [
        '#type' => 'details',
        '#title' => $this->t('Ignored properties [@count]', ['@count' => count($ignored_properties)]),
        '#description' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['description']],
          'description' => ['#markup' => $this->t('The below properties are not displayed to simplify the user experience.')],
        ],
        'links' => $this->schemaTypeBuilder->buildItemsLinks($ignored_properties),
        '#weight' => 0,
      ];
      if ($this->currentUser()->hasPermission('administer schemadotorg')) {
        $form['ignored_properties']['configure'] = [
          '#type' => 'link',
          '#title' => $this->t('Configure ignored properties'),
          '#url' => Url::fromRoute('schemadotorg.settings.properties', [], ['query' => $this->getRedirectDestination()->getAsArray()]),
          '#attributes' => ['class' => ['button', 'button--small']],
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
    }
  }

  /**
   * Build Schema.org property information.
   *
   * @param array $definition
   *   The Schema.org property's definition.
   *
   * @return array
   *   A renderable array containing a Schema.org property's information.
   */
  protected function buildSchemaPropertyDefinitionInformation(array $definition): array {
    $options = ['attributes' => ['target' => '_blank']];
    return [
      '#prefix' => '<div class="schemadotorg-ui-property">',
      '#suffix' => '</div>',
      'label' => [
        '#type' => 'link',
        '#title' => $definition['label'],
        '#url' => $this->schemaTypeBuilder->getItemUrl($definition['label']),
        '#prefix' => '<div class="schemadotorg-ui-property--label"><strong>',
        '#suffix' => '</strong></div>',
      ],
      'comment' => [
        '#markup' => $this->schemaTypeBuilder->formatComment($definition['drupal_description'], $options),
        '#prefix' => '<div class="schemadotorg-ui-property--comment">',
        '#suffix' => '</div>',
      ],
      'range_includes' => [
        'links' => $this->schemaTypeBuilder->buildItemsLinks($definition['range_includes'], $options),
        '#prefix' => '<div class="schemadotorg-ui-property--range-includes">(',
        '#suffix' => ')</div>',
      ],
    ];
  }

  /**
   * Build Schema.org property field form.
   *
   * @param array $definition
   *   The Schema.org property's definition.
   * @param array $defaults
   *   The Schema.org property's default values.
   *
   * @return array
   *   A Schema.org property field form.
   */
  protected function buildSchemaPropertyFieldForm(array $definition, array $defaults): array {
    $schema_type = $this->getSchemaType();
    $property = $definition['label'];
    $property_maxlength = $this->schemaNames->getNameMaxLength('properties');

    $mapping_entity = $this->getEntity();
    $is_new_mapping = $mapping_entity->isNew();

    // Initialize field options.
    if (!isset($this->fieldOptions)) {
      $this->fieldOptions = $this->schemaEntityFieldManager->getFieldOptions(
        $this->getTargetEntityTypeId(),
        $this->getTargetBundle()
      );
    }

    // Get Schema.org property field type options with optgroups.
    $field_type_options = $this->schemaEntityFieldManager->getPropertyFieldTypeOptions($schema_type, $property);

    // NOTE:
    // Setting .form-required via #label_attributes instead of using
    // #states to improve the page load time.
    // phpcs:ignore
    // $required_property = ['#states' => ['required' => [':input[name="properties[' . $property . '][field][name]"]' => ['value' => static::ADD_FIELD]]]];
    // @see ::validateForm
    $required_property = ['#label_attributes' => ['class' => ['form-required']]];

    $form = [];

    // Field name.
    $field_default_value = $defaults['name'];
    $has_field_default_value = $field_default_value && $field_default_value !== static::ADD_FIELD;

    $field_options = $this->fieldOptions;
    // If no default value and the 'Recommended field' exists,
    // set it immediately after ADD_FIELD.
    if (!$has_field_default_value) {
      $existing_field_name = $this->schemaNames->getFieldPrefix() . $defaults['machine_name'];
      $existing_field_options =& $field_options[(string) $this->t('Existing fields')];
      if (isset($existing_field_options[$existing_field_name])) {
        $field_optgroup = (string) $this->t('Recommended field');
        $field_options = [
          self::ADD_FIELD => $field_options[self::ADD_FIELD],
          $field_optgroup => [$existing_field_name => $existing_field_options[$existing_field_name]],
        ] + $field_options;
        unset($existing_field_options[$existing_field_name]);
      }
    }

    if ($has_field_default_value && !$is_new_mapping) {
      $field_empty_options = $this->t('- Remove field mapping -');
    }
    else {
      $field_empty_options = $this->t('- Select or add field -');
    }

    $form['name'] = [
      '#type' => 'select',
      '#title' => $this->t('Field'),
      '#title_display' => 'invisible',
      '#options' => $field_options,
      '#empty_option' => $field_empty_options,
      '#default_value' => $field_default_value,
    ];

    // Add new field.
    $form[static::ADD_FIELD] = [
      '#type' => 'details',
      '#title' => $this->t('Add field'),
      '#attributes' => ['class' => ['schemadotorg-ui--add-field']],
      '#states' => [
        'visible' => [
          ':input[name="mapping[properties][' . $property . '][field][name]"]' => ['value' => static::ADD_FIELD],
        ],
      ],
    ];

    $form[static::ADD_FIELD]['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Field type'),
      '#empty_option' => $this->t('- Select a field type -'),
      '#options' => $field_type_options,
      '#default_value' => $defaults['type'],
    ] + $required_property;

    $form[static::ADD_FIELD]['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#size' => 40,
      '#default_value' => $defaults['label'],
    ] + $required_property;

    $form[static::ADD_FIELD]['machine_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine-readable name'),
      '#description' => $this->t('A unique machine-readable name containing letters, numbers, and underscores.'),
      '#maxlength' => $property_maxlength,
      '#size' => $property_maxlength,
      '#pattern' => '[_0-9a-z]+',
      '#field_prefix' => $this->schemaNames->getFieldPrefix(),
      '#default_value' => $defaults['machine_name'],
      '#attributes' => ['style' => 'width: 20em'],
      '#wrapper_attributes' => ['style' => 'white-space: nowrap'],
    ] + $required_property;

    $form[static::ADD_FIELD]['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('Instructions to present to the user below this field on the editing form.'),
      '#default_value' => $defaults['description'],
    ];

    $form[static::ADD_FIELD]['unlimited'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Unlimited number of values'),
      '#default_value' => $defaults['unlimited'],
    ];

    $form[static::ADD_FIELD]['required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Required field'),
      '#default_value' => $defaults['required'],
    ];

    // Display property status, which can be new (warning) or mapped (success).
    if ($defaults['name']) {
      $form['#row_class'] = ($defaults['name'] === static::ADD_FIELD)
        ? 'color-warning'
        : 'color-success';
    }

    return $form;
  }

  /**
   * Build Schema.org type item to be displayed in comma or hierarchical lists.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   A renderable array containing the Schema.org type item.
   */
  protected function buildSchemaTypeItem(string $type): array {
    $schema_mapping_storage = $this->getMappingStorage();
    $entity_type_id = $this->getTargetEntityTypeId();
    if ($schema_mapping_storage->isSchemaTypeMapped($entity_type_id, $type)) {
      return ['#markup' => $type];
    }
    else {
      return [
        '#type' => 'link',
        '#title' => $type,
        '#url' => Url::fromRoute('<current>', [], ['query' => ['type' => $type]]),
      ];
    }
  }

  /**
   * Build Schema.org third party settings form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $defaults
   *   The Schema.org properties default values.
   */
  protected function buildThirdPartySettingsForm(array &$form, array $defaults): void {
    $form['third_party_settings'] = ['#weight' => -1];
  }

  /* ************************************************************************ */
  // Schema.org helper methods.
  /* ************************************************************************ */

  /**
   * Gets the Schema.org type.
   *
   * @return string|null
   *   The Schema.org type.
   */
  protected function getSchemaType(): ?string {
    return $this->getEntity()->getSchemaType();
  }

  /**
   * Gets the Schema.org type definition.
   *
   * @return array|false
   *   The Schema.org type definition.
   */
  protected function getSchemaTypeDefinition(): array|FALSE {
    return $this->schemaTypeManager->getType($this->getSchemaType());
  }

  /* ************************************************************************ */
  // Entity helper methods.
  /* ************************************************************************ */

  /**
   * Gets the Schema.org mapping entity.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface
   *   The Schema.org mapping entity.
   */
  public function getEntity(): SchemaDotOrgMappingInterface {
    return $this->entity;
  }

  /**
   * Get Schema.org mapping type.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null
   *   The Schema.org mapping type.
   */
  protected function getMappingType(): ?SchemaDotOrgMappingTypeInterface {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
    $mapping_type = $this->getMappingTypeStorage()->load($this->getTargetEntityTypeId());
    return $mapping_type;
  }

  /**
   * Gets the current entity type ID (i.e. node, block_content, user, etc...).
   *
   * @return string
   *   The current entity type ID
   */
  protected function getTargetEntityTypeId(): string {
    return $this->getEntity()->getTargetEntityTypeId();
  }

  /**
   * Gets the current entity bundle.
   *
   * @return string|null
   *   The current entity bundle.
   */
  protected function getTargetBundle(): ?string {
    return $this->getEntity()->getTargetBundle();
  }

}
