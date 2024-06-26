<?php

namespace Drupal\content_model_documentation\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\workflows\Entity\Workflow;
use Drupal\workflows\WorkflowTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements WorkflowDiagramForm class.
 */
class WorkflowDiagramForm extends FormBase {

  use StringTranslationTrait;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * WorkflowTypeManager.
   *
   * @var Drupal\workflows\WorkflowTypeManager
   */
  protected $workFlowTypeManager;

  /**
   * Constructs the WorkflowDiagramController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The EntityTypeManagerInterface service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route matcher service.
   * @param \Drupal\workflows\WorkflowTypeManager|null $workflow_type_manager
   *   WorkflowType manager.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    RouteMatchInterface $route_match,
    WorkflowTypeManager|null $workflow_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->routeMatch = $route_match;
    $this->workFlowTypeManager = $workflow_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $module_handler = $container->get('module_handler');
    $workflow_type_manager = ($module_handler->moduleExists('workflows')) ? $container->get('plugin.manager.workflows.type') : NULL;
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_route_match'),
      $workflow_type_manager
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'content_model_documentation_workflow_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $request = $this->getRequest();
    // Gets default values when form or update is requested from controller.
    $args = $form_state->getBuildInfo()['args'];

    $workflow_types = $this->getWorkflowTypes();
    $workflow_type = $form_state->getValue('workflow_type') ?? $args[0] ?? $request->get('workflow_type');
    // Choose the only choice if there is only one choice.
    if (empty($workflow_type) && count($workflow_types) === 1) {
      $workflow_type = array_key_first($workflow_types);
    }
    $form['workflow_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Workflow Type'),
      '#description' => $this->t('Select workflow type'),
      '#options' => $workflow_types,
      '#required' => TRUE,
      '#ajax' => [
        'method' => 'replaceWith',
        'callback' => '::workflowOptionsCallback',
        'disable-refocus' => TRUE,
        'event' => 'change',
        'wrapper' => 'workflows_list',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading workflow options ...'),
        ],
      ],
      '#default_value' => $workflow_type,
    ];

    // Get bundle from form state or url or default to first in list.
    $workflow_id = $form_state->getValue('workflow') ?? $args[1] ?? $request->get('workflow');
    $workflows = $this->getWorkflows($workflow_type);
    // Choose the only choice if there is only one choice.
    if (empty($workflow_id) && count($workflows) === 1) {
      $workflow_id = array_key_first($workflows);
    }
    $form['workflow'] = [
      '#type' => 'select',
      '#title' => $this->t('Workflow'),
      '#description' => $this->t('Select workflow'),
      '#options' => $workflows,
      '#prefix' => '<div id="workflows_list">',
      '#suffix' => '</div>',
      '#required' => TRUE,
      '#ajax' => [
        'method' => 'replaceWith',
        'callback' => '::transitionOptionsCallback',
        'disable-refocus' => TRUE,
        'event' => 'change',
        'wrapper' => 'transitions_list',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading transition options ...'),
        ],
      ],
      '#default_value' => $workflow_id,
    ];

    // If no transition is selected, treat it like ALL.
    $transition = $form_state->getValue('transition') ?? $args[2] ?? $request->get('transition') ?? 'all';
    $transitions = $this->getTransitions($workflow_type, $workflow_id);
    $form['transition'] = [
      '#type' => 'select',
      '#title' => $this->t('Transition'),
      '#description' => $this->t('Select transition.'),
      '#options' => $transitions,
      '#default_value' => $transition,
      '#prefix' => '<div id="transitions_list">',
      '#suffix' => '</div>',
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
    $form_state->setRedirect('entity.content_model_documentation.workflow_diagram', [
      'workflow_type' => $form_state->getValue('workflow_type'),
      'workflow' => $form_state->getValue('workflow'),
      'transition' => $form_state->getValue('transition'),
    ]);
  }

  /**
   * Callback to populate workflow based on selected workflow type.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Its state.
   *
   * @return array
   *   The workflow form element.
   */
  public function workflowOptionsCallback(array &$form, FormStateInterface $form_state): array {
    $form['workflow']['#options'] = $this->getWorkflows($form_state->getValue('workflow_type'));
    return $form['workflow'];
  }

  /**
   * Callback to populate transtions based on selected workflow.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Its state.
   *
   * @return array
   *   The transition form element.
   */
  public function transitionOptionsCallback(array &$form, FormStateInterface $form_state): array {
    $form['transition']['#options'] = $this->getTransitions($form_state->getValue('workflow_type'), $form_state->getValue('workflow'));
    return $form['transition'];
  }

  /**
   * Get all the workflow types that are part of the site.
   *
   * @return array
   *   An array of workflow types.
   */
  protected function getWorkflowTypes(): array {
    $types = [];
    if ($this->workFlowTypeManager) {
      $workflow_types = $this->workFlowTypeManager->getDefinitions();
      foreach ($workflow_types as $workflow_type) {
        $types[$workflow_type['id']] = (string) $workflow_type['label'];
      }
    }
    asort($types);

    return $types;
  }

  /**
   * Get all the workflows that are part of a workflow type.
   *
   * @param string $workflow_type
   *   The workflow type to lookup.
   *
   * @return array
   *   An array of workflows that are of the specific type.
   */
  protected function getWorkflows($workflow_type): array {
    $flows = [];
    $workflows = Workflow::loadMultipleByType($workflow_type);
    foreach ($workflows as $workflow_id => $workflow) {
      $flows[$workflow->id()] = $workflow->label();
    }
    asort($flows);

    return $flows;
  }

  /**
   * Get all the transitions that are part of a workflow.
   *
   * @param string $workflow_type
   *   The workflow type to lookup.
   * @param string $workflow_id
   *   The workflow id to lookup.
   *
   * @return array
   *   An array of transitions that are part of the workflow
   */
  protected function getTransitions($workflow_type, $workflow_id): array {
    $transitions = [];
    $workflows = Workflow::loadMultipleByType($workflow_type);
    $workflow = $workflows[$workflow_id] ?? NULL;
    if ($workflow) {
      $transition_list = $workflow->getTypePlugin()->getTransitions();
      foreach ($transition_list as $transition) {
        $transitions[$transition->id()] = $transition->label();
      }
    }
    asort($transitions);
    $first = ['all' => 'All'];
    $transitions = $first + $transitions;

    return $transitions;
  }

}
