<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_endpoint\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines dynamic routes for Schema.org JSON-LD endpoint module.
 */
final class SchemaDotOrgJsonLdEndpointRoutes implements ContainerInjectionInterface {

  /**
   * A key with which to flag a route as belonging to the Schema.org JSON-LD endpoint module.
   */
  const JSONLD_ROUTE_FLAG_KEY = '_is_schemadotorg_jsonld_endpoint';

  /**
   * The configuration factory.
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = new static();
    $instance->configFactory = $container->get('config.factory');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function routes(): RouteCollection {
    $config = $this->configFactory->get('schemadotorg_jsonld_endpoint.settings');

    $routes = new RouteCollection();

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface $mapping_type_storage */
    $mapping_type_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping_type');
    $endpoints = $config->get('entity_type_endpoints') + $mapping_type_storage->getEntityTypes();
    foreach ($endpoints as $entity_type_id => $entity_type_path) {
      if (!$this->entityTypeManager->hasDefinition($entity_type_id)) {
        continue;
      }

      $name = 'schemadotorg_jsonld_endpoint.' . $entity_type_id;
      $path = "/jsonld/" . $entity_type_path . "/{entity}";
      $defaults = [
        '_controller' => '\Drupal\schemadotorg_jsonld_endpoint\Controller\SchemaDotOrgJsonLdEndpointController::getEntity',
        // Flag route as belonging to the Schema.org JSON-LD module.
        // @see \Drupal\schemadotorg_jsonld_endpoint\ParamConverter\EntityUuidConverter::applies
        static::JSONLD_ROUTE_FLAG_KEY => TRUE,
      ];
      $requirements = [
        '_custom_access' => '\Drupal\schemadotorg_jsonld_endpoint\Controller\SchemaDotOrgJsonLdEndpointController::access',
      ];
      $options = [
        'parameters' => [
          'entity' => ['type' => 'entity:' . $entity_type_id],
        ],
      ];

      $route = (new Route($path))
        ->setDefaults($defaults)
        ->setRequirements($requirements)
        ->setOptions($options)
        ->setMethods(['GET']);

      $routes->add($name, $route);
    }

    return $routes;
  }

}
