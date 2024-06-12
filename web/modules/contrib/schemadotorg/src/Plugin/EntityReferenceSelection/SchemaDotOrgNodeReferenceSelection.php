<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Node plugin implementation of the Schema.org Entity Selection plugin.
 *
 * @see \Drupal\node\Plugin\EntityReferenceSelection\NodeSelection
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg:node",
 *   label = @Translation("Schema.org: Filter by Schema.org types"),
 *   entity_types = {"node"},
 *   group = "schemadotorg",
 *   weight = 1,
 * )
 */
class SchemaDotOrgNodeReferenceSelection extends SchemaDotOrgEntityReferenceSelection {


  /**
   * The module handler service.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\node\Plugin\EntityReferenceSelection\NodeSelection::buildEntityQuery
   */
  protected function buildEntityQuery(?string $match = NULL, string $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);
    // Adding the 'node_access' tag is sadly insufficient for nodes: core
    // requires us to also know about the concept of 'published' and
    // 'unpublished'. We need to do that as long as there are no access control
    // modules in use on the site. As long as one access control module is there,
    // it is supposed to handle this check.
    if (!$this->currentUser->hasPermission('bypass node access') && !$this->moduleHandler->hasImplementations('node_grants')) {
      $query->condition('status', NodeInterface::PUBLISHED);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\node\Plugin\EntityReferenceSelection\NodeSelection::createNewEntity
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $node = parent::createNewEntity($entity_type_id, $bundle, $label, $uid);

    // In order to create a referenceable node, it needs to published.
    /** @var \Drupal\node\NodeInterface $node */
    $node->setPublished();

    return $node;
  }

}
