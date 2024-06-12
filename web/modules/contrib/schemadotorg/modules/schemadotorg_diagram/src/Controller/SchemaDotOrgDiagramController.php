<?php

namespace Drupal\schemadotorg_diagram\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg_diagram\SchemaDotOrgDiagramInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Schema.org Diagram.
 */
class SchemaDotOrgDiagramController extends ControllerBase {

  /**
   * The Schema.org Diagram service.
   */
  protected SchemaDotOrgDiagramInterface $schemaDiagram;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaDiagram = $container->get('schemadotorg_diagram');
    return $instance;
  }

  /**
   * Builds the response containing the Schema.org diagrams.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return array
   *   A renderable array containing the the Schema.org diagrams.
   */
  public function index(NodeInterface $node): array {
    return $this->schemaDiagram->buildDiagrams($node);
  }

  /**
   * Get the node's title.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return string
   *   The node's title.
   */
  public function getTitle(NodeInterface $node): string {
    return $node->label();
  }

}
