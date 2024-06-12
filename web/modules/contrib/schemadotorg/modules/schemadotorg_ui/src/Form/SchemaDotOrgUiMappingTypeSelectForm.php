<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Schema.org UI type select form.
 */
class SchemaDotOrgUiMappingTypeSelectForm extends FormBase {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * The module handler.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org schema type builder service.
   */
  protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_ui_mapping_type_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->moduleHandler = $container->get('module_handler');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?string $entity_type_id = NULL): array {
    // Description top.
    if ($this->moduleHandler->moduleExists('schemadotorg_report')
      && $this->currentUser()->hasPermission('access site reports')) {
      $t_args = [
        ':types_href' => Url::fromRoute('schemadotorg_report.types')->toString(),
        ':properties_href' => Url::fromRoute('schemadotorg_report.properties')->toString(),
        ':things_href' => Url::fromRoute('schemadotorg_report.types.things')->toString(),
      ];
      $description_top = $this->t('The schemas are a set of <a href=":types_href">types</a>, each associated with a set of <a href=":properties_href">properties</a>.', $t_args);
      $description_top .= ' ' . $this->t('The types are arranged in a <a href=":things_href">hierarchy</a>.', $t_args);
    }
    else {
      $description_top = $this->t("The schemas are a set of 'types', each associated with a set of properties.");
      $description_top .= ' ' . $this->t('The types are arranged in a hierarchy.');
    }
    $form['description'] = ['#markup' => $description_top];

    // Find.
    $t_args = ['@label' => $this->t('type')];
    $form['find'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['container-inline']],
    ];
    $form['find']['type'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Find a @label', $t_args),
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('Find a Schema.org @label', $t_args),
      '#size' => 30,
      '#required' => TRUE,
      '#target_type' => 'Thing',
      '#action' => Url::fromRoute('<current>')->toString() . '?type=',
    ];
    $form['find']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Find'),
      '#validate' => [static::class . '::validateTypeForm'],
      '#submit' => [static::class . '::submitTypeForm'],
    ];

    // Description bottom.
    // Display recommended Schema.org types.
    $entity_type_id = $entity_type_id ?? 'node';
    $mapping_type = $this->loadMappingType($entity_type_id);
    $recommended_types = $mapping_type->getRecommendedSchemaTypes();
    $items = [];
    foreach ($recommended_types as $group_name => $group) {
      $item = [];
      $item['group'] = [
        '#markup' => $group['label'],
        '#prefix' => '<strong>',
        '#suffix' => ':</strong> ',
      ];
      foreach ($group['types'] as $type) {
        $item[$type] = $this->buildSchemaTypeItem($entity_type_id, $type)
          + ['#prefix' => (count($item) > 1) ? ', ' : ''];
      }
      $items[$group_name] = $item;
    }
    $form['description_bottom'] = [
      'intro' => ['#markup' => '<p>' . $this->t('Or you can jump directly to a commonly used type:') . '</p>'],
    ];
    if (isset($items['quick_start'])) {
      $form['description_bottom']['quick_start'] = [
        '#theme' => 'item_list',
        '#items' => [$items['quick_start']],
        '#prefix' => '<div class="schemadotorg-ui-quick-start">',
        '#suffix' => '</div>',
      ];
      unset($items['quick_start']);
    }
    $form['description_bottom']['items'] = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];

    // Types tree.
    $tree = $this->schemaTypeManager->getTypeTree('Thing');
    $base_path = Url::fromRoute('<current>', [], ['query' => ['type' => '']])->setAbsolute()->toString();
    $form['types'] = [
      '#type' => 'details',
      '#title' => $this->t('Full list of Schema.org types'),
      'tree' => $this->schemaTypeBuilder->buildTypeTree($tree, ['base_path' => $base_path]),
    ];

    $form['#attached']['library'][] = 'schemadotorg_ui/schemadotorg_ui';

    return $form;
  }

  /**
   * Validate the Schema.org type.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function validateTypeForm(array &$form, FormStateInterface $form_state): void {
    $type = $form_state->getValue('type');
    /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
    $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
    if (!$schema_type_manager->isThing($type)) {
      $t_args = ['%type' => $type];
      $form_state->setErrorByName('type', t("The Schema.org type %type is not valid.", $t_args));
    }
  }

  /**
   * Submit the Schema.org type.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function submitTypeForm(array &$form, FormStateInterface $form_state): void {
    $type = $form_state->getValue('type');
    $form_state->setRedirect('<current>', [], ['query' => ['type' => $type]]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Do nothing because the 'submit' callback is already handled.
    // @see \Drupal\schemadotorg_ui\Form\SchemaDotOrgUiTypeSelectForm::submitTypeForm
  }

  /**
   * Build Schema.org type item to be displayed in comma or hierarchical lists.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   A renderable array containing the Schema.org type item.
   */
  protected function buildSchemaTypeItem(string $entity_type_id, string $schema_type): array {
    $is_mapped = $this->getMappingStorage()->isSchemaTypeMapped($entity_type_id, $schema_type);
    $supports_multiple = $this->loadMappingType($entity_type_id)->supportsMultiple();
    if ($is_mapped && !$supports_multiple) {
      return ['#markup' => $schema_type];
    }
    else {
      return [
        '#type' => 'link',
        '#title' => $schema_type,
        '#url' => Url::fromRoute('<current>', [], ['query' => ['type' => $schema_type]]),
      ];
    }
  }

}
