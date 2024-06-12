<?php

namespace Drupal\schemadotorg_jsonapi_preview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg_jsonapi_preview\SchemaDotOrgJsonApiPreviewBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Schema.org JSON:API preview.
 */
class SchemaDotOrgJsonApiPreviewController extends ControllerBase {

  /**
   * The Schema.org JSON-LD preview builder.
   */
  protected SchemaDotOrgJsonApiPreviewBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->builder = $container->get('schemadotorg_jsonapi_preview.builder');
    return $instance;
  }

  /**
   * Builds the response containing the Schema.org JSON:API preview.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return array
   *   A renderable array containing the the Schema.org JSON:API preview.
   */
  public function index(NodeInterface $node): array {
    return $this->builder->build($node);
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
