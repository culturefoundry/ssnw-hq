<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_content_model_documentation;

use Drupal\Component\Serialization\Json;
use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\NodeTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Schema.org Content Model Documentation builder service.
 */
class SchemaDotOrgContentModelDocumentationBuilder implements SchemaDotOrgContentModelDocumentationBuilderInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgContentModelDocumentationBuilder object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg_content_model_documentation\SchemaDotOrgContentModelDocumentationManagerInterface $documentationManager
   *   THe Schema.org documentation manager.
   */
  public function __construct(
    protected RequestStack $requestStack,
    protected AccountProxyInterface $currentUser,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgContentModelDocumentationManagerInterface $documentationManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function cmDocumentFormAlter(array &$form, FormStateInterface &$form_state, string $form_id): void {
    // Append a note to the name field's description.
    $form['name']['widget'][0]['value']['#description'] .= ' ' . $this->t('For Schema.org mapping documentation, the name must be the absolute URL of the Schema.org type that is being documented. (i.e., https://schema.org/SpecialAnnouncement)');
  }

  /**
   * {@inheritdoc}
   */
  public function cmDocumentViewAlter(array &$build, CMDocumentInterface $cm_document, EntityViewDisplayInterface $display): void {
    $is_modal = $this->isModal();

    // Move 'Fields that appear on ...' table into a details widget.
    // Add access control to view 'Fields that appear on ...'.
    // @see \Drupal\content_model_documentation\CmDocumentViewBuilder::getFieldsOnEntity
    $documented_entity = $cm_document->getDocumentedEntity();
    if (isset($build['FieldsOnEntity']['table'])
      && $documented_entity instanceof ConfigEntityBundleBase) {
      $bundle_of = $documented_entity->getEntityType()->getBundleOf();
      $build['FieldsOnEntity'] += [
        '#type' => 'details',
        '#open' => !$is_modal,
        '#title' => $build['FieldsOnEntity']['table']['#caption'],
        '#access' => $this->currentUser->hasPermission('administer ' . $bundle_of . ' fields'),
      ];
      unset(
        $build['FieldsOnEntity']['table']['#caption'],
        $build['FieldsOnEntity']['table']['#attributes'],
        $build['FieldsOnEntity']['table']['#attached'],
      );
    }

    if ($is_modal) {
      // Open documentation links within the modal.
      $build['#attached']['library'][] = 'schemadotorg_content_model_documentation/schemadotorg_content_model_documentation.dialog';

      // Add 'Open in new tab' button to modal dialogs.
      $documentation_link = $cm_document->toLink(
        $this->t('Open in new tab'),
        'canonical',
        [
          'attributes' => [
            'target' => '_blank',
            'class' => ['button', 'button-small', 'button--extrasmall'],
          ],
        ]
      )->toRenderable();
      $build['schemadotorg_content_model_documentation_open_tab'] = $documentation_link + [
          '#weight' => 21,
          '#prefix' => '<p>',
          '#suffix' => '</p>',
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fieldWidgetSingleElementMarkupFormAlter(array &$element, FormStateInterface $form_state, array $context): void {
    if (!$this->documentationManager->openLinksInModal()) {
      return;
    }

    // Check that this markup field is being used for content model documentation.
    // (i.e., The field name is 'schema_cm_documentation')
    /** @var \Drupal\markup\Field\MarkupItemList $items */
    $items = $context['items'];
    $cm_documentation_field_name = $this->documentationManager->getFieldName();
    if ($cm_documentation_field_name !== $items->getFieldDefinition()->getName()) {
      return;
    }

    // Add the modal attributes to the link.
    $attributes = new Attribute($this->getLinkModalAttributes());
    $element['markup'] = [
      '#markup' => str_replace('<a', '<a' . $attributes . ' ', $element['markup']['#text']),
      '#attached' => ['library' => ['core/drupal.dialog']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function entityOperation(EntityInterface $entity): ?array {
    // Add 'Relationships' operation to Schema.org mappings which links to
    // an 'Entity Relationship Diagram' provided by the
    // Content Model Documentation module.
    // @see https://www.drupal.org/project/content_model_documentation
    if ($entity instanceof SchemaDotOrgMappingInterface) {
      $entity_type_id = $entity->getTargetEntityTypeId();
      $bundle = $entity->getTargetBundle();

      $operations = [];
      $operations['content_model_documentation'] = [
        'title' => $this->t('Relationships'),
        'url' => Url::fromRoute(
          'entity.content_model_documentation.diagram',
          ['entity' => $entity_type_id, 'bundle' => $bundle],
          ['query' => ['max_depth' => '1']],
        ),
        'weight' => 50,
      ];
      return $operations;
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function help(string $route_name, RouteMatchInterface $route_match): array|NULL {
    $link_text = $this->documentationManager->getLinkText();
    if (!$link_text) {
      return NULL;
    }

    $node_type = $this->getNodeTypeFromRouteMatch($route_match);
    if (!$node_type) {
      return NULL;
    }

    /** @var \Drupal\content_model_documentation\CMDocumentStorageInterface $cm_document_storage */
    $cm_document_storage = $this->entityTypeManager->getStorage('cm_document');
    /** @var \Drupal\content_model_documentation\Entity\CMDocument[] $cm_documents */
    $cm_documents = $cm_document_storage->loadByProperties(['documented_entity' => 'node.' . $node_type->id()]);
    $cm_document = $cm_documents ? reset($cm_documents) : NULL;
    if (!$cm_document || !$cm_document->access('view')) {
      return NULL;
    }

    // Build the content model documentation link.
    $link = $cm_document->toLink($link_text)->toRenderable();
    $link['#attributes'] = ['target' => '_blank'];
    $link['#prefix'] = ' ';

    // Check that open content model documentation in a modal is enabled.
    if ($this->documentationManager->openLinksInModal()) {
      $link['#attributes'] += $this->getLinkModalAttributes();
      $link['#attached'] = ['library' => ['core/drupal.dialog']];
    }

    return $link;
  }

  /**
   * {@inheritdoc}
   */
  public function preprocessBlock(array &$variables): void {
    if ($variables['base_plugin_id'] !== 'help_block') {
      return;
    }

    $node_type = $this->getNodeTypeFromRouteMatch();
    if (!$node_type) {
      return;
    }

    // Hide node help content if the Content Model Documentation field exists.
    $field_name = $this->documentationManager->getFieldName();
    if (FieldConfig::loadByName('node', $node_type->id(), $field_name)) {
      $variables['content'] = [];
    };
  }

  /**
   * Determine if the current request is being displayed in a modal.
   *
   * @return bool
   *   TRUE if the current request is being displayed in a modal.
   */
  protected function isModal(): bool {
    return ($this->requestStack->getCurrentRequest()->query->get(MainContentViewSubscriber::WRAPPER_FORMAT) === 'drupal_modal');
  }

  /**
   * Get the link attributes needed to open a modal dialog.
   *
   * @return array
   *   The link attributes needed to open a modal dialog.
   */
  protected function getLinkModalAttributes(): array {
    return [
      'class' => ['use-ajax', 'schemadotorg-content-model-documentation-link'],
      'data-dialog-options' => Json::encode([
        'width' => 1000,
        'classes' => [
          'ui-dialog' => 'schemadotorg-content-model-documentation-ui-dialog',
        ],
      ]),
      'data-dialog-type' => 'modal',
    ];
  }

  /**
   * Get the node type from a route match.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface|null $route_match
   *   A route match.
   *
   * @return \Drupal\node\NodeTypeInterface|null
   *   A node type.
   */
  protected function getNodeTypeFromRouteMatch(?RouteMatchInterface $route_match = NULL): ?NodeTypeInterface {
    $route_match = $route_match ?? $this->routeMatch;

    switch ($route_match->getRouteName()) {
      case 'entity.node.edit_form':
        /** @var \Drupal\node\NodeInterface $node */
        $node = $route_match->getParameter('node');
        /** @var \Drupal\node\NodeTypeInterface $node_type */
        $node_type = $this->entityTypeManager
          ->getStorage('node_type')
          ->load($node->getType());
        return $node_type;

      case 'node.add':
        return $route_match->getParameter('node_type');

      default:
        return NULL;
    }
  }

}
