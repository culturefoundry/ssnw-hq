<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\Component\Utility\NestedArray;
use Drupal\schemadotorg_report\Controller\SchemaDotOrgReportRelationshipsController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Returns responses for Schema.org relationships export.
 */
class SchemaDotOrgExportReportRelationshipsController extends SchemaDotOrgExportMappingDefaultBaseController {

  /**
   * The Schema.org report relationships controller.
   */
  protected SchemaDotOrgReportRelationshipsController $controller;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->controller = SchemaDotOrgReportRelationshipsController::create($container);
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
    $response = new StreamedResponse(function () use ($request, $format): void {
      $build = $this->controller->$format($request);
      $table = $build['table'];

      $handle = fopen('php://output', 'r+');

      // Header.
      $header = [];
      foreach ($table['#header'] as $table_header) {
        // Get the header value.
        $value = NestedArray::getValue($table_header, ['data', '#markup'])
          ?? NestedArray::getValue($table_header, ['data'])
          ?? $table_header;

        $header[] = $value;
      }
      fputcsv($handle, $header);

      // Rows.
      foreach ($table['#rows'] as $table_row) {
        $table_row = $table_row['data'] ?? $table_row;

        $row = [];
        foreach ($table_row as $row_id => $item) {
          // Get the row value.
          if (is_array($item)) {
            $value = NestedArray::getValue($item, ['data', '#markup'])
              ?? NestedArray::getValue($item, ['data', '#title'])
              ?? NestedArray::getValue($item, ['data', '#items'])
              ?? $item;
          }
          else {
            $value = (string) $item;
          }

          // Prefix all Schema.org type and properties with https://schema.org/.
          if (is_array($value) && $row_id !== 'starterkit') {
            $value = array_map(
              function (mixed $item): mixed {
                if (isset($item['#title'])) {
                  return 'https://schema.org/' . $item['#title'];
                }
                if (isset($item['#plain_text'])) {
                  return $item['#plain_text'];
                }
                else {
                  return $item;
                }
              },
              $value
            );
          }

          $row[] = implode(PHP_EOL, (array) $value);
        }
        fputcsv($handle, $row);
      }
      fclose($handle);
    });

    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-Disposition', 'attachment; filename="schemadotorg_relationships_' . $format . '.csv"');
    return $response;
  }

}
