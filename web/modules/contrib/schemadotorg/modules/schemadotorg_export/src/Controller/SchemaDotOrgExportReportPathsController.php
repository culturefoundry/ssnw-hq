<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\Component\Utility\NestedArray;
use Drupal\schemadotorg_pathauto\Controller\SchemaDotOrgPathautoReportController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Returns responses for Schema.org paths export.
 */
class SchemaDotOrgExportReportPathsController extends SchemaDotOrgExportMappingDefaultBaseController {

  /**
   * The Schema.org Pathauto paths report  controller.
   */
  protected SchemaDotOrgPathautoReportController $controller;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->controller = SchemaDotOrgPathautoReportController::create($container);
    return $instance;
  }

  /**
   * Returns response for Schema.org mapping set CSV export request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param string $format
   *   The format of the Schema.org relationships table. Defaults to 'overview'.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing a Schema.org mapping set CSV export.
   */
  public function index(Request $request, string $format = 'overview'): StreamedResponse {
    $response = new StreamedResponse(function (): void {
      $build = $this->controller->index();
      $table = $build['table'];

      $handle = fopen('php://output', 'r+');

      // Header.
      $header = [];
      foreach ($table['#header'] as $table_header) {
        $header[] = $this->getTableItemValue($table_header);
      }
      fputcsv($handle, $header);

      // Rows.
      foreach ($table['#rows'] as $table_row) {
        $row = [];
        foreach ($table_row as $table_cell) {
          $row[] = $this->getTableItemValue($table_cell);
        }
        fputcsv($handle, $row);
      }
      fclose($handle);
    });

    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-Disposition', 'attachment; filename="schemadotorg_paths.csv"');
    return $response;
  }

  /**
   * Get the value of a table item.
   *
   * @param mixed $item
   *   The item to get the value from.
   *
   * @return string
   *   The value of the table item.
   */
  protected function getTableItemValue(mixed $item): string {
    // Get the row value.
    if (is_array($item)) {
      $value = NestedArray::getValue($item, ['data', '#title'])
        ?? NestedArray::getValue($item, ['data'])
        ?? $item;
      return (string) $value;
    }
    else {
      return (string) $item;
    }
  }

}
