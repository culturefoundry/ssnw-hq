<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Schema.org starter kit export.
 */
class SchemaDotOrgExportStarterKitController extends SchemaDotOrgExportMappingDefaultBaseController {

  /**
   * The Schema.org starter kit manager service.
   */
  protected SchemaDotOrgStarterkitManagerInterface $schemaStarterKitManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaStarterKitManager = $container->get('schemadotorg_starterkit.manager');
    return $instance;
  }

  /**
   * Returns response for Schema.org mapping set CSV export request.
   *
   * @param string $name
   *   The name of the Schema.org mapping set.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing a Schema.org mapping set CSV export.
   */
  public function details(string $name): StreamedResponse {
    $settings = $this->schemaStarterKitManager->getStarterkitSettings($name);
    if (!$settings) {
      throw new NotFoundHttpException();
    }

    return $this->exportTypes($settings['types'], $name);
  }

}
