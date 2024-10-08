<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Traits;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Trait for building Schema.org report relationships.
 */
trait SchemaDotOrgReportRelationshipsTrait {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * An array of empty relationships..
   */
  protected array $relationships = [
    'hierarchical' => [],
    'reference' => [],
    'link' => [],
    'enumeration' => [],
    'taxonomy_term' => [],
    'media' => [],
  ];

  /**
   * An array of hierarchy Schema.org properties.
   */
  protected array $hierarchyProperties = [
    'subOrganization',
    'parentOrganization',
    'subEvent',
    'superEvent',
    'containedInPlace',
    'containsPlace',
    'offeredBy',
    'makesOffer',
    'isPartOf',
    'hasPart',
    'hasVariant',
    'isVariantOf',
    'partOfTrip',
    'subTrip',
    'partOfSeason',
    'partOfSeries',
    'episode',
    'department',
  ];

  /**
   * An associative array containing Schema.org mapping categories.
   */
  protected array $mappingCategories;

  /**
   * Get the category definition for a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   *
   * @return array
   *   The category definition for a Schema.org mapping.
   */
  protected function getMappingCategory(SchemaDotOrgMappingInterface $mapping): array {
    if (!isset($this->mappingCategories)) {
      $categories = $this->config('schemadotorg.settings')
        ->get('schema_types.categories');
      $this->mappingCategories = [];
      foreach ($categories as $category_name => $category_definition) {
        foreach ($category_definition['types'] as $category_type) {
          $this->mappingCategories[$category_type] = $category_definition + ['name' => $category_name];
        }
      }
    }
    $setting = $this->schemaTypeManager->getSetting($this->mappingCategories, $mapping) ?? [];
    return $setting + [
      'name' => 'zzz_other',
      'label' => (string) $this->t('Other'),
      'color' => '#ffffcc',
    ];
  }

  /**
   * Get Schema.org properties initially excluded from diagram relationships.
   *
   * @return string[]
   *   Schema.org properties initially excluded from diagram relationships.
   */
  protected function getDiagramExcludedSchemaProperties(): array {
    return $this->config('schemadotorg_report.settings')
      ->get('diagram_excluded_schema_properties') ?? [];
  }

  /**
   * Gets the relationships based on a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   * @param array $types
   *   Relationships types to be return.
   * @param array $filter
   *   Relationships type/property filter.
   *
   * @return array
   *   An array of relationships.
   */
  protected function getMappingRelationships(SchemaDotOrgMappingInterface $mapping, array $types = [], array $filter = []): array {
    $relationships = $this->relationships;
    if ($types) {
      $relationships = array_intersect_key(
        $relationships,
        array_flip($types)
      );
    }

    $field_definitions = $this->entityFieldManager
      ->getFieldDefinitions('node', $mapping->getTargetBundle());
    foreach ($field_definitions as $field_name => $field_definition) {
      $schema_property = $mapping->getSchemaPropertyMapping($field_name, TRUE);
      if (!$schema_property) {
        continue;
      }

      $relationship_type = $this->getRelationshipType($schema_property, $field_definition);
      if (!isset($relationships[$relationship_type])) {
        continue;
      }

      if (isset($filter[$relationship_type]) && !in_array($schema_property, $filter[$relationship_type])) {
        continue;
      }

      $relationships[$relationship_type][$field_name] = $schema_property;
    }

    return $relationships;
  }

  /**
   * Get Schema.org relationships.
   *
   * @return array
   *   Schema.org relationships.
   */
  protected function getRelationships(): array {
    /** @var \Drupal\node\Entity\NodeType[] $node_types */
    $node_types = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple();

    $relationships = [];
    foreach ($node_types as $bundle => $node_type) {
      $mapping = $this->loadMapping('node', $bundle);
      if ($mapping) {
        $mapping_relationships = $this->getMappingRelationships($mapping);
        foreach ($mapping_relationships as $relationship_type => $mapping_relationship) {
          $relationships += [$relationship_type => []];
          $relationships[$relationship_type] += $mapping_relationship;
        }
      }
    }
    return $relationships;
  }

  /**
   * Gets the relationship type for a given Schema.org property and field definition.
   *
   * @param string $schema_property
   *   The Schema.org property.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   *
   * @return string|null
   *   The relationship type, or NULL if none is found.
   */
  protected function getRelationshipType(string $schema_property, FieldDefinitionInterface $field_definition): ?string {
    $field_type = $field_definition->getType();
    if (str_contains($field_type, 'entity_reference')) {
      $target_type = $field_definition->getSetting('target_type');
      if (in_array($schema_property, $this->hierarchyProperties)) {
        return 'hierarchical';
      }
      elseif (in_array($target_type, ['taxonomy_term', 'media'])) {
        return $target_type;
      }
      else {
        return 'reference';
      }
    }
    elseif (str_starts_with($field_type, 'list')) {
      return 'enumeration';
    }
    elseif ($field_type === 'link') {
      return 'link';
    }
    return NULL;
  }

  /**
   * Get Schema.org relationship types.
   *
   * @return array
   *   An array of Schema.org relationship types.
   */
  protected function getRelationshipTypes(): array {
    return [
      'hierarchical' => [
        'singular' => $this->t('Hierarchical'),
        'plural' => $this->t('Hierarchical'),
      ],
      'reference' => [
        'singular' => $this->t('Reference'),
        'plural' => $this->t('References'),
      ],
      'link' => [
        'singular' => $this->t('Link'),
        'plural' => $this->t('Links'),
      ],
      'enumeration' => [
        'singular' => $this->t('Enumeration'),
        'plural' => $this->t('Enumerations'),
      ],
      'taxonomy_term' => [
        'singular' => $this->t('Taxonomy'),
        'plural' => $this->t('Taxonomies'),
      ],
      'media' => [
        'singular' => $this->t('Media'),
        'plural' => $this->t('Media'),
      ],
    ];
  }

}
