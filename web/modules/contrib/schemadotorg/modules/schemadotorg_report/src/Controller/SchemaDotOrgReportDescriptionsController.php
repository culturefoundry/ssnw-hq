<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\Utility\SchemaDotOrgStringHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Schema.org report descriptions routes.
 */
class SchemaDotOrgReportDescriptionsController extends SchemaDotOrgReportControllerBase {

  /**
   * The route match.
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->routeMatch = $container->get('current_route_match');
    return $instance;
  }

  /**
   * Builds the Schema.org types or properties descriptions.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param string $table
   *   Schema.org types and properties table.
   *
   * @return array
   *   A renderable array containing Schema.org types or properties
   *   descriptions.
   */
  public function index(Request $request, string $table): array {
    $id = $request->query->get('id');
    $descriptions_installed = $this->moduleHandler()->moduleExists('schemadotorg_descriptions');

    // Header.
    $header = [];
    $header['label'] = [
      'data' => $this->t('Label'),
      'width' => '2 0%',
    ];
    $header['comment'] = [
      'data' => $this->t('Default description'),
      'width' => '35%',
    ];
    if ($descriptions_installed) {
      $header['custom_description'] = [
        'data' => $this->t('Custom description'),
        'width' => '35%',
      ];
    }
    $header['links'] = [
      'data' => $this->t('Has links'),
      'width' => '10%',
    ];

    // Base query.
    $base_query = $this->database->select('schemadotorg_' . $table, $table);
    $base_query->fields($table, ['label', 'comment']);
    $base_query->orderBy('label');
    if ($id) {
      $or = $base_query->orConditionGroup()
        ->condition('label', '%' . $id . '%', 'LIKE')
        ->condition('comment', '%' . $id . '%', 'LIKE');
      $base_query->condition($or);
    }

    // Total.
    $total_query = clone $base_query;
    $count = $total_query->countQuery()->execute()->fetchField();

    // Result.
    $result_query = clone $base_query;
    $result = $result_query->execute();

    // Rows.
    $default_types = $this->config('schemadotorg.settings')
      ->get('schema_types.default_types');

    $custom_descriptions = $this->config('schemadotorg_descriptions.settings')
      ->get('custom_descriptions') ?: [];
    $rows = [];
    while ($record = $result->fetchAssoc()) {
      $label = $record['label'];
      $comment = $default_types[$label]['description'] ?? $record['comment'];
      $custom_description = $custom_descriptions[$label] ?? '';
      $has_links = str_contains(SchemaDotOrgStringHelper::getFirstSentence($custom_description ?: $comment), '<a');

      $row = [];
      $row['label'] = $this->buildTableCell('label', $label);
      $row['comment'] = $this->buildTableCell('comment', $comment);
      if ($descriptions_installed) {
        $row['custom_description'] = [
          'data' => [
            '#markup' => $this->schemaTypeBuilder->formatComment($custom_description),
          ],
        ];
      }
      $row['has_links'] = $has_links ? $this->t('Yes') : $this->t('No');

      if ($custom_description) {
        $rows[] = ['data' => $row, 'class' => ['color-success']];
      }
      elseif ($has_links) {
        $rows[] = ['data' => $row, 'class' => ['color-warning']];
      }
      else {
        $rows[] = $row;
      }
    }

    $t_args = [
      '@type' => ($table === 'types') ? $this->t('types') : $this->t('properties'),
    ];

    $build = parent::buildHeader($table);

    $build['info'] = $this->buildInfo($table, $count);
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#sticky' => TRUE,
      '#empty' => $this->t('No @type found.', $t_args),
      '#attributes' => ['class' => ['schemadotorg-report-table']],
    ];
    $build['pager'] = [
      '#type' => 'pager',
      // Use the <current> route to make sure pager links works as expected
      // in a modal.
      // @see Drupal.behaviors.schemaDotOrgDialog
      '#route_name' => '<current>',
    ];
    return $build;
  }

}
