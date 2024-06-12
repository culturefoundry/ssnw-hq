<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Returns responses for Schema.org mapping default base export.
 */
class SchemaDotOrgExportMappingDefaultBaseController extends ControllerBase {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * The Schema.org schema names services.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * The Schema.org mapping manager service.
   */
  protected SchemaDotOrgMappingManagerInterface $schemaMappingManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    return $instance;
  }

  /**
   * Returns response for Schema.org mapping default CSV export request.
   *
   * @param array $types
   *   An associative array of Schema.org types mapping defaults.
   * @param string $name
   *   The Schema.org mapping default CSV export file name.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing Schema.org types mapping defaults
   *   in a CSV export.
   */
  protected function exportTypes(array $types, string $name): StreamedResponse {
    $response = new StreamedResponse(function () use ($types): void {
      $handle = fopen('php://output', 'r+');

      // Header.
      fputcsv($handle, [
        'entity_type',
        'entity_bundle',
        'schema_type',
        'field_label',
        'field_description',
        'schema_property',
        'field_name',
        'existing_field',
        'field_type',
        'unlimited_field',
        'required_field',
      ]);

      // Rows.
      foreach ($types as $type => $defaults) {
        if (!$this->getMappingStorage()->isValidType($type)) {
          continue;
        }

        [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);

        $mapping = $this->getMappingStorage()->loadBySchemaType($entity_type_id, $schema_type);
        $mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
          entity_type_id: $entity_type_id,
          schema_type: $schema_type,
          defaults: $defaults,
        );
        $bundle = ($mapping) ? $mapping->getTargetBundle() : $mapping_defaults['entity']['id'];
        $field_prefix = $this->schemaNames->getFieldPrefix();

        // Main mapping's properties.
        foreach ($mapping_defaults['properties'] as $property_name => $property_definition) {
          if (empty($property_definition['name'])) {
            continue;
          }

          $record = [];
          $record[] = $entity_type_id;
          $record[] = $bundle;
          $record[] = $schema_type;
          $record[] = $property_definition['label'];
          $record[] = $property_definition['description'];
          $record[] = $property_name;
          if ($property_definition['name'] === SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD) {
            $record[] = $field_prefix . '_' . $property_definition['machine_name'];
            $record[] = $this->t('No');
          }
          else {
            $record[] = $property_definition['name'];
            $record[] = $this->t('Yes');
          }
          $record['type'] = $property_definition['type'];
          $record['unlimited'] = !empty($property_definition['unlimited']) ? $this->t('Yes') : $this->t('No');
          $record['required'] = !empty($property_definition['required']) ? $this->t('Yes') : $this->t('No');

          fputcsv($handle, $record);
        }

        // Additional mappings' properties.
        if (isset($mapping_defaults['additional_mappings'])) {
          foreach ($mapping_defaults['additional_mappings'] as $additional_mapping) {
            $additional_mapping_schema_type = $additional_mapping['schema_type'];
            $additional_mapping_schema_properties = $additional_mapping['schema_properties'];
            $mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
              entity_type_id: $entity_type_id,
              schema_type: $additional_mapping_schema_type,
            );
            foreach ($mapping_defaults['properties'] as $property_name => $property_definition) {
              if (!in_array($property_name, $additional_mapping_schema_properties)) {
                continue;
              }

              $record = [];
              $record[] = $entity_type_id;
              $record[] = $bundle;
              $record[] = $additional_mapping_schema_type;
              $record[] = $property_definition['label'];
              $record[] = $property_definition['description'];
              $record[] = $property_name;
              if ($property_definition['name'] === SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD) {
                $record[] = $field_prefix . '_' . $property_definition['machine_name'];
                $record[] = $this->t('No');
              }
              else {
                $record[] = $property_definition['name'];
                $record[] = $this->t('Yes');
              }
              $record['type'] = $property_definition['type'];
              $record['unlimited'] = !empty($property_definition['unlimited']) ? $this->t('Yes') : $this->t('No');
              $record['required'] = !empty($property_definition['required']) ? $this->t('Yes') : $this->t('No');

              fputcsv($handle, $record);
            }
          }
        }
      }
    });

    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-Disposition', 'attachment; filename="schemadotorg_mapping_set_' . $name . '.csv"');
    return $response;
  }

}
