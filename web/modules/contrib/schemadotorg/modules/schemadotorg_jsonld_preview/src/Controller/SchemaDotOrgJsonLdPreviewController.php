<?php

namespace Drupal\schemadotorg_jsonld_preview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;
use Drupal\schemadotorg_jsonld_preview\SchemaDotOrgJsonLdPreviewBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Schema.org JSON-LD preview.
 */
class SchemaDotOrgJsonLdPreviewController extends ControllerBase {

  /**
   * The Schema.org JSON-LD manager service.
   */
  protected SchemaDotOrgJsonLdManagerInterface $manager;

  /**
   * The Schema.org JSON-LD preview builder.
   */
  protected SchemaDotOrgJsonLdPreviewBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->manager = $container->get('schemadotorg_jsonld.manager');
    $instance->builder = $container->get('schemadotorg_jsonld_preview.builder');
    return $instance;
  }

  /**
   * Builds the response containing the Schema.org JSON-LD preview.
   *
   * @param string $format
   *   The format of the JSON-LD preview.
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return array
   *   A renderable array containing the the Schema.org JSON-LD preview.
   */
  public function index(string $format, NodeInterface $node): array {
    $route_match = $this->manager->getEntityRouteMatch($node);
    return $this->builder->build($format, $route_match);
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
