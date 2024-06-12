<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Schema.org mapping type form.
 *
 * @property \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface $entity
 */
class SchemaDotOrgMappingTypeForm extends EntityForm {

  /**
   * The entity display repository.
   */protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->entityDisplayRepository = $container->get('entity_display.repository');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface $entity */
    $entity = $this->getEntity();
    $config_name = 'schemadotorg.schemadotorg_mapping_type.' . ($entity->id() ?? '_na_');

    // Type settings.
    $form['types'] = [
      '#type' => 'details',
      '#title' => $this->t('Type settings'),
      '#open' => TRUE,
    ];
    if ($entity->isNew()) {
      $form['types']['target_entity_type_id'] = [
        '#type' => 'select',
        '#title' => $this->t('Target entity type'),
        '#options' => $this->getTargetEntityTypeOptions(),
        '#required' => TRUE,
      ];
    }
    else {
      $form['types']['target_entity_type'] = [
        '#type' => 'item',
        '#title' => $this->t('Target entity type'),
        '#value' => $entity->id(),
        '#markup' => $entity->label(),
      ];
      // Display a warning about the missing entity type.
      if (!$this->entityTypeManager->hasDefinition($entity->id())) {
        $t_args = ['%entity_type' => $entity->id()];
        $message = $this->t('The target entity type %entity_type is missing and its associated module most likely needs to be installed.', $t_args);
        $this->messenger()->addWarning($message);
      }
    }
    $form['types']['multiple'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow multiple mappings to point to the same Schema.org type'),
      '#description' => $this->t('If unchecked, new mappings to an existing Schema.org type will display a warning'),
      '#return_value' => TRUE,
      '#default_value' => $entity->get('multiple'),
    ];
    $form['types']['label_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label prefix'),
      '#description' => $this->t("Enter the prefix to be prepended to the bundle's label. For example, you can enter 'Schema.org: ' to distinguish a Schema.org type from existing bundles."),
      '#default_value' => $entity->get('label_prefix'),
    ];
    $form['types']['id_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ID prefix'),
      '#description' => $this->t("Enter the prefix to prepended to bundle names. For example, you can enter 'schema_' to create a 'schema_*' namespace for all Schema.org related bundles."),
      '#pattern' => '^[a-z0-9_]+$',
      '#default_value' => $entity->get('id_prefix'),
    ];
    $form['types']['recommended_schema_types'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Recommended Schema.org types'),
      '#description' => $this->t('Enter recommended Schema.org types to be displayed when creating a new Schema.org type. Recommended Schema.org types will only be displayed on entity types that support adding new Schema.org types.'),
      '#description_link' => 'types',
      '#config_name' => $config_name,
      '#config_key' => 'recommended_schema_types',
      '#example' => '
group_name:
  name: Group label
  types:
    - SchemaType01
    - SchemaType01
    - SchemaType01
',
    ];
    $form['types']['default_schema_types'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org types'),
      '#description' => $this->t('Enter default Schema.org types that will automatically be assigned to an existing entity type/bundle.'),
      '#description_link' => 'types',
      '#config_name' => $config_name,
      '#config_key' => 'default_schema_types',
      '#example' => 'entity_type: SchemaType',
    ];
    $view_modes = $this->entityDisplayRepository->getViewModes($entity->id());
    $view_modes = array_intersect_key($view_modes, ['teaser' => 'teaser']);
    if ($view_modes) {
      $form['types']['default_schema_type_view_displays'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Default Schema.org type view displays'),
        '#open' => TRUE,
      ];
      foreach ($view_modes as $view_mode_id => $view_mode) {
        $form['types']['default_schema_type_view_displays'][$view_mode_id] = [
          '#type' => 'schemadotorg_settings',
          '#title' => $view_mode['label'],
          '#description' => $this->t('Enter Schema.org types default view display for @display.', ['@display' => $view_mode['label']]),
          '#config_name' => $config_name,
          '#config_key' => 'default_schema_type_view_displays.' . $view_mode_id,
          '#example' => '
SchemaType:
  - property_name_01
  - property_name_02
',
        ];
      }
    }

    // Property settings.
    $form['properties'] = [
      '#type' => 'details',
      '#title' => $this->t('Property settings'),
      '#open' => TRUE,
    ];
    $form['properties']['default_schema_type_properties'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org type properties'),
      '#description' => $this->t('Enter default Schema.org type properties.')
      . '<br/><br/>'
      . $this->t('Please note: Default properties are automatically inherited from their parent Schema.org type and <a href="https://schema.org/Intangible">Intangible</a> are automatically assigned all defined properties, expect for properties defined via <a href="https://schema.org/Thing">Thing</a>.')
      . ' '
      . $this->t('Prepend a minus to a property to explicitly remove the property from the specific type.'),
      '#description_link' => 'types',
      '#config_name' => $config_name,
      '#config_key' => 'default_schema_type_properties',
      '#example' => "
SchemaType:
  - '-removedPropertyName01'
  - '-removedPropertyName02'
  - '-removedPropertyName03'
  - propertyName01
  - propertyName02
  - propertyName03
",
    ];
    $form['properties']['default_base_fields'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default base field to Schema.org property mappings'),
      '#description' => $this->t('Enter default base field mappings from existing entity properties and fields to Schema.org properties.')
      . ' ' . $this->t('Leave the property_name value blank to allow the base field to be available but not mapped to a Schema.org property.'),
      '#description_link' => 'properties',
      '#config_name' => $config_name,
      '#config_key' => 'default_base_fields',
      '#example' => '
base_field_name: null
base_field_name:
  - property_name_01
  - property_name_02
',
    ];
    $form['properties']['default_component_weights'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default component display weights'),
      '#description' => $this->t('Enter default display component weights.')
      . ' ' . $this->t('Generally, existing component weights should come after Schema.org fields and their weighting should start at 200.'),
      '#config_name' => $config_name,
      '#config_key' => 'default_component_weights',
      '#example' => '
component_name: 100
field_name: 100
',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);
    $t_args = ['%label' => $this->getEntity()->label()];
    $message = ($result === SAVED_NEW)
      ? $this->t('Created %label mapping type.', $t_args)
      : $this->t('Updated %label mapping type.', $t_args);
    $this->messenger()->addStatus($message);
    $form_state->setRedirectUrl($this->getEntity()->toUrl('collection'));
    return $result;
  }

  /* ************************************************************************ */
  // Options.
  /* ************************************************************************ */

  /**
   * Get available target content entity type options.
   *
   * @return array
   *   Available target content entity type options.
   */
  protected function getTargetEntityTypeOptions(): array {
    $mapping_type_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping_type');
    $definitions = $this->entityTypeManager->getDefinitions();

    $options = [];
    foreach ($definitions as $definition) {
      if ($definition instanceof ContentEntityTypeInterface
        && !$mapping_type_storage->load($definition->id())) {
        $options[$definition->id()] = $definition->getLabel();
      }
    }
    return $options;
  }

}
