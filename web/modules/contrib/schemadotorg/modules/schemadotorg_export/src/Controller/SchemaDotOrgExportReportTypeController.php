<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Returns responses for Schema.org report about and item routes.
 */
class SchemaDotOrgExportReportTypeController extends ControllerBase {

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    return $instance;
  }

  /**
   * Exports a Schema.org type.
   *
   * @param string $id
   *   The Schema.org type.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing a Schema.org type CSV export.
   */
  public function index(string $id): StreamedResponse {
    $response = new StreamedResponse(function () use ($id): void {
      $handle = fopen('php://output', 'r+');

      // Get default properties from type breadcrumb.
      $schema_types_default_properties = $this->config('schemadotorg.settings')
        ->get('schema_types.default_properties');
      $breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs($id);
      $default_properties = [];
      foreach ($breadcrumbs as $breadcrumb) {
        foreach ($breadcrumb as $breadcrumb_type) {
          if (isset($schema_types_default_properties[$breadcrumb_type])) {
            $default_properties += $schema_types_default_properties[$breadcrumb_type];
          }
        }
      }
      $default_properties = array_combine($default_properties, $default_properties);

      // Get ignored properties.
      $ignored_properties = $this->config('schemadotorg.settings')
        ->get('schema_properties.ignored_properties');
      $ignored_properties = $ignored_properties ? array_combine($ignored_properties, $ignored_properties) : [];

      // Get all type properties.
      $properties = $this->schemaTypeManager->getTypeProperties($id);
      $property = reset($properties);

      // Header.
      $header = array_keys($property);
      $header[] = 'status';
      fputcsv($handle, $header);

      // Rows.
      foreach ($properties as $property_name => $property) {
        $row = $property;
        if (isset($default_properties[$property_name])) {
          $row[] = 'default';
        }
        elseif (isset($ignored_properties[$property_name])) {
          $row[] = 'ignored';
        }
        else {
          $row[] = '';
        }
        fputcsv($handle, $row);
      }
      fclose($handle);
    });

    $type = $this->schemaTypeManager->getType($id);
    $name = $type['drupal_name'];
    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-Disposition', 'attachment; filename="schemadotorg_type_' . $name . '.csv"');
    return $response;
  }

}
