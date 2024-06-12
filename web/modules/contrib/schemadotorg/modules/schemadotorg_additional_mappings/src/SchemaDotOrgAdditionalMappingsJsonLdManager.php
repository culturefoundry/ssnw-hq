<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_mappings;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;

/**
 * Schema.org additional mappings JSON-LD manager.
 */
class SchemaDotOrgAdditionalMappingsJsonLdManager implements SchemaDotOrgAdditionalMappingsJsonLdManagerInterface {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgAdditionalMappingsJsonLdManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface|null $schemaJsonLdManager
   *   The Schema.org JSON-LD manager service.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface|null $schemaJsonLdBuilder
   *   The Schema.org JSON-LD builder service.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected ?SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager = NULL,
    protected ?SchemaDotOrgJsonLdBuilderInterface $schemaJsonLdBuilder = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function entityAlter(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping, BubbleableMetadata $bubbleable_metadata): void {
    // Make sure this is an entity with a mapping.
    if (!$mapping) {
      return;
    }

    $additional_mappings = $mapping->getAdditionalMappings();
    foreach ($additional_mappings as $schema_type => $additional_mapping) {
      if ($this->isWebPage($schema_type)) {
        continue;
      }

      // Append the @type.
      $data['@type'] = array_merge(
        (array) $data['@type'],
        (array) $schema_type
      );
      // Append the additional data, which can be empty.
      $data += $this->getAdditionalData($entity, $mapping, $additional_mapping) ?? [];
    }

    $data = $this->schemaJsonLdManager->sortProperties($data);
  }

  /**
   * {@inheritdoc}
   */
  public function alter(array &$data, RouteMatchInterface $route_match, BubbleableMetadata $bubbleable_metadata): void {
    $entity = $this->schemaJsonLdManager->getRouteMatchEntity($route_match);
    if (!$entity) {
      return;
    }

    $mapping = $this->getMappingStorage()->loadByEntity($entity);
    if (!$mapping) {
      return;
    }

    $additional_mappings = $mapping->getAdditionalMappings();
    foreach ($additional_mappings as $schema_type => $additional_mapping) {
      if (!$this->isWebPage($schema_type)) {
        continue;
      }

      $additional_data = $this->getAdditionalData($entity, $mapping, $additional_mapping);
      $data['schemadotorg_jsonld_entity'] = $additional_data
        + ['mainEntity' => $data['schemadotorg_jsonld_entity']];

      $data['schemadotorg_jsonld_entity'] = $this->schemaJsonLdManager->sortProperties(
        $data['schemadotorg_jsonld_entity']
      );
    }
  }

  /**
   * Determine if a Schema.org type is a WebPage.
   *
   * @param string $schema_type
   *   A Schema.org type.
   *
   * @return bool
   *   TRUE if the Schema.org type is a WebPage.
   */
  protected function isWebPage(string $schema_type): bool {
    return $this->schemaTypeManager->isSubTypeOf($schema_type, 'WebPage');
  }

  /**
   * Get the JSON-LD data for an additional Schema.org mapping.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org (main) mapping.
   * @param array $additional_mapping
   *   An additional Schema.org mapping.
   *
   * @return array|null
   *   The JSON-LD data for an additional Schema.org mapping.
   */
  protected function getAdditionalData(EntityInterface $entity, SchemaDotOrgMappingInterface $mapping, array $additional_mapping): ?array {
    $schema_properties = $additional_mapping['schema_properties'];

    // Create a temp additional mapping, which does not need to be saved.
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $additional_mapping */
    $additional_mapping = $this->getMappingStorage()->create([
      'target_entity_type_id' => $mapping->getTargetEntityTypeId(),
      'target_bundle' => $mapping->getTargetBundle(),
      'schema_type' => $additional_mapping['schema_type'],
      'schema_properties' => $schema_properties,
    ]);

    return $this->schemaJsonLdBuilder->buildEntity($entity, $additional_mapping);
  }

}
