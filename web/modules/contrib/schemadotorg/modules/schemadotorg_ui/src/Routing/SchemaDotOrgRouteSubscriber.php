<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Schema.org UI routes.
 *
 * @see \Drupal\field_ui\Routing\RouteSubscriber
 */
class SchemaDotOrgRouteSubscriber extends RouteSubscriberBase {

  /**
   * Constructs a SchemaDotOrgRouteSubscriber object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface $mapping_type_storage */
    $mapping_type_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping_type');
    $entity_types = $mapping_type_storage->getEntityTypes();
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
      // Make sure the entity is supported.
      if (!in_array($entity_type_id, $entity_types)) {
        continue;
      }

      // Make sure the entity has a field UI.
      $route_name = $entity_type->get('field_ui_base_route');
      if (!$route_name) {
        continue;
      }

      // Try to get the route from the current collection.
      $entity_route = $collection->get($route_name);
      if (!$entity_route) {
        continue;
      }

      $path = $entity_route->getPath();

      $options = $entity_route->getOptions();
      $bundle_entity_type = $entity_type->getBundleEntityType();
      if ($bundle_entity_type) {
        $options['parameters'][$bundle_entity_type] = [
          'type' => 'entity:' . $bundle_entity_type,
        ];
      }
      // Special parameter used to easily recognize all Field UI routes.
      $options['_field_ui'] = TRUE;

      $defaults = [
        'entity_type_id' => $entity_type_id,
      ];
      // If the entity type has no bundles, and it doesn't use {bundle} in its
      // admin path, use the entity type.
      if (!str_contains($path, '{bundle}')) {
        $defaults['bundle'] = !$entity_type->hasKey('bundle') ? $entity_type_id : '';
      }

      $requirements = ['_permission' => 'administer ' . $entity_type_id . ' fields'];

      // Add 'Manage Schema.org fields' route.
      $route = new Route(
        "$path/schemadotorg",
        [
          '_title' => 'Manage Schema.org fields',
          '_entity_form' => 'schemadotorg_mapping.edit',
        ] + $defaults,
        $requirements,
        $options
      );
      $collection->add("entity.{$entity_type_id}.schemadotorg_mapping", $route);

      // Skip media unless the schemadotorg_media.module is installed.
      if ($entity_type_id === 'media'
        && !$this->moduleHandler->moduleExists('schemadotorg_media')) {
        continue;
      }

      // Add 'Add Schema.org type' route.
      $entity_collection_route = $collection->get("entity.{$bundle_entity_type}.collection");
      if ($bundle_entity_type && $entity_collection_route) {
        $entity_collection_path = $entity_collection_route->getPath();
        $bundle_entity_type_label = ($entity_type_id === 'paragraph')
          ? 'paragraph type'
          : $this->entityTypeManager->getDefinition($bundle_entity_type)->getSingularLabel();

        $route = new Route(
          "$entity_collection_path/schemadotorg",
          [
            '_title' => 'Add Schema.org ' . $bundle_entity_type_label,
            '_entity_form' => 'schemadotorg_mapping.add',
          ] + $defaults,
          $requirements,
        );
        $collection->add("schemadotorg.{$bundle_entity_type}.type_add", $route);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events = parent::getSubscribedEvents();
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -100];
    return $events;
  }

}
