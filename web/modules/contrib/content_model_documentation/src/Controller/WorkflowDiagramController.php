<?php

namespace Drupal\content_model_documentation\Controller;

use Drupal\content_model_documentation\MermaidTrait;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\workflows\Entity\Workflow;
use Drupal\workflows\StateInterface;
use Drupal\workflows\WorkflowTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Implements WorkflowDiagramController class.
 */
class WorkflowDiagramController extends ControllerBase {
  use MermaidTrait;
  use StringTranslationTrait;

  /**
   * Whether workflow module is enabled.
   *
   * @var bool
   */
  protected $enabled;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * WorkflowTypeManager.
   *
   * @var \Drupal\workflows\WorkflowTypeManager
   */
  protected $workFlowTypeManager;

  /**
   * Constructs the EntityDiagramController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The EntityTypeManagerInterface service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The Renderer service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   The Request service.
   * @param \Drupal\workflows\WorkflowTypeManager|null $workflow_type_manager
   *   Workflow type manager.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    RendererInterface $renderer,
    RequestStack $request,
    WorkflowTypeManager|null $workflow_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->request = $request->getCurrentRequest();
    // Needs to only offer this if workflow is enabled.
    $this->enabled = !empty($workflow_type_manager);
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
      $container->get('renderer'),
      $container->get('request_stack'),
      $workflow_type_manager
    );
  }

  /**
   * Display /admin/reports/content-model/workflow/{workflow_type}/{workflow}/{transition} page.
   *
   * @param string|null $workflow_type
   *   The name of a workflow_type to list (from the url).
   * @param string|null $workflow
   *   The name of a workflow to list (from the url).
   * @param string|null $transition
   *   The transition to highlight (from the url).
   *
   * @return array
   *   Drupal build array.
   */
  public function display($workflow_type, $workflow, $transition): array {
    $markdown = '';
    if (!$this->enabled) {
      return [
        '#theme' => 'mermaid_diagram',
        '#md' => $this->t('Workflow diagrams not available because the Workflows module is not enabled.'),
        '#title' => $this->t('Workflow diagrams unavailable'),
      ];

    }

    if ($workflow_type && $workflow && $transition) {
      $markdown = $this->flowchart($workflow_type, $workflow, $transition);
    }
    else {
      $markdown = "flowchart LR\n";
      $markdown .= "warn" . $this->wrapMermaidShape($this->t('Please make a selection'), 'circle');
    }
    $form = $this->formBuilder()->getForm('Drupal\content_model_documentation\Form\WorkflowDiagramForm', $workflow_type, $workflow, $transition);

    $key = $this->key([
      'trapezoid' => 'status = 0',
      'rectangle' => 'state',
      'flag' => 'status = 1',
    ]);

    return [
      [
        '#theme' => 'mermaid_diagram',
        '#mermaid' => $markdown,
        '#title' => $this->getTitle($workflow_type, $workflow, $transition),
        '#preface' => $this->renderer->render($form),
        '#attached' => [
          'library' => [
            'mermaid_diagram_field/diagram',
            'content_model_documentation/diagram',
          ],
        ],
        '#key' => $key,
        '#show_code' => TRUE,
      ],
    ];
  }

  /**
   * Get the page title given the entity and bundle ids.
   *
   * @param string $workflow_type_id
   *   The workflow type for the page.
   * @param string $workflow_id
   *   The Workflow of the page.
   * @param string $transition_id
   *   The transition id of the page.
   *
   * @return string
   *   Page title.
   */
  public function getTitle($workflow_type_id, $workflow_id, $transition_id): string {
    if (empty($workflow_type_id) || empty($workflow_type_id)) {
      return $this->t('Workflow Diagram');
    }
    $workflow = $this->getWorkflow($workflow_type_id, $workflow_id);
    $workflow_type = $this->getWorkflowType($workflow_type_id);
    $workflow_type_name = $workflow_type['label'];
    $workflow_name = (!empty($workflow)) ? $workflow->label() : '';

    if ($workflow_type_name === $workflow_name) {
      // Remove redundancy.
      return $this->t('Workflow: @workflow_type_name diagram', ['@workflow_type_name' => $workflow_type_name]);
    }
    elseif (!empty($transition_id) && $transition_id !== 'all') {
      $transitions = $this->getWorkflowTransitions($workflow, $transition_id);
      /** @var \Drupal\workflows\Transition $transition*/
      $transition = reset($transitions);
      $transition_name = $transition->label();
      $vars = [
        '@workflow_type_name' => $workflow_type_name,
        '@workflow_name' => $workflow_name,
        '@transition_name' => $transition_name,
      ];
      return $this->t('Workflow: @workflow_type_name: @workflow_name -> @transition_name transition diagram', $vars);
    }
    else {
      $vars = [
        '@workflow_type_name' => $workflow_type_name,
        '@workflow_name' => $workflow_name,
      ];
      return $this->t('Workflow: @workflow_type_name: @workflow_name diagram', $vars);
    }
  }

