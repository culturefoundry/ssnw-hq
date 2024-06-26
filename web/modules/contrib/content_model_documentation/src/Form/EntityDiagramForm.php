<?php

namespace Drupal\content_model_documentation\Form;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements EntityDiagramForm class.
 */
class EntityDiagramForm extends FormBase {

  /**
   * Drupal\Core\Entity\EntityFieldManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\EntityTypeBundleInfoInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Constructs the EntityDiagramController.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The EntityFieldManagerInterface service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The EntityFieldManagerInterface service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The EntityTypeBundleInfoInterface service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route matcher service.
   */
  public function __construct(EntityFieldManagerInterface $entity_field_manager, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, RouteMatchInterface $route_match) {
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'content_model_documentation_diagram_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $request = $this->getRequest();

    // @todo Load these from settings so only admin selected entity types show.
    // @todo This code is duplicated here and in RelatedEntities::getEntities.
    $entity_types = [];
    foreach ($this->entityTypeManager->getDefinitions() as $id => $type) {
      if ($type->entityClassImplements(FieldableEntityInterface::class)) {
        $entity_types[$id] = $type->getLabel();
      }
    }
    asort($entity_types);

    // Gets default values when form or update is requested from controller.
    $args = $form_state->getBuildInfo()['args'];

    // Get entity from form state or url or default to first in list.
    /*
     * @todo This mess is an attempt to get the right value for both callback
     * and link. May be a futile effort.
     */
    $entity_id = $form_state->getValue('entity_id') ?? $args[0] ?? $request->get('entity');
    if (empty($entity_id)) {
      $entity_ids = array_keys($entity_types);
      $entity_id = reset($entity_ids);
    }

    $form['entity_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity'),
      '#description' => $this->t('Select starting entity'),
      '#options' => $entity_types,
      '#ajax' => [
        'callback' => '::bundleOptionsCallback',
    // Or TRUE to prevent re-focusing on the triggering element.
        'disable-refocus' => TRUE,
        'event' => 'change',
        'wrapper' => 'bundles',
      ],
      '#default_value' => $entity_id,
    ];

    // Get bundle from form state or url or default to first in list.
    $bundle_id = $form_state->getValue('bundle_id') ?? $args[1] ?? $request->get('bundle');
    $bundles = $this->getBundleOptions($entity_id);
    if (empty($bundle_id) || !in_array($bundle_id, array_keys($bundles))) {
      $bundle_ids = array_keys($bundles);
      $bundle_id = reset($bundle_ids);
    }

    $form['bundle_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Bundle'),
      '#description' => $this->t('Select starting bundle'),
      '#options' => $bundles,
      '#prefix' => '<div id="bundles">',
      '#suffix' => '</div>',
      '#default_value' => $bundle_id,
    ];

    $form['max_depth'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum depth'),
      '#description' => $this->t('How many degrees of separation to show'),
      '#default_value' => $request->get('max_depth') ?? $args[2] ?? 2,
      '#attributes' => ['min' => 0],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#prefix' => '<div id="submit_button">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo Validate fields.
    $form_state->setRedirect('entity.content_model_documentation.diagram', [
      'entity' => $form_state->getValue('entity_id'),
      'bundle' => $form_state->getValue('bundle_id'),
      'max_depth' => $form_state->getValue('max_depth'),
    ]);
  }

  /**
   * Callback to populate bundles based on selected entity.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Its state.
   *
   * @return array
   *   The bundle form element.
   */
  public function bundleOptionsCallback(array &$form, FormStateInterface $form_state): array {
    $form['bundle_id']['#options'] = $this->getBundleOptions($form_state->getValue('entity_id'));
    return $form['bundle_id'];
  }

  /**
   * Returns a list of an entity's bundles.
   *
   * @param string $entity_id
   *   Tne entity to get bundles for.
   *
   * @return array
   *   Bundle labels keyed on bundle ids.
   */
  protected function getBundleOptions(string $entity_id): array {
    $options = [];

    $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_id);

    foreach ($bundles as $bundle_id => $values) {
      $options[$bundle_id] = $values['label'];
    }
    asort($options);

    return $options;
  }

}
