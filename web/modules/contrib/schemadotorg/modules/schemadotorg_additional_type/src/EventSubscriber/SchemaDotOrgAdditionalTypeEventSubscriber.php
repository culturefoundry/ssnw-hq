<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_type\EventSubscriber;

use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alters the Schema.org mapping list builder and adds a 'Additional type' column.
 *
 * @see \Drupal\schemadotorg\SchemaDotOrgMappingListBuilder
 */
class SchemaDotOrgAdditionalTypeEventSubscriber extends ServiceProviderBase implements EventSubscriberInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgAdditionalTypeEventSubscriber object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Alters Schema.org mapping list builder and adds a 'Subtyping' column.
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
    // Add 'Schema.org additional type' to header after 'Schema.org type'.
    // @see \Drupal\schemadotorg\SchemaDotOrgMappingTypeListBuilder::buildHeader
    $details_toggle = (boolean) ($event->getRequest()->query->get('details') ?? 0);
    $header_width = $details_toggle ? '10%' : '27%';
    $header =& $result['table']['#header'];
    $header['bundle_label']['width'] = $header_width;
    $header['schema_type']['width'] = $header_width;
    $header_cell = [
      'data' => $this->t('Additional type'),
      'class' => [RESPONSIVE_PRIORITY_LOW],
      'width' => '10%',
    ];
    $this->insertAfter($header, 'schema_type', 'schema_type', $header_cell);

    // Rows.
    // Add 'Schema.org additional type' to row after 'Schema.org type'.
    foreach ($result['table']['#rows'] as $id => &$row) {
      [$entity_type_id, $bundle] = explode('.', $id);
      $mapping = $this->loadMapping($entity_type_id, $bundle);
      $row_cell = $mapping->getSchemaPropertyFieldName('additionalType')
        ? $this->t('Yes')
        : $this->t('No');
      $this->insertAfter($row, 'schema_type', 'schema_type', $row_cell);
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
