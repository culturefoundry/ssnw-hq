<?php

namespace Drupal\content_model_documentation\Controller;

use Drupal\content_model_documentation\EntityBundleId;
use Drupal\content_model_documentation\MermaidTrait;
use Drupal\content_model_documentation\RelatedEntities;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Implements EntityDiagramController class.
 */
class EntityDiagramController extends ControllerBase {
  use MermaidTrait;

  /**
   * Drupal\Core\Entity\EntityFieldManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Drupal\Core\Entity\EntityTypeBundleInfoInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

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
   * Entities that are related to the selected entity.
   *
   * @var \Drupal\content_model_documentation\RelatedEntities
   */
  protected $relatedEntities;

  /**
   * Constructs the EntityDiagramController.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The EntityFieldManagerInterface service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The EntityTypeBundleInfoInterface service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The EntityFieldManagerInterface service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The Renderer service.
   * @param Symfony\Component\HttpFoundation\RequestStack $request
   *   The Request service.
   */
  public function __construct(EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer, RequestStack $request) {
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->request = $request->getCurrentRequest();
    $this->relatedEntities = new RelatedEntities($entity_field_manager, $entity_type_bundle_info, $entity_type_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('request_stack')
    );
  }

  /**
   * Display /admin/structure/cm_document/diagram/{entity}/{bundle} page.
   *
   * @return array
   *   Return build array.
   */
  public function display($entity, $bundle) {
    $max_depth = $this->request->get('max_depth', 2);
    $markdown = '';
    $relations = [];

    if ($entity && $bundle) {
      $start = new EntityBundleId($entity, $bundle);
      $relations = $this->relatedEntities->getRelations($start, $max_depth);
      $markdown = $this->flowchart($start, $relations);
    }

    $entities = [];
    foreach ($relations as $relation) {
      $entity_id = $relation->getSource()->entityId;
      $entities[$entity_id] = $entity_id;
      $entity_id = $relation->getDest()->entityId;
      $entities[$entity_id] = $entity_id;
    }

    $form = $this->formBuilder()->getForm('Drupal\content_model_documentation\Form\EntityDiagramForm', $entity, $bundle, $max_depth);
    $key = $this->key($entities);
    return [
      [
        '#theme' => 'mermaid_diagram',
        '#mermaid' => $markdown,
        '#title' => $this->getTitle($entity, $bundle),
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
   * @param string $entity
   *   The Entity id.
   * @param string $bundle
   *   The Bundle id.
   *
   * @return string
   *   Page title.
   */
  public function getTitle($entity, $bundle): string {
    $entityLabel = '';
    $bundleLabel = '';
    if ($entity && $bundle) {
      if ($entityDefinition = $this->entityTypeManager()->getDefinition($entity)) {
        $entityLabel = strval($entityDefinition->getLabel());
      }
      if ($bundleInfo = $this->entityTypeBundleInfo->getBundleInfo($entity)) {
        $bundleLabel = $bundleInfo[$bundle]['label'];
      }
    }
    if (empty($entityLabel) && empty($bundleLabel)) {
      return 'Entity Relationship Diagram';
    }
    elseif ($bundleLabel === $entityLabel) {
      // Remove redundancy.
      return trim("{$bundleLabel} Relationship Diagram");
    }
    else {
      return trim("{$bundleLabel} {$entityLabel} Relationship Diagram");
    }

  }

  /**
   * Generate mermaid markdown for relelationships.
   *
   * @param string $start
   *   The starting bundle.
   * @param array $relation_list
   *   Array of relationships.
   *
   * @return string
   *   Mermaid markdown.
   */
  protected function flowchart(string $start, array $relation_list): string {
    $classed = [];
    $output = "flowchart TD\n";

    if (!empty($relation_list)) {
      /** @var \Drupal\content_model_documentation\EntityRelation $relation */
      foreach ($relation_list as $relation) {
        $source_node_id = (string) $relation->getSource();
        $dest_node_id = (string) $relation->getDest();

        if (!in_array($source_node_id, $classed)) {
          $output .= $this->diagramNode($relation->getSource());
          $classed[] = $source_node_id;
        }
        if (!in_array($dest_node_id, $classed)) {
          $output .= $this->diagramNode($relation->getDest());
          $classed[] = $dest_node_id;
        }
        $output .= $this->makeArrow($source_node_id, $dest_node_id, $relation->getField());
      }
      // Highlight the starting bundle selected by the form.
      $start_name = (string) $start;
      $output .= $this->highlightShape($start_name);
    }
    else {
      $output .= 'empty(No relations found)';
    }
    return $output;
  }

  /**
   * Returns array of entity IDs mapped to mermaid shape markup.
   *
   * @todo Use settings here to assign entity types to shapes.
   *
   * @return array
   *   Array of arrays of mermaid markup.
   */
  protected function getMapping() {
    $mapping = [];
    $itemNo = 0;
    foreach (array_keys($this->relatedEntities->entityTypes()) as $entityId) {
      $shape_names = array_keys($this->getShapes());
      $mapping[$entityId] = current(array_slice($shape_names, $itemNo % count($shape_names), 1, FALSE));
      $itemNo++;
    }
    return $mapping;
  }

  /**
   * Returns mermaid 'key' of entity names and their shapes.
   *
   * @param array $entity_ids
   *   Entity ids of entities to include in key.
   *
   * @return string
   *   Mermaid md to show entity names and shapes.
   */
  protected function key(array $entity_ids): string {
    if (empty($entity_ids)) {
      return '';
    }
    $entityTypes = $this->entityTypeManager()->getDefinitions();
    $output = "flowchart LR\n";
    $entities = array_intersect_key($this->getMapping(), $entity_ids);
    $subgraph_content = '';
    foreach ($entities as $entity_id => $shape) {
      $label = strval($entityTypes[$entity_id]->getLabel());
      $subgraph_content .= "  {$entity_id}{$this->wrapMermaidShape($label, $shape)}\n";
    }
    $output .= $this->wrapSubgraph('Entity types', $subgraph_content, TRUE);
    return $output;
  }

  /**
   * Returns mermaid to add labels and links to mermaid 'nodes'.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $entity_bundle
   *   The entity id and bundle id to use.
   *
   * @return string
   *   Mermaid md to create label and link if appropriate.
   */
  protected function diagramNode(EntityBundleId $entity_bundle) {
    $entity_id = $entity_bundle->entityId;
    $bundle_id = $entity_bundle->bundleId;

    $info = $this->entityTypeBundleInfo->getBundleInfo($entity_id);
    $label = str_replace(['(', ')'], '', $info[$bundle_id]['label']);
    $mapping = $this->getMapping();
    $node_content = "{$label}<br>{$entity_id}";
    $bundle = (string) $entity_bundle;
    $node = "  {$bundle}{$this->wrapMermaidShape($node_content, $mapping[$entity_id])}\n";

    // Add a link if the bundle has relationships.
    if (count($this->relatedEntities->getRelations($entity_bundle, 1)) > 1) {
      $node_label = (string) $entity_bundle;
      $link = Url::fromRoute('entity.content_model_documentation.diagram', [
        'entity' => $entity_id,
        'bundle' => $bundle_id,
      ])->toString();
      $node .= "click $node_label \"$link\"\n";
    }
    return $node;
  }

}
