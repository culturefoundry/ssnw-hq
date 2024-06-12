<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Schema.org report relationships routes.
 */
class SchemaDotOrgReportRelationshipsController extends SchemaDotOrgReportControllerBase {

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
  ];

  /**
   * An array of relationship Schema.org properties.
   */
  protected array $relationshipProperties = [
    'about',
    'contactPoint',
    'department',
    'employee',
    'healthCondition',
    'member',
    'memberOf',
    'relatedDrug',
    'study',
    'subjectOf',
    'workLocation',
    'worksFor',
  ];

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * The starter kit manager.
   */
  protected ?SchemaDotOrgStarterkitManagerInterface $starterKitManager = NULL;

  /**
   * A mapping for starter kit type to starter kit names.
   */
  protected array $starterKitTypes = [];

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->entityFieldManager = $container->get('entity_field.manager');

    if ($container->has('schemadotorg_starterkit.manager')) {
      $instance->starterKitManager = $container->get('schemadotorg_starterkit.manager');

      $starterkits = $instance->starterKitManager->getStarterkits(TRUE);
      foreach ($starterkits as $module_name => $starterkit) {
        $settings = $instance->starterKitManager->getStarterkitSettings($module_name);
        foreach (array_keys($settings['types']) as $type) {
          $instance->starterKitTypes += [$type => []];
          $instance->starterKitTypes[$type][$module_name] = str_replace('Schema.org Blueprints Starter Kit: ', '', $starterkit['name']);
        }
      }
    }

    return $instance;
  }

  /**
   * Builds a table containing Schema.org relationships.
   *
   * @return array
   *   A renderable array containing a table containing Schema.org relationships.
   */
  public function index(): array {
    // Header.
    $header = [];
    $header['label'] = [
      'data' => $this->t('Label'),
      'style' => 'min-width: 200px',
    ];
    $header['id'] = [
      'data' => $this->t('ID'),
      'style' => 'min-width: 100px',
    ];
    $header['description'] = [
      'data' => $this->t('Description'),
      'style' => 'min-width: 400px',
    ];
    if ($this->starterKitManager) {
      $header['starterkit'] = [
        'data' => $this->t('Starter kit'),
        'style' => 'min-width: 100px',
      ];
    }
    $header['type'] = [
      'data' => $this->t('Schema.org type'),
      'style' => 'min-width: 100px',
    ];
    $header['hierarchy'] = [
      'data' => $this->t('Hierarchy'),
      'style' => 'min-width: 100px',
    ];
    $header['relationships'] = [
      'data' => $this->t('Relationships'),
      'style' => 'min-width: 100px',
    ];
    $header['enumerations'] = [
      'data' => $this->t('Enumerations'),
      'style' => 'min-width: 100px',
    ];
    $header['taxonomy_term'] = [
      'data' => $this->t('Taxonomy'),
      'style' => 'min-width: 100px',
    ];
    $header['media'] = [
      'data' => $this->t('Media'),
      'style' => 'min-width: 100px',
    ];

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager()
      ->getStorage('schemadotorg_mapping');
    /** @var \Drupal\node\Entity\NodeType[] $node_types */
    $node_types = $this->entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();
    $rows = [];
    foreach ($node_types as $bundle => $node_type) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
      $mapping = $mapping_storage->load("node.$bundle");
      if (!$mapping) {
        continue;
      }

      $row = [];
      $row['label'] = [
        'data' => $node_type->toLink(NULL, 'edit-form')->toRenderable(),
      ];
      $row['id'] = [
        'data' => ['#markup' => $node_type->id()],
      ];
      $row['description'] = [
        'data' => ['#markup' => $node_type->getDescription()],
      ];
      if ($this->starterKitManager) {
        $row['starterkit'] = [
          'data' => [
            '#theme' => 'item_list',
            '#items' => $this->getStarterKits($mapping),
          ],
        ];
      }
      $row['type'] = [
        'data' => [
          '#theme' => 'item_list',
          '#items' => $this->schemaTypeBuilder->buildItemsLinks(
            $mapping->getAllSchemaTypes(),
            ['prefix' => NULL]
          ),
        ],
      ];
      $relationships = $this->getRelationships($mapping);
      foreach ($relationships as $relationship_type => $relationship_items) {
        $row[$relationship_type] = [
          'data' => [
            '#theme' => 'item_list',
            '#items' => $this->schemaTypeBuilder->buildItemsLinks(
              $relationship_items,
              ['prefix' => NULL]
            ),
          ],
        ];
      }
      $rows[] = $row;
    }

    $build = [];
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#sticky' => TRUE,
      '#empty' => $this->t('No content types found.'),
      '#attributes' => ['class' => ['schemadotorg-report-table']],
    ];
    $build['#attached']['library'][] = 'schemadotorg_report/schemadotorg_report';
    return $build;
  }

  /**
   * Get installed starter kits that sets up the Schema.org mapping type.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   THe Schema.org mapping.
   *
   * @return array
   *   Starter kits that sets up the Schema.org mapping type.
   */
  protected function getStarterKits(SchemaDotOrgMappingInterface $mapping): array {
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $schema_type = $mapping->getSchemaType();

    $starterkits = [];
    $starterkits += ($this->starterKitTypes["$entity_type_id:$bundle:$schema_type"] ?? []);
    $starterkits += ($this->starterKitTypes["$entity_type_id:$schema_type"] ?? []);
    return $starterkits;
  }

  /**
   * Gets the relationships based on a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return array
   *   An array of relationships.
   */
  protected function getRelationships(SchemaDotOrgMappingInterface $mapping): array {
    $relationships = [
      'hierarchy' => [],
      'relationships' => [],
      'enumerations' => [],
      'taxonomy_term' => [],
      'media' => [],
    ];

    $schema_properties = $mapping->getAllSchemaProperties();
    $field_definitions = $this->entityFieldManager
      ->getFieldDefinitions('node', $mapping->getTargetBundle());
    foreach ($field_definitions as $field_name => $field_definition) {
      $schema_property = $schema_properties[$field_name] ?? NULL;
      if (!$schema_property) {
        continue;
      }

      if (str_contains($field_definition->getType(), 'entity_reference')) {
        $target_type = $field_definition->getSetting('target_type');
        if (in_array($schema_property, $this->hierarchyProperties)) {
          $relationships['hierarchy'][] = $schema_property;
        }
        elseif (in_array($schema_property, $this->relationshipProperties)) {
          $relationships['relationships'][] = $schema_property;
        }
        elseif (isset($relationships[$target_type])) {
          $relationships[$target_type][] = $schema_property;
        }
        else {
          $relationships['relationships'][] = $schema_property;
        }
      }
      elseif (str_starts_with($field_definition->getType(), 'list')) {
        $relationships['enumerations'][] = $schema_property;
      }
    }

    return $relationships;
  }

}
