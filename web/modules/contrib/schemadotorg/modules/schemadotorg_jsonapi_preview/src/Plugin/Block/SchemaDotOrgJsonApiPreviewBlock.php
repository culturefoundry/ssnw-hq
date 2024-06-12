<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi_preview\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg_jsonapi_preview\SchemaDotOrgJsonApiPreviewBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Schema.org JSON:API preview' block.
 *
 * @Block(
 *   id = "schemadotorg_jsonapi_preview",
 *   admin_label = @Translation("Schema.org Blueprints JSON:API Preview"),
 *   category = @Translation("Schema.org Blueprints")
 * )
 */
final class SchemaDotOrgJsonApiPreviewBlock extends BlockBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * The current route match.
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * The Schema.org JSON-LD preview builder.
   */
  protected SchemaDotOrgJsonApiPreviewBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    $instance->builder = $container->get('schemadotorg_jsonapi_preview.builder');
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
    $entity = $this->getRouteMatchEntity();
    if (!$entity) {
      return NULL;
    }

    $build = $this->builder->build($entity);
    if (!$build) {
      return NULL;
    }

    // Display the JSON:API using a details element.
    $build['#type'] = 'details';
    $build['#title'] = $this->t('Schema.org JSON:API');
    $build['#attributes']['data-schemadotorg-details-key'] = 'schemadotorg-jsonapi-preview';
    return ['details' => $build];
  }

  /**
   * Returns the entity of the current route.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity or NULL if this is not an entity route.
   *
   * @see metatag_get_route_entity()
   */
  protected function getRouteMatchEntity(): EntityInterface|NULL {
    $route_name = $this->routeMatch->getRouteName();
    if (preg_match('/entity\.(.*)\.(latest[_-]version|canonical)/', $route_name, $matches)) {
      return $this->routeMatch->getParameter($matches[1]);
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'view schemadotorg jsonapi');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user.permissions', 'url.path'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $entity = $this->getRouteMatchEntity();
    if ($entity) {
      return Cache::mergeTags(parent::getCacheTags(), $entity->getCacheTags());
    }
    else {
      return parent::getCacheTags();
    }
  }

}
