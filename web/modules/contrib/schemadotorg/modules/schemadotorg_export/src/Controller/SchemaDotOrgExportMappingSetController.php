<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\schemadotorg_mapping_set\SchemaDotOrgMappingSetManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Schema.org mapping set export.
 */
class SchemaDotOrgExportMappingSetController extends SchemaDotOrgExportMappingDefaultBaseController {

  /**
   * The Schema.org mapping set manager service.
   */
  protected SchemaDotOrgMappingSetManagerInterface $schemaMappingSetManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaMappingSetManager = $container->get('schemadotorg_mapping_set.manager');
    return $instance;
  }

  /**
   * Returns response for Schema.org mapping set CSV export request.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing a Schema.org mapping set CSV export.
   */
  public function overview(): StreamedResponse {
    $response = new StreamedResponse(function (): void {
      $handle = fopen('php://output', 'r+');

      // Header.
      fputcsv($handle, [
        'title',
        'name',
        'types',
      ]);

      // Rows.
      $mapping_sets = $this->config('schemadotorg_mapping_set.settings')->get('sets') ?? [];
      foreach ($mapping_sets as $name => $mapping_set) {
        fputcsv($handle, [
          $mapping_set['label'],
          $name,
          implode('; ', $mapping_set['types']),
        ]);
      }
      fclose($handle);
    });

    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-Disposition', 'attachment; filename="schemadotorg_mapping_set.csv"');
    return $response;
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
    $mapping_set = $this->config('schemadotorg_mapping_set.settings')->get("sets.$name");
    if (empty($mapping_set)) {
      throw new NotFoundHttpException();
    }

    // Set mapping set types with empty defaults arrays.
    $types = array_fill_keys($mapping_set['types'], []);

    return $this->exportTypes($types, 'schemadotorg_mapping_set_' . $name);
  }

}
