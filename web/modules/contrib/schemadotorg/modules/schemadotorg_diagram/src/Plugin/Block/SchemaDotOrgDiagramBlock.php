<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_diagram\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg_diagram\SchemaDotOrgDiagramInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Schema.org Diagrams' block.
 *
 * @Block(
 *   id = "schemadotorg_diagram",
 *   admin_label = @Translation("Schema.org Blueprints Diagrams"),
 *   category = @Translation("Schema.org Blueprints")
 * )
 */
final class SchemaDotOrgDiagramBlock extends BlockBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * The current route match.
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * The Schema.org Diagram service.
   */
  protected SchemaDotOrgDiagramInterface $schemaDiagram;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    $instance->schemaDiagram = $container->get('schemadotorg_diagram');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label_display' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): ?array {
    $current_node = $this->getCurrentNode();
    if (!$current_node) {
      return NULL;
    }

    $diagrams = $this->schemaDiagram->buildDiagrams($current_node);
    if (!$diagrams) {
      return NULL;
    }

    return [
      'details' => [
        '#type' => 'details',
        '#title' => $this->t('Schema.org diagrams'),
        '#attributes' => [
          'id' => 'schemadotorg-diagram',
          'data-schemadotorg-details-key' => 'schemadotorg-diagram',
        ],
        '#attached' => ['library' => ['schemadotorg_diagram/schemadotorg_diagram']],
      ] + $diagrams,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'view schemadotorg diagram');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $current_node = $this->getCurrentNode();
    if ($current_node) {
      return Cache::mergeTags(parent::getCacheTags(), $current_node->getCacheTags());
    }
    else {
      return parent::getCacheTags();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

  /**
   * Get the current node for the current route.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The current node for the current route.
   */
  protected function getCurrentNode(): ?NodeInterface {
    $current_node = $this->routeMatch->getParameter('node');
    if ($current_node
      && ($current_node instanceof NodeInterface)
      && node_is_page($current_node)) {
      return $current_node;
    }
    else {
      return NULL;
    }
  }

}
