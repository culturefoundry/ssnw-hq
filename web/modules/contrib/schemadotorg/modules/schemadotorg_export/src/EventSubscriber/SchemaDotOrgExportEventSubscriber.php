<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\EventSubscriber;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alters Schema.org mapping list builder and adds a 'Download CSV' link.
 *
 * @see \Drupal\schemadotorg_export\Controller\SchemaDotOrgExportMappingController
 */
class SchemaDotOrgExportEventSubscriber extends ServiceProviderBase implements EventSubscriberInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgJsonApiExtrasEventSubscriber object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * Alters Schema.org mapping list builder and adds a 'Download CSV' link.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The event to process.
   */
  public function onView(ViewEvent $event): void {
    $route_name = $this->routeMatch->getRouteName();
    if (!str_contains($route_name, 'schemadotorg')) {
      return;
    }

    switch ($route_name) {
      case 'entity.schemadotorg_mapping.collection':
        $this->appendButton(
          $event,
          $this->t('<u>⇩</u> Download CSV'),
          'entity.schemadotorg_mapping.overview.export'
        );
        break;

      case 'entity.schemadotorg_mapping.edit_form':
        $this->appendButton(
          $event,
          $this->t('<u>⇩</u> Export HTML'),
          'entity.schemadotorg_mapping.details.export'
        );
        break;

      case 'schemadotorg_mapping_set.overview':
        $this->appendButton(
          $event,
          $this->t('<u>⇩</u> Download CSV'),
          'schemadotorg_mapping_set.overview.export',
        );
        break;

      case 'schemadotorg_mapping_set.details':
        $this->appendButton(
          $event,
          $this->t('<u>⇩</u> Download CSV'),
          'schemadotorg_mapping_set.details.export'
        );
        break;

      case 'schemadotorg_starterkit.details':
        $this->appendButton(
          $event,
          $this->t('<u>⇩</u> Download CSV'),
          'schemadotorg_starterkit.details.export'
        );
        break;

      case 'schemadotorg_report':
        if ($this->schemaTypeManager->isType($this->routeMatch->getParameter('id'))) {
          $this->appendButton(
            $event,
            $this->t('<u>⇩</u> Download CSV'),
            'schemadotorg_report.type.export'
          );
        }
        break;

      case 'schemadotorg_report.relationships':
        $this->appendButton(
          $event,
          $this->t('<u>⇩</u> Download CSV'),
          'schemadotorg_report.relationships.export'
        );
        break;

    }

    if (preg_match('/^entity\.([^.]+)\.schemadotorg_mapping$/', $route_name, $match)) {
      $entity_type_id = $match[1];
      $parameters = ($this->routeMatch->getRawParameters()->all());
      unset($parameters['entity_type_id']);
      $parameters = array_filter($parameters);
      $bundle = reset($parameters);

      $url = Url::fromRoute(
        'entity.schemadotorg_mapping.details.export',
        ['schemadotorg_mapping' => "$entity_type_id.$bundle"]
      );

      $this->appendButton(
        $event,
        $this->t('<u>⇩</u> Export HTML'),
        $url
      );
    }
  }

  /**
   * Append a button to the event's controller result.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The event to process.
   * @param string|\Drupal\Component\Render\MarkupInterface $text
   *   The link text for the button.
   * @param string|\Drupal\Core\Url $url
   *   The route name or Url to create the link for button.
   */
  protected function appendButton(ViewEvent $event, string|MarkupInterface $text, string|Url $url): void {
    if (is_string($url)) {
      $url = Url::fromRoute($url, $this->routeMatch->getRawParameters()->all());
    }

    $result = $event->getControllerResult();
    $result['export'] = [
      '#type' => 'link',
      '#title' => $text,
      '#url' => $url,
      '#attributes' => ['class' => ['button', 'button--small', 'button--extrasmall']],
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#weight' => 100,
    ];
    $event->setControllerResult($result);
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