  /**
   * Generate mermaid markdown workflows.
   *
   * @param string $workflow_type
   *   The workflow type id.
   * @param string $workflow_id
   *   The workflow id.
   * @param string $transition_id
   *   The transition id.
   *
   * @return string
   *   Mermaid markdown.
   */
  protected function flowchart(string $workflow_type, string $workflow_id, string $transition_id): string {
    $workflow = $this->getWorkflow($workflow_type, $workflow_id);
    $states = $this->getWorkflowStates($workflow);
    $transitions = $this->getWorkflowTransitions($workflow, $transition_id);

    if (empty($transition_id) || $transition_id === 'all') {
      // Treat it as ALL and show the fully connected diagram.
      $output = "flowchart LR\n";
      // Build the States.
      foreach ($states as $state_id => $state) {
        $shape = $this->getShapeState($state);
        $output .= "  {$state_id}{$this->wrapMermaidShape($state->label(), $shape)}\n";
      }

      // Build the Transitions.
      foreach ($transitions as $transition_id => $transition) {
        /** @var \Drupal\workflows\Transition $transition */
        $to = $transition->to();
        $to_id = $to->id();
        $froms = $transition->from();
        foreach ($froms as $from) {
          $from_id = $from->id();
          $output .= $this->makeArrow($from_id, $to_id, $transition->label());
        }
      }
    }
    else {
      // The transition is set, build the left-rail right-rail of states
      // and then connect left to right by the transitions.
      $output = "flowchart LR\n";
      // Build the States.
      $subgraph_before = '';
      $subgraph_after = '';
      foreach ($states as $state_id => $state) {
        $shape = $this->getShapeState($state);
        $subgraph_before .= "    {$state_id}_before{$this->wrapMermaidShape($state->label(), $shape)}\n";
        $subgraph_after .= "    {$state_id}_after{$this->wrapMermaidShape($state->label(), $shape)}\n";
      }
      $output .= $this->wrapSubgraph($this->t('Before'), $subgraph_before);
      $output .= $this->wrapSubgraph($this->t('After'), $subgraph_after);

      // Visually join the subgraphs to align the tops.
      $output .= "  {$this->t('Before')} -.- {$this->t('After')}\n";
      // Build the Transitions.
      foreach ($transitions as $transition_id => $transition) {
        /** @var \Drupal\workflows\Transition $transition */
        $to = $transition->to();
        $to_id = $to->id() . '_after';
        $froms = $transition->from();
        foreach ($froms as $from) {
          $from_id = $from->id() . '_before';
          $output .= $this->makeArrow($from_id, $to_id, $transition->label());
        }
      }
    }

    return $output;
  }

  /**
   * Gets a Workflow object.
   *
   * @param string $workflow_type
   *   The workflow type id.
   * @param string $workflow_id
   *   The workflow id.
   *
   * @return \Drupal\workflows\Entity\Workflow|null
   *   The workflow object or null if not found.
   */
  protected function getWorkflow($workflow_type, $workflow_id) {
    $workflows = Workflow::loadMultipleByType($workflow_type);
    return $workflows[$workflow_id] ?? NULL;
  }

  /**
   * Get the workflow states from a workflow.
   *
   * @param Drupal\workflows\Entity\Workflow $workflow
   *   A workflow object.
   *
   * @return array
   *   An array or States.
   */
  protected function getWorkflowStates(Workflow $workflow): array {
    return $workflow->getTypePlugin()->getStates();
  }

  /**
   * Gets a single transition or all.
   *
   * @param Drupal\workflows\Entity\Workflow $workflow
   *   A workflow object.
   * @param string $transition_id
   *   The transition id to load.
   *
   * @return array
   *   An array of Transition(s) or empty array if not found.
   */
  protected function getWorkflowTransitions(Workflow $workflow, $transition_id = 'all'): array {
    $transitions = $workflow->getTypePlugin()->getTransitions();
    if (empty($transition_id) || $transition_id === 'all') {
      return $transitions ?? [];
    }
    else {
      return [$transitions[$transition_id]] ?? [];
    }
  }

  /**
   * Get the shape to use in the diagram.
   *
   * @param Drupal\workflows\StateInterface $state
   *   The State object.
   *
   * @return string
   *   A mermaid shape name to use.
   */
  protected function getShapeState(StateInterface $state): string {
    $published = $this->accessProtected($state, 'published');
    $default = $this->accessProtected($state, 'defaultRevision');
    if ($published) {
      // This is a status = 1 state.
      $shape = 'flag';
    }
    elseif ($default && !$published) {
      // This is a status = 0 and default revision state state.
      $shape = 'trapezoid';
    }
    else {
      $shape = 'rectangle';
    }
    return $shape;
  }

  /**
   * Gets a requsted workflow type.
   *
   * @param string $type_id
   *   A workflow type id.
   *
   * @return array
   *   An array of information about a workflow type.
   */
  protected function getWorkflowType($type_id): array {
    $workflow_types = $this->workFlowTypeManager->getDefinitions();
    return $workflow_types[$type_id] ?? [];
  }

  /**
   * Retrieves a property from a class even if it is protected.
   *
   * Use this only as a last resort.  Grabbing protected things is risky.
   *
   * @param object $object
   *   The haystack that contains needle (lookup_property).
   * @param string $lookup_property
   *   The property to be returned.
   *
   * @return mixed
   *   Whatever is stored in the property of $object.
   */
  protected function accessProtected($object, $lookup_property) {
    if (is_object($object)) {
      $reflection = new \ReflectionClass($object);
      $property = $reflection->getProperty($lookup_property);
      $property->setAccessible(TRUE);
      return $property->getValue($object);
    }
    return NULL;
  }

}
