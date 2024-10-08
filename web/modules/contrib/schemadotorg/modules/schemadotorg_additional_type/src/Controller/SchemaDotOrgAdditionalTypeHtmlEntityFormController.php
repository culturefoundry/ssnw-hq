<?php

namespace Drupal\schemadotorg_additional_type\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\FormController;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Schema.org additional type node form controller.
 */
class SchemaDotOrgAdditionalTypeHtmlEntityFormController extends FormController {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a MercuryEditorHtmlEntityFormController object.
   *
   * @param \Drupal\Core\Controller\FormController $entityFormController
   *   The entity form controller being decorated.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected FormController $entityFormController,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * Renders the Schema.org additional type selection page or the entity edit form.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   The render array that results from invoking the controller or a response.
   */
  public function getContentResult(Request $request, RouteMatchInterface $route_match): array|Response {
    switch ($route_match->getRouteName()) {
      case 'node.add':
      case 'entity.node.edit_form';
        return $this->buildTypeSelect($request, $route_match)
          ?? $this->buildNodeForm($request, $route_match);

      default:
        return $this->entityFormController->getContentResult($request, $route_match);
    }
  }

  /**
   * Build the Schema.org additional type selection page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object.
   *
   * @return array|null
   *   A renderable array containing links for selecting
   *   a node's additional type.
   */
  protected function buildTypeSelect(Request $request, RouteMatchInterface $route_match): ?array {
    $mapping = $this->getMappingFromRouteMatch($route_match);
    if (!$mapping || !$this->isAdditionalTypeRequired($mapping)) {
      return NULL;
    }

    $allowed_values = $this->getMappingAdditionalTypeAllowedValues($mapping);
    if (!$allowed_values) {
      return NULL;
    }

    $field_name = $mapping->getSchemaPropertyFieldName('additionalType');
    $value = $request->query->get($field_name);

    // Validate the additional type query parameter for a node add/edit form.
    switch ($route_match->getRouteName()) {
      case 'node.add':
        if ($value && isset($allowed_values[$value])) {
          return NULL;
        }
        break;

      case 'entity.node.edit_form':
        if (is_null($value) || isset($allowed_values[$value])) {
          return NULL;
        }
        break;
    }

    $links = [];
    $query = $this->getRequestQuery($request);
    foreach ($allowed_values as $value => $text) {
      $links[] = [
        'url' => Url::fromRouteMatch($route_match)->setOption('query', [$field_name => $value] + $query),
        'title' => $text,
      ];
    }

    $t_args = [
      '%label' => $mapping->getTargetEntityBundleEntity()->label(),
      '@action' => ($route_match->getRouteName() === 'node.add')
        ? $this->t('create')
        : $this->t('change to'),
    ];
    return [
      '#theme' => 'links',
      '#title' => $this->t('Please select the %label type you want to @action.', $t_args),
      '#links' => $links,
      '#prefix' => '<br/>',
      '#suffix' => '<br/>',
      '#cache' => [
        'contexts' => ['url.query_args:' . $field_name],
      ],
    ];
  }

