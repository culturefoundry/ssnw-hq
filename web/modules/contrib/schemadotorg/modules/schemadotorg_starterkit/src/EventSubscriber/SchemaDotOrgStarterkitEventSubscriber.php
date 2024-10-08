<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit\EventSubscriber;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\schemadotorg\Utility\SchemaDotOrgArrayHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alters Schema.org reports.
 */
class SchemaDotOrgStarterkitEventSubscriber extends ServiceProviderBase implements EventSubscriberInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgStarterkitEventSubscriber object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $schemaMappingManager
   *   The Schema.org mapping manager.
   */
  public function __construct(
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgMappingManagerInterface $schemaMappingManager,
  ) {}

  /**
   * Alters Schema.org type report and adds starter kit information.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The event to process.
   */
  public function onView(ViewEvent $event): void {
    $route_name = $this->routeMatch->getRouteName();
    $id = $this->routeMatch->getParameter('id');
    if ($route_name !== 'schemadotorg_report'
      || !$this->schemaTypeManager->isType($id)) {
      return;
    }

    $starterkit_defaults = $this->getStarterkitDefaults($id);

    $result = $event->getControllerResult();
    $build = [
      '#type' => 'details',
      '#title' => $this->t('Starter kit defaults'),
      '#description' => $this->t('The below starter kit defaults are used when the Schema.org type is created via a starter kit.'),
      'code' => [
        '#type' => 'html_tag',
        '#tag' => 'pre',
        '#plain_text' => Yaml::encode($starterkit_defaults),
        '#attributes' => ['data-schemadotorg-codemirror-mode' => 'text/x-yaml'],
        '#attached' => ['library' => ['schemadotorg/codemirror.yaml']],
      ],
    ];
    SchemaDotOrgArrayHelper::insertAfter($result, 'mapping_defaults', 'starterkit', $build);

    $event->setControllerResult($result);
  }

  /**
   * Get a Schema.org type's starter kit defaults.
   *
   * @param string $id
   *   A Schema.org type.
   *
   * @return array
   *   A Schema.org type's starter kit defaults.
   */
  protected function getStarterkitDefaults(string $id): array {
    $default_entity_type_id = $this->getDefaultEntityTypeId($id);
    $mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
      entity_type_id: $default_entity_type_id,
      schema_type: $id,
    );
    foreach ($mapping_defaults['properties'] as $property_name => $property_definition) {
      if (is_null($property_definition['name'])) {
        unset($mapping_defaults['properties'][$property_name]);
      }
      else {
        $mapping_defaults['properties'][$property_name] = TRUE;
      }
    }

    $default_bundle = $mapping_defaults['entity']['id'];

    return [
      "$default_entity_type_id:$default_bundle:$id" => $mapping_defaults,
    ];
  }

  /**
   * Get the default entity type id for a Schema.org type.
   *
   * @param string $type
   *   A Schema.org type.
   *
   * @return string
   *   The default entity type id for the Schema.org type.
   */
  protected function getDefaultEntityTypeId(string $type): string {
    if ($this->schemaTypeManager->isSubTypeOf($type, 'Intangible')
      && $this->loadMappingType('paragraph')) {
      return 'paragraph';
    }
    elseif ($this->schemaTypeManager->isSubTypeOf($type, 'MediaObject')
      && $this->loadMappingType('media')) {
      return 'media';
    }
    else {
      return 'node';
    }
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
