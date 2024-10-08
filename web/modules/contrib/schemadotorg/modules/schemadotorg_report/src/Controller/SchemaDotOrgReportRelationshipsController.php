<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\schemadotorg_report\Traits\SchemaDotOrgReportRelationshipsTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Schema.org report relationships routes.
 */
class SchemaDotOrgReportRelationshipsController extends SchemaDotOrgReportControllerBase {
  use SchemaDotOrgReportRelationshipsTrait;

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->entityFieldManager = $container->get('entity_field.manager');
    return $instance;
  }

  /**
   * Returns the title of the Schema.org relationship diagram.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The translated title of the Schema.org relationship diagram.
   */
  public function title(Request $request): TranslatableMarkup {
    $label = NULL;
    if ($request->query->get('category')) {
      $category = $request->query->get('category');
      $label = $this->config('schemadotorg.settings')
        ->get('schema_types.categories.' . $category . '.label');
    }
    elseif ($request->query->get('bundle')) {
      $node_type = $this->entityTypeManager()
        ->getStorage('node_type')
        ->load($request->query->get('bundle'));
      $label = ($node_type) ? $node_type->label() : NULL;
    }
    elseif ($request->query->get('property')) {
      $label = $request->query->get('property');
    }

    return ($label)
      ? $this->t('Schema.org: Relationships: @label', ['@label' => $label])
      : $this->t('Schema.org: Relationships');
  }

  /**
   * Builds a table containing Schema.org relationships.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable array containing a table containing Schema.org relationships.
   */
  public function overview(Request $request): array {
    // Header.
    $header = [];
    $header['category'] = [
      'data' => $this->t('Category'),
      'style' => 'min-width: 100px',
    ];
    $header['bundle'] = [
      'data' => $this->t('Content type'),
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
    $header['type'] = [
      'data' => $this->t('Schema.org type'),
      'style' => 'min-width: 100px',
    ];
    $relationship_types = $this->getRelationshipTypes();
    foreach ($relationship_types as $relationship_type => $relationship_label) {
      $header[$relationship_type] = [
        'data' => $relationship_label['plural'],
        'style' => 'min-width: 100px',
      ];
    }

    // Rows.
    $rows = [];
    $node_types = $this->loadNodeTypes($request);
    foreach ($node_types as $bundle => $node_type) {
      $mapping = $this->loadMapping('node', $bundle);
      $category = $this->getMappingCategory($mapping);

      $row = [];
      $row['category'] = [
        'data' => ['#markup' => $category['label']],
      ];
      $row['bundle'] = [
        'data' => $node_type->toLink(NULL, 'edit-form')->toRenderable(),
      ];
      $row['id'] = [
        'data' => ['#markup' => $node_type->id()],
      ];
      $row['description'] = [
        'data' => ['#markup' => $node_type->getDescription()],
      ];
      $row['type'] = [
        'data' => [
          '#theme' => 'item_list',
          '#items' => $this->schemaTypeBuilder->buildItemsLinks(
            $mapping->getAllSchemaTypes(),
            ['prefix' => NULL]
          ),
        ],
      ];
      $relationships = $this->getMappingRelationships($mapping);
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

      $category_name = $category['name'];
      $rows["$category_name--$bundle"] = [
        'data' => $row,
        'style' => 'background-color:' . $category['color'] . ';border-top: 2px solid #333',
      ];
    }
    ksort($rows);

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
   * Builds a table containing Schema.org relationship targets.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable array containing a table containing Schema.org relationship targets.
   */
  public function targets(Request $request): array {
    // Header.
    $header = [];
    $header['category'] = [
      'data' => $this->t('Category'),
      'style' => 'min-width: 100px',
    ];
    $header['bundle'] = [
      'data' => $this->t('Content type'),
      'style' => 'min-width: 200px',
    ];
    $header['schema_type'] = [
      'data' => $this->t('Schema.org type'),
      'style' => 'min-width: 100px',
    ];
    $header['schema_property'] = [
      'data' => $this->t('Schema.org property'),
      'style' => 'min-width: 100px',
    ];
    $header['relationship_type'] = [
      'data' => $this->t('Relationship type'),
      'style' => 'min-width: 100px',
    ];
    $header['unlimited'] = [
      'data' => $this->t('Unlimited values'),
      'style' => 'min-width: 100px',
    ];
    $header['required'] = [
      'data' => $this->t('Required field'),
      'style' => 'min-width: 100px',
    ];
    $header['targets'] = [
      'data' => $this->t('Target types'),
      'style' => 'min-width: 100px',
    ];
    $header['allowed_values'] = [
      'data' => $this->t('Allowed values'),
      'style' => 'min-width: 100px',
    ];

    $relationship_types = $this->getRelationshipTypes();
    $node_types = $this->loadNodeTypes($request);

    if ($request->query->get('category') || $request->query->get('bundle') || $request->query->get('property')) {
      $filter = [];
    }
    else {
      $filter = ($request->query->all())
        ? $request->query->all() + $this->relationships
        : [];
    }

    $rows = [];
    foreach ($node_types as $bundle => $node_type) {
      $mapping = $this->loadMapping('node', $bundle);
      $category = $this->getMappingCategory($mapping);

      $field_definitions = $this->entityFieldManager
        ->getFieldDefinitions('node', $mapping->getTargetBundle());

      $relationships = $this->getMappingRelationships(
        mapping: $mapping,
        filter: $filter,
      );
      foreach ($relationships as $relationship_type => $relationship_items) {
        foreach ($relationship_items as $field_name => $schema_property) {
          if ($request->query->get('property')
            && $request->query->get('property') !== $schema_property) {
            continue;
          }

          $field_definition = $field_definitions[$field_name];

          $row = [];
          $row['category'] = [
            'data' => [
              '#type' => 'link',
              '#title' => $category['label'],
              '#url' => Url::fromRoute('<current>', [], ['query' => ['category' => $category['name']]]),
            ],
          ];
          $row['bundle'] = [
            'data' => [
              '#type' => 'link',
              '#title' => $node_type->label(),
              '#url' => Url::fromRoute('<current>', [], ['query' => ['bundle' => $bundle]]),
            ],
          ];
          $row['schema_type'] = [
            'data' => [
              '#theme' => 'item_list',
              '#items' => $this->schemaTypeBuilder->buildItemsLinks(
                $mapping->getAllSchemaTypes(),
                ['prefix' => NULL]
              ),
            ],
          ];
          $row['schema_property'] = [
            'data' => [
              '#type' => 'link',
              '#title' => $schema_property,
              '#url' => Url::fromRoute('<current>', [], ['query' => ['property' => $schema_property]]),
            ],
          ];
          $row['relationship_type'] = $relationship_types[$relationship_type]['singular'];
          $row['unlimited'] = $field_definition->getFieldStorageDefinition()->getCardinality() ? $this->t('Yes') : $this->t('No');
          $row['required'] = $field_definition->isRequired() ? $this->t('Yes') : $this->t('No');
          $row['targets'] = [
            'data' => [
              '#theme' => 'item_list',
              '#items' => $this->getTargets($field_definition),
            ],
          ];
          $row['allowed_values'] = [
            'data' => [
              '#theme' => 'item_list',
              '#items' => options_allowed_values($field_definition->getFieldStorageDefinition()) ?: [],
            ],
          ];

          $category_name = $category['name'];
          $rows["$category_name--$bundle--$relationship_type--$schema_property"] = [
            'data' => $row,
            'style' => 'background-color:' . $category['color'],
          ];
        }
      }
    }
    ksort($rows);

    $current_bundle = NULL;
    foreach ($rows as $key => &$row) {
      $bundle = explode('--', $key)[1];
      if ($current_bundle !== $bundle) {
        $row['style'] .= ';border-top: 2px solid #333';
        $current_bundle = $bundle;
      }
    }

    $build = [];
    $build['filter'] = $this->formBuilder()->getForm('\Drupal\schemadotorg_report\Form\SchemaDotOrgReportRelationshipsFilterForm');
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
   * Builds Schema.org relationship diagram.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable array containing Schema.org relationship diagram.
   */
  public function diagram(Request $request): array {
    /** @var \Drupal\node\Entity\NodeType[] $node_types */
    $node_types = $this->entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();
    foreach ($node_types as $bundle => $node_type) {
      if (!$this->loadMapping('node', $bundle)) {
        unset($node_types[$bundle]);
      }
    }

    if ($request->query->get('category') || $request->query->get('bundle')) {
      $filter = [];
    }
    else {
      $filter = ($request->query->all())
        ? $request->query->all() + $this->relationships
        : [];
    }

    $inverse_relationships = $this->getInverseOfRelationships();

    // Build selected types and connectors.
    $diagram_types = [];
    $diagram_connectors = [];
    foreach ($this->loadNodeTypes($request) as $bundle => $node_type) {
      $diagram_types[$bundle] = $bundle . '[' . $node_type->label() . ']';

      $mapping = $this->loadMapping('node', $bundle);

      $field_definitions = $this->entityFieldManager
        ->getFieldDefinitions('node', $bundle);

      $relationships = $this->getMappingRelationships(
        mapping: $mapping,
        types: ['hierarchical', 'reference'],
        filter: $filter,
      );
      foreach ($relationships as $relationship_type => $items) {
        // If there are no filters (a.k.a., query), exclude some Schema.org
        // properties from the default diagram.
        if (empty($filter)) {
          $items = array_diff($items, $this->getDiagramExcludedSchemaProperties());
        }

        // Determine connector based on the relationship type.
        $diagram_connector = ($relationship_type === 'hierarchical')
          ? '--'
          : '-.-';

        foreach ($items as $field_name => $schema_property) {
          $field_definition = $field_definitions[$field_name];
          $has_role = ($field_definition->getType() === 'entity_reference_override');
          $target_bundles = array_keys($this->getTargets($field_definition));
          foreach ($target_bundles as $target_bundle) {
            $target_field_definitions = $this->entityFieldManager
              ->getFieldDefinitions('node', $target_bundle);

            $target_node_type = $node_types[$target_bundle];
            $diagram_types[$target_bundle] = $target_bundle . '[' . $target_node_type->label() . ']';

            $target_mapping = $this->loadMapping('node', $target_bundle);
            $inverse_schema_property = $inverse_relationships[$schema_property] ?? '';
            $target_field_name = $target_mapping->getSchemaPropertyFieldName($inverse_schema_property, TRUE);
            if ($inverse_schema_property
              && $target_field_name) {
              // Get predictable sortable key for inverse relationships.
              $inverse_bundles = [$bundle, $target_bundle];
              asort($inverse_bundles);
              $inverse_properties = [$schema_property, $inverse_schema_property];
              asort($inverse_properties);
              $key = $relationship_type . implode('--', $inverse_bundles)
                . '--'
                . implode('--', $inverse_properties);
              // Get connector label.
              $target_has_role = (isset($target_field_definitions[$target_field_name])
                && $target_field_definitions[$target_field_name]->getType() === 'entity_reference_override');
              $connector_label = implode('/', $inverse_properties)
                . (($has_role || $target_has_role) ? ' + ' . $this->t('Role') : '');
              $diagram_connectors[$key] = $bundle . '<' . $diagram_connector . '>|' . $connector_label . '|' . $target_bundle;
            }
            else {
              // Get sortable key.
              $key = "$relationship_type--$bundle--$schema_property--$target_bundle";
              // Get connector label.
              $connector_label = $schema_property
                . (($has_role) ? ' + ' . $this->t('Role') : '');
              $diagram_connectors[$key] = $bundle . $diagram_connector . '>|' . $connector_label . '|' . $target_bundle;
            }
          }
        }
      }
    }

    // Set styles and categories based on the diagram types.
    $categories = $this->config('schemadotorg.settings')
      ->get('schema_types.categories');
    $diagram_styles = [];
    $diagram_links = [];

    $category_categories = array_fill_keys(array_keys($categories), NULL);
    $category_links = [];
    $category_styles = [];
    foreach (array_keys($diagram_types) as $bundle) {
      $mapping = $this->loadMapping('node', $bundle);

      $category = $this->getMappingCategory($mapping);
      $category_name = $category['name'];

      $url = Url::fromRoute('<current>', [], ['query' => ['bundle' => $bundle]]);
      $diagram_links[$bundle] = 'click ' . $bundle . ' "' . $url->toString() . '"';
      $diagram_styles[$bundle] = 'style ' . $bundle . ' fill:' . $category['color'] . ',stroke:#000,stroke-width:2px';

      $url = Url::fromRoute('<current>', [], ['query' => ['category' => $category_name]]);
      $category_categories[$category_name] = $category_name . '[' . $category['label'] . ']';
      $category_links[$category_name] = 'click ' . $category_name . ' "' . $url->toString() . '"';
      $category_styles[$category_name] = 'style ' . $category_name . ' fill:' . $category['color'] . ',stroke:#000';
    }
    $category_categories = array_values(array_filter($category_categories));

    // Build Mermaid.js code.
    $subgraphs = (isset($filter['subgraphs']))
      ? (bool) $filter['subgraphs']
      : TRUE;
    $mermaid_diagram = $this->buildMermaidCode(
      'flowchart TD',
      '',
      $this->buildDiagramTypes($diagram_types, $subgraphs),
      '',
      $diagram_connectors,
      '',
      $diagram_links,
      '',
      $diagram_styles,
    );

    $mermaid_categories = $this->buildMermaidCode(
      'flowchart LR',
      '',
      'subgraph "' . $this->t('Categories') . '"',
      preg_filter('/^/', '  ', $category_categories),
      'end',
      '',
      $category_links,
      '',
      $category_styles,
    );

    $build = [];
    // Filter relationships.
    $build['filter'] = $this->formBuilder()->getForm('\Drupal\schemadotorg_report\Form\SchemaDotOrgReportRelationshipsFilterForm', 'diagram');
    // Mermaid.js diagram.
    if ($diagram_types) {
      $build['mermaid']['diagram'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['mermaid', 'schemadotorg-mermaid', 'schemadotorg-report-relationships-diagram']],
        '#markup' => $mermaid_diagram,
      ];
      $build['mermaid']['categories'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['mermaid', 'schemadotorg-mermaid', 'schemadotorg-report-relationships-categories']],
        '#markup' => $mermaid_categories,
      ];
      $build['mermaid']['code'] = [
        '#type' => 'details',
        '#title' => $this->t('Mermaid code'),
        'code' => [
          '#type' => 'html_tag',
          '#tag' => 'pre',
          '#plain_text' => $this->buildMermaidCode(
            '# Diagram',
            $mermaid_diagram,
            '',
            '# Categories',
            $mermaid_categories,
          ),
        ],
      ];
      $build['#attached']['library'][] = 'schemadotorg/schemadotorg.mermaid';
      $build['#attached']['library'][] = 'schemadotorg/svg-pan-zoom';
    }
    else {
      $build['mermaid'] = [
        '#markup' => $this->t('No content types were found that meet current filter criteria. Please adjust or reset your filters.'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }
    $build['#attached']['library'][] = 'schemadotorg_report/schemadotorg_report';
    return $build;
  }

  /**
   * Load node types.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Drupal\node\Entity\NodeType[]
   *   Node types for the current request.
   */
  protected function loadNodeTypes(Request $request): array {
    $query = $request->query->all();

    $bundles = $query['bundle'] ?? $query['bundles'] ?? NULL;
    $ids = (isset($bundles))
      ? $this->entityTypeManager()
        ->getStorage('node_type')
        ->getQuery()
        ->condition('type', (array) $bundles, 'IN')
        ->execute()
      : NULL;

    /** @var \Drupal\node\Entity\NodeType[] $node_types */
    $node_types = $this->entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple($ids);

    $categories = $query['category'] ?? $query['categories'] ?? NULL;
    if ($categories) {
      $categories = (array) $categories;
    }
    foreach ($node_types as $bundle => $node_type) {
      $mapping = $this->loadMapping('node', $bundle);
      if (!$mapping) {
        unset($node_types[$bundle]);
        continue;
      }

      $category = $this->getMappingCategory($mapping);
      if (isset($categories)
        && !in_array($category['name'], $categories)) {
        unset($node_types[$bundle]);
      }
    }
    return $node_types;
  }

  /**
   * Get the target Schema.org types for a given field definition.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   *
   * @return array
   *   An array of target Schema.org types.
   */
  protected function getTargets(FieldDefinitionInterface $field_definition): array {
    $settings = $field_definition->getSettings();
    $target_bundles = NestedArray::getValue($settings, ['handler_settings', 'target_bundles']);
    if (!$target_bundles) {
      return [];
    }

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager()
      ->getStorage('schemadotorg_mapping');
    $ids = $mapping_storage
      ->getQuery()
      ->condition('target_entity_type_id', 'node')
      ->condition('target_bundle', $target_bundles, 'IN')
      ->sort('schema_type')
      ->execute();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
    $mappings = $mapping_storage->loadMultiple($ids);

    $targets = [];
    foreach ($mappings as $mapping) {
      $targets[$mapping->getTargetBundle()] = $mapping->getTargetEntityBundleEntity()->label()
        . ' (' . $mapping->getSchemaType() . ')';
    }
    return $targets;
  }

  /**
   * Get Schema.org inverse of relationships.
   *
   * @return array
   *   An associative containing Schema.org inverse of relationships.
   */
  protected function getInverseOfRelationships(): array {
    // Get inverse relationships from Schema.org properties table.
    $inverse_relationships = [];
    $result = $this->database
      ->select('schemadotorg_properties', 'properties')
      ->fields('properties', ['label', 'inverse_of'])
      ->condition('inverse_of', '', '<>')
      ->orderBy('label')
      ->execute();
    while ($record = $result->fetchAssoc()) {
      $schema_property = $record['label'];
      $inverse_of = str_replace('https://schema.org/', '', $record['inverse_of']);
      $inverse_relationships[$schema_property] = $inverse_of;
    }
    $inverse_relationships += array_flip($inverse_relationships);

    // Get custom inverse relationships from Schema.org Blueprints
    // Corresponding Entity References module.
    if ($this->moduleHandler()->moduleExists('schemadotorg_cer')) {
      $schema_properties = $this->config('schemadotorg_cer.settings')
        ->get('default_schema_properties');
      $inverse_relationships += $schema_properties;
      $inverse_relationships += array_flip($schema_properties);
    }
    return $inverse_relationships;
  }

  /**
   * Builds Schema.org relationship diagram for content types.
   *
   * @param array $types
   *   An array of content types.
   * @param bool $display_subgraphs
   *   Whether to display subgraphs.
   *
   * @return array
   *   An array containing Schema.org relationship diagram for content types.
   */
  protected function buildDiagramTypes(array $types, bool $display_subgraphs = TRUE): array {
    // Get categories with 'other' category.
    $categories = $this->configFactory
      ->get('schemadotorg.settings')
      ->get('schema_types.categories');
    $categories['other'] = [
      'label' => $this->t('Other'),
      'types' => ['Thing'],
    ];

    // Build categories settings used to look up a type's category and weight.
    $settings = [];
    $weight = 0;
    foreach ($categories as $category_name => $category_definition) {
      foreach ($category_definition['types'] as $type) {
        $settings[$type] = [
          'category' => $category_name,
          'weight' => $weight,
        ];
        $weight++;
      }
    }

    // Create an array of types sorted by category and weight.
    $sort_types = [];
    foreach ($types as $type => $code) {
      $mapping = $this->loadMapping('node', $type);
      $sort_types[$type] = $this->schemaTypeManager->getSetting($settings, $mapping);
      $sort_types[$type]['code'] = $code;
    }
    usort($sort_types, fn($a, $b) => $a['weight'] - $b['weight']);

    if ($display_subgraphs) {
      // Return a categories as subgraph with sorted types.
      $code = [];
      $current_category = NULL;
      foreach ($sort_types as $type_definition) {
        if ($current_category !== $type_definition['category']) {
          $category_name = $type_definition['category'];
          if ($current_category) {
            $code[] = 'end';
            $code[] = '';
          }
          $code[] = 'subgraph "' . $categories[$category_name]['label'] . '"';
          $current_category = $category_name;
        }
        $code[] = '  ' . $type_definition['code'];
      }
      $code[] = 'end';
      return $code;
    }
    else {
      // Return the types sorted by category and weight.
      return array_column($sort_types, 'code');
    }
  }

  /**
   * Build Mermaid.js diagram from the functions array of arguments.
   *
   * @return string
   *   A Mermaid.js diagram.
   */
  protected function buildMermaidCode(): string {
    $lines = func_get_args();
    foreach ($lines as $index => $line) {
      if (is_array($line) && !isset($line[0])) {
        sort($line);
      }
      $lines[$index] = (array) $line;
    }
    return implode(PHP_EOL, array_merge(...$lines));
  }

}