  /**
   * Builds the node add/edit form with additional type enhancements.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   A renderable array containing the node add/edit form or a response
   */
  protected function buildNodeForm(Request $request, RouteMatchInterface $route_match): array|Response {
    /** @var array|\Symfony\Component\HttpFoundation\Response $result */
    $result = $this->entityFormController->getContentResult($request, $route_match);
    if ($result instanceof Response) {
      return $result;
    }

    $form = $result;

    $mapping = $this->getMappingFromRouteMatch($route_match);
    if (!$mapping || !$this->isAdditionalTypeRequired($mapping)) {
      return $form;
    }

    $allowed_values = $this->getMappingAdditionalTypeAllowedValues($mapping);
    if (!$allowed_values) {
      return $form;
    }

    $field_name = $mapping->getSchemaPropertyFieldName('additionalType');
    $value = ($route_match->getRouteName() === 'node.add')
      ? $request->query->get($field_name)
      : $form[$field_name]['widget']['#default_value'][0] ?? NULL;
    if (!$value) {
      return $form;
    }

    // Hide the additional type widget.
    $form[$field_name]['widget']['#access'] = FALSE;

    // Alter the form based on the form type.
    switch ($route_match->getRouteName()) {
      case 'node.add':
        // Update the node add form's title.
        $form['#title'] = $this->t('Create @name', ['@name' => $allowed_values[$value]]);
        break;
    }

    $query = [$field_name => ''] + $this->getRequestQuery($request);

    // Display the additional type value with a modal link to change it.
    $form[$field_name]['schemadotorg_additional_type'] = [
      '#type' => 'item',
      '#title' => $form[$field_name]['widget']['#title'] ?? $this->t('Type'),
      'value' => [
        '#markup' => $allowed_values[$value],
        '#suffix' => ' ',
      ],
      'change' => [
        '#type' => 'link',
        '#title' => $this->t('Change type'),
        '#url' => Url::fromRouteMatch($route_match)->setOption('query', $query),
        '#attributes' => [
          'class' => ['use-ajax', 'button', 'button--small', 'button--extrasmall'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode(['width' => 600]),
        ],
        '#attached' => [
          'library' => ['core/drupal.dialog.ajax'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * Get the Schema.org mapping associated with the current route match.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null
   *   A Schema.org mapping or NULL if not found.
   */
  protected function getMappingFromRouteMatch(RouteMatchInterface $route_match): ?SchemaDotOrgMappingInterface {
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $route_match->getParameter('node_type')
      ?? $route_match->getParameter('node')
      ?? NULL;

    if ($entity instanceof NodeTypeInterface) {
      $bundle = $entity->id();
    }
    elseif ($entity instanceof NodeInterface) {
      $bundle = $entity->bundle();
    }
    else {
      return NULL;
    }

    return $this->getMappingStorage()->loadByBundle('node', $bundle);
  }

  /**
   * Check if additional type is required for a given Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return bool
   *   Returns TRUE if additional type is required, FALSE otherwise.
   */
  protected function isAdditionalTypeRequired(SchemaDotOrgMappingInterface $mapping): bool {
    $required_types = $this->configFactory
      ->get('schemadotorg_additional_type.settings')
      ->get('required_types');
    return (bool) $this->schemaTypeManager->getSetting($required_types, $mapping);
  }

  /**
   * Get the allowed values for the additional type field of a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping object.
   *
   * @return array|null
   *   An array of allowed values for the additional type field,
   *   or NULL if the field is not found.
   */
  protected function getMappingAdditionalTypeAllowedValues(SchemaDotOrgMappingInterface $mapping): ?array {
    $field_name = $mapping->getSchemaPropertyFieldName('additionalType');
    if (!$field_name) {
      return NULL;
    }

    /** @var \Drupal\field\FieldStorageConfigInterface|null $field_storage_config */
    $field_storage_config = $this->entityTypeManager->getStorage('field_storage_config')
      ->load("node.$field_name");
    if (!$field_storage_config) {
      return NULL;
    }

    return options_allowed_values($field_storage_config) ?: NULL;
  }

  /**
   * Get the current request's query parameters.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   The current request's query parameters.
   */
  protected function getRequestQuery(Request $request): array {
    $query = $request->query->all();
    unset($query[MainContentViewSubscriber::WRAPPER_FORMAT]);
    return $query;
  }

  /* ************************************************************************ */
  // Implement required methods for this form controller.
  /* ************************************************************************ */

  /**
   * {@inheritdoc}
   */
  protected function getFormArgument(RouteMatchInterface $route_match) {
    return $this->entityFormController->getFormArgument($route_match);
  }

  /**
   * {@inheritdoc}
   */
  protected function getFormObject(RouteMatchInterface $route_match, $form_arg) {
    return $this->entityFormController->getFormObject($route_match, $form_arg);
  }

}
