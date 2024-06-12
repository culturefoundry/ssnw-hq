<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_endpoint\EventSubscriber;

use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Routing\AccessAwareRouterInterface;
use Drupal\Core\Url;
use Drupal\jsonapi\Events\CollectResourceObjectMetaEvent;
use Drupal\jsonapi\Events\MetaDataEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Alters JSON:API resource meta data to include JSON-LD URI.
 */
class SchemaDotOrgJsonLdEndpointEventSubscriber extends ServiceProviderBase implements EventSubscriberInterface {

  /**
   * Constructs a SchemaDotOrgJsonLdEndpointEventSubscriber object.
   *
   * @param \Drupal\Core\Routing\AccessAwareRouterInterface $routeProvider
   *   The route provider.
   */
  public function __construct(
    protected AccessAwareRouterInterface $routeProvider,
  ) {}

  /**
   * Add JSON-LD URI to JSON:API resource object meta data.
   *
   * @param \Drupal\jsonapi\Events\CollectResourceObjectMetaEvent $event
   *   The event used for collecting resource object metadata.
   *
   * @phpstan-ignore-next-line
   */
  public function addResourceObjectMeta(CollectResourceObjectMetaEvent $event): void {

    $resource_object = $event->getResourceObject();
    $resource_type = $resource_object->getResourceType();

    $entity_type_id = $resource_type->getEntityTypeId();
    $entity_uuid = $resource_object->getId();

    $route_name = 'schemadotorg_jsonld_endpoint.' . $entity_type_id;
    $route_parameters = ['entity' => $entity_uuid];
    $route_options = ['absolute' => TRUE];

    // Make sure the JSON-LD route exists.
    // @see \Drupal\schemadotorg_jsonld_endpoint\Routing\SchemaDotOrgJsonLdEndpointRoutes::routes
    if (!$this->routeProvider->getRouteCollection()->get($route_name)) {
      return;
    }

    $uri = Url::fromRoute($route_name, $route_parameters, $route_options)->toString();

    $event->setMeta('json-ld', $uri);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    if (!class_exists('\Drupal\jsonapi\Events\MetaDataEvents')) {
      return [];
    }
    else {
      return [MetaDataEvents::COLLECT_RESOURCE_OBJECT_META => 'addResourceObjectMeta'];
    }
  }

}
