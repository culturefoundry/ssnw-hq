<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mapping_set\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgBuildTrait;
use Drupal\schemadotorg_mapping_set\SchemaDotOrgMappingSetManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Schema.org Blueprints Mapping Sets routes.
 */
class SchemadotorgMappingSetController extends ControllerBase {
  use SchemaDotOrgBuildTrait;

  /**
   * The Schema.org names manager.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org schema type builder.
   */
  protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder;

  /**
   * The Schema.org mapping manager service.
   */
  protected SchemaDotOrgMappingManagerInterface $schemaMappingManager;

  /**
   * The Schema.org mapping set manager service.
   */
  protected SchemaDotOrgMappingSetManagerInterface $schemaMappingSetManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    $instance->schemaMappingSetManager = $container->get('schemadotorg_mapping_set.manager');
    return $instance;
  }

  /**
   * Builds the response for the mapping sets overview page.
   */
  public function overview(): array {
    // Header.
    $header = [
      'title' => ['data' => $this->t('Title / Name'), 'width' => '30%'],
      'setup' => ['data' => $this->t('Setup'), 'width' => '10%'],
      'types' => ['data' => $this->t('Types'), 'width' => '50%'],
      'operations' => ['data' => $this->t('Operations'), 'width' => '10%'],
    ];

    // Rows.
    $rows = [];
    $mapping_sets = $this->config('schemadotorg_mapping_set.settings')->get('sets');
    foreach ($mapping_sets as $name => $mapping_set) {
      $is_setup = $this->schemaMappingSetManager->isSetup($name);

      // Types.
      $invalid_types = [];
      $types = $mapping_set['types'];
      foreach ($types as $index => $type) {
        if ($this->getMappingStorage()->isValidType($type)) {
          $mapping = $this->getMappingStorage()->loadByType($type);
          if ($mapping) {
            $entity_type_bundle = $mapping->getTargetEntityBundleEntity();
            $types[$index] = $entity_type_bundle->toLink($type, 'edit-form')->toString();
          }
        }
        else {
          $invalid_types[] = $type;
          $types[$index] = '<strong>' . $type . '</strong>';
        }
      }

      $view_url = Url::fromRoute('schemadotorg_mapping_set.details', ['name' => $name]);
      $row = [];
      $row['title'] = [
        'data' => [
          'link' => [
            '#type' => 'link',
            '#title' => $mapping_set['label'],
            '#url' => $view_url,
          ],
          'name' => [
            '#markup' => ' (' . $name . ')',
          ],
        ],
      ];
      $row['setup'] = $is_setup ? $this->t('Yes') : $this->t('No');
      $row['types'] = ['data' => ['#markup' => implode(', ', $types)]];
      // Only show operation when there are no invalid types.
      if (!$invalid_types) {
        $operations = $this->getOperations($name);
        $operations['view'] = [
          'title' => $this->t('View details'),
          'url' => $view_url,
        ];
        $row['operations'] = [
          'data' => [
            '#type' => 'operations',
            '#links' => $operations,
          ],
          'style' => 'white-space: nowrap',
        ];
      }
      else {
        $row['operations'] = '';
      }

      if ($invalid_types) {
        $rows[] = ['data' => $row, 'class' => ['color-error']];
      }
      elseif ($is_setup) {
        $rows[] = ['data' => $row, 'class' => ['color-success']];
      }
      else {
        $rows[] = $row;
      }

      // Display error message able invalid types.
      if ($invalid_types) {
        $t_args = [
          '%set' => $mapping_set['label'],
          '%types' => implode(', ', $invalid_types),
          ':href' => Url::fromRoute('schemadotorg_mapping_set.settings')->toString(),
        ];
        $message = $this->t('%types in %set are not valid. <a href=":href">Please update this information.</a>', $t_args);
        $this->messenger()->addError($message);
      }
    }

    return [
      'table' => [
        '#type' => 'table',
        '#sticky' => TRUE,
        '#header' => $header,
        '#rows' => $rows,
      ],
    ];
  }

  /**
   * Builds the response for the mapping set detail page.
   */
  public function details(string $name): array {
    $mapping_set = $this->config('schemadotorg_mapping_set.settings')->get("sets.$name");
    if (empty($mapping_set)) {
      throw new NotFoundHttpException();
    }

    $build = [];
    $build['#title'] = $this->t('@label Schema.org mapping set', ['@label' => $mapping_set['label']]);
    $build['summary'] = $this->buildSummary($name);
    $build['details'] = $this->buildDetails($name, 'view');
    return $build;
  }

  /**
   * Build a mapping set's summary.
   *
   * @param string $name
   *   The mapping set's name.
   *
   * @return array
   *   A renderable array containing a mapping set's summary.
   */
  public function buildSummary(string $name): array {
    $mapping_set = $this->config('schemadotorg_mapping_set.settings')->get("sets.$name");
    foreach ($mapping_set['types'] as $type) {
      if (!$this->getMappingStorage()->isValidType($type)) {
        continue;
      }
      [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);
      $mapping = $this->getMappingStorage()->loadByType($type);
      $mapping_defaults = $this->schemaMappingManager->getMappingDefaultsByType($type);
      if ($mapping) {
        $status = $this->t('Exists');

        $operation = $mapping->getTargetEntityBundleEntity()
          ->toLink($this->t('Edit type'), 'edit-form')
          ->toRenderable();
      }
      else {
        $status = [
          'data' => [
            '#markup' => $this->t('Missing'),
            '#prefix' => '<em>',
            '#suffix' => '</em>',
          ],
        ];

        $bundle_entity_type = $this->entityTypeManager
          ->getDefinition($entity_type_id)
          ->getBundleEntityType();
        $route_name = "schemadotorg.{$bundle_entity_type}.type_add";
        $route_options = [
          'query' => ['type' => $schema_type] + $this->getRedirectDestination()->getAsArray(),
        ];
        $url = Url::fromRoute($route_name, [], $route_options);
        $operation = Link::fromTextAndUrl($this->t('Add type'), $url)->toRenderable();
      }

      $row = [];
      $row['schema_type'] = $schema_type;
      if (!empty($mapping_defaults['additional_mappings'])) {
        $row['schema_type'] .= ' (' . implode(', ', array_keys($mapping_defaults['additional_mappings'])) . ')';
      }
      $row['entity_type'] = [
        'data' => [
          'label' => [
            '#markup' => $mapping_defaults['entity']['label'],
            '#prefix' => '<strong>',
            '#suffix' => '</strong> (' . $entity_type_id . ')<br/>',
          ],
          'comment' => [
            '#markup' => $mapping_defaults['entity']['description'],
          ],
        ],
      ];
      $row['status'] = $status;
      $row['operations'] = [
        'data' => $operation + [
          '#attributes' => [
            'class' => ['button', 'button--extrasmall'],
          ],
        ],
        'style' => 'white-space: nowrap',
      ];

      $rows[] = [
        'data' => $row,
        'class' => [
          ($mapping) ? 'color-success' : 'color-warning',
        ],
      ];
    }

    // Append operations as the last row in the table.
    $rows[] = [
      ['colspan' => 3],
      'operations' => [
        'data' => [
          '#type' => 'operations',
          '#links' => $this->getOperations($name, ['query' => $this->getRedirectDestination()->getAsArray()]),
        ],
        'style' => 'white-space: nowrap',
      ],
    ];

    $header = [
      'schema_type' => ['data' => $this->t('Schema.org type(s)'), 'width' => '15%'],
      'entity_type' => ['data' => $this->t('Entity label (type) / description'), 'width' => '65%'],
      'status' => ['data' => $this->t('Status'), 'width' => '10%'],
      'operation' => ['data' => $this->t('Operations'), 'width' => '10%'],
    ];

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
  }

  /**
   * Build a mapping set's details.
   *
   * @param string $name
   *   The mapping set's name.
   * @param string $operation
   *   The current operation.
   *
   * @return array
   *   A renderable array containing a mapping set's details.
   */
  public function buildDetails(string $name, string $operation = 'view'): array {
    $mapping_set = $this->config('schemadotorg_mapping_set.settings')->get("sets.$name");

    $build = [];
    foreach ($mapping_set['types'] as $type) {
      if (!$this->getMappingStorage()->isValidType($type)) {
        continue;
      }

      [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);
      $mapping = $this->getMappingStorage()->loadByType($type);
      $mapping_defaults = $this->schemaMappingManager->getMappingDefaultsByType($type);
      $details = $this->buildSchemaType($type, $mapping_defaults);
      switch ($operation) {
        case 'view':
          $details['#title'] .= ' - ' . ($mapping ? $this->t('Exists') : '<em>' . $this->t('Missing') . '</em>');
          $details['#summary_attributes']['class'] = [($mapping) ? 'color-success' : 'color-warning'];
          break;

        case 'setup':
          $details['#title'] .= ' - ' . ($mapping ? $this->t('Exists') : '<em>' . $this->t('Creating') . '</em>');
          $details['#summary_attributes']['class'] = [($mapping) ? 'color-success' : 'color-warning'];
          break;

        case 'teardown':
          $mapping_sets = $this->schemaMappingSetManager->getMappingSets($entity_type_id, $schema_type, TRUE);
          if (count($mapping_sets) > 1) {
            unset($mapping_sets[$name]);
            $labels = array_map(
              fn($mapping_set) => $mapping_set['label'],
              $mapping_sets
            );
            $t_args = ['%labels' => implode(', ', $labels)];
            $details['#title'] .= ' - ' . $this->t('Used by %labels', $t_args);
            $details['#summary_attributes']['class'] = ['color-warning'];
          }
          break;
      }
      $build[$type] = $details;
    }
    return $build;
  }

  /**
   * Get a mapping set's operations based on its status.
   *
   * @param string $name
   *   The name of the mapping set.
   * @param array $options
   *   An array of route options.
   *
   * @return array
   *   A mapping set's operations based on its status.
   */
  protected function getOperations(string $name, array $options = []): array {
    $operations = [];

    $is_setup = $this->schemaMappingSetManager->isSetup($name);
    if (!$is_setup) {
      $operations['setup'] = $this->t('Setup types');
    }
    else {
      if ($this->moduleHandler()->moduleExists('devel_generate')) {
        $operations['generate'] = $this->t('Generate content');
        $operations['kill'] = $this->t('Kill content');
      }
      $operations['teardown'] = $this->t('Teardown types');
    }
    foreach ($operations as $operation => $title) {
      $operations[$operation] = [
        'title' => $title,
        'url' => Url::fromRoute(
          'schemadotorg_mapping_set.confirm_form',
          ['name' => $name, 'operation' => $operation],
          $options
        ),
      ];
    }

    return $operations;
  }

}
