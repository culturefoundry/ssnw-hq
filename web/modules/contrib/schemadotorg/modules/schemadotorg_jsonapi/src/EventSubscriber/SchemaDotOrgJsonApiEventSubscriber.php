<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\jsonapi_extras\ResourceType\ConfigurableResourceTypeRepository;
use Drupal\schemadotorg_jsonapi\SchemaDotOrgJsonApiManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alters Schema.org mapping list builder and adds a 'JSON:API' column.
 *
 * @see \Drupal\schemadotorg\SchemaDotOrgMappingListBuilder
 */
class SchemaDotOrgJsonApiEventSubscriber extends ServiceProviderBase implements EventSubscriberInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgJsonApiEventSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\jsonapi_extras\ResourceType\ConfigurableResourceTypeRepository $resourceTypeRepository
   *   The JSON:API configurable resource type repository.
   * @param \Drupal\schemadotorg_jsonapi\SchemaDotOrgJsonApiManagerInterface $schemaJsonApiManager
   *   The Schema.org JSON:API manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected ConfigurableResourceTypeRepository $resourceTypeRepository,
    protected SchemaDotOrgJsonApiManagerInterface $schemaJsonApiManager,
  ) {}

  /**
   * Alters Schema.org mapping list builder and adds a 'JSON:API' column.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The event to process.
   */
  public function onView(ViewEvent $event): void {
    if ($this->routeMatch->getRouteName() !== 'entity.schemadotorg_mapping.collection') {
      return;
    }

    $result = $event->getControllerResult();

    // Header.
    // Add 'JSON:API' to header after 'Name'.
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingTypeListBuilder::buildHeader
    $details_toggle = (boolean) ($event->getRequest()->query->get('details') ?? 0);
    $header_width = $details_toggle ? '10%' : '27%';
    $header =& $result['table']['#header'];
    $header['bundle_label']['width'] = $header_width;
    $header['schema_type']['width'] = $header_width;
    $header_cell = [
      'data' => $this->t('JSON:API'),
      'class' => [RESPONSIVE_PRIORITY_LOW],
      'width' => $header_width,
    ];
    $this->insertAfter($header, 'bundle_label', 'jsonapi', $header_cell);

    // Rows.
    // Add 'JSON:API' to row after 'Name'.
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingTypeListBuilder::buildRow
    $path_prefix = $this->configFactory
      ->get('jsonapi_extras.settings')
      ->get('path_prefix');
    foreach ($result['table']['#rows'] as $id => &$row) {
      [$entity_type_id, $bundle] = explode('.', $id);
      $resource_id = "$entity_type_id--$bundle";

      /** @var \Drupal\jsonapi_extras\Entity\JsonapiResourceConfig|null $resource_config */
      $resource_config = $this->entityTypeManager->getStorage('jsonapi_resource_config')->load($resource_id);
      $resource_type_name = ($resource_config && $resource_config->get('resourceType'))
        ? $resource_config->get('resourceType')
        : $resource_id;
      $resource_type = $this->resourceTypeRepository->getByTypeName($resource_type_name);

      $resource_path = sprintf('/%s%s', $path_prefix, $resource_type->getPath());
      $resource_includes = $this->schemaJsonApiManager->getResourceIncludes($resource_type);
      $resource_options = $resource_includes
      ? ['query' => ['include' => implode(',', $resource_includes)]]
      : [];
      $row_cell = [
        'data' => [
          '#type' => 'link',
          '#title' => $resource_path,
          '#url' => Url::fromUri('base:' . $resource_path, $resource_options),
          '#prefix' => '<code>',
          '#suffix' => '</code>',
        ],
      ];
      $this->insertAfter($row, 'bundle_label', 'jsonapi', $row_cell);
    }

    $event->setControllerResult($result);
  }

  /**
   * Inserts a new key/value after the key in the array.
   *
   * @param array &$array
   *   An array to insert in to.
   * @param string $target_key
   *   The key to insert after.
   * @param string $new_key
   *   The key to insert.
   * @param mixed $new_value
   *   The value to insert.
   */
  protected function insertAfter(array &$array, string $target_key, string $new_key, mixed $new_value): void {
    $new = [];
    foreach ($array as $key => $value) {
      $new[$key] = $value;
      if ($key === $target_key) {
        $new[$new_key] = $new_value;
      }
    }
    $array = $new;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Run before main_content_view_subscriber.
    $events[KernelEvents::VIEW][] = ['onView', 100];
    return $events;
  }

}
