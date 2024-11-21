<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\schemadotorg_recipe\SchemaDotOrgRecipeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Schema.org recipe export.
 */
class SchemaDotOrgExportRecipeController extends SchemaDotOrgExportMappingDefaultBaseController {

  /**
   * The Schema.org starter kit manager service.
   */
  protected SchemaDotOrgRecipeManagerInterface $schemaRecipeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaRecipeManager = $container->get('schemadotorg_recipe.manager');
    return $instance;
  }

  /**
   * Returns response for Schema.org mapping set CSV export request.
   *
   * @param string $name
   *   The name of the Schema.org recipe.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing a Schema.org recipe CSV export.
   */
  public function details(string $name): StreamedResponse {
    $settings = $this->schemaRecipeManager->getRecipeSettings($name);
    if (!$settings) {
      throw new NotFoundHttpException();
    }

    return $this->exportTypes($settings['schemadotorg']['types'], $name);
  }

}
