<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\Core\Database\Query\PagerSelectExtender;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Schema.org report table routes.
 */
class SchemaDotOrgReportTableController extends SchemaDotOrgReportControllerBase {

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
   * Builds the Schema.org types or properties documentation.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param string $table
   *   Schema.org types and properties table.
   *
   * @return array
   *   A renderable array containing Schema.org types or properties
   *   documentation.
   */
  public function index(Request $request, string $table): array {
    $id = $request->query->get('id');

    // Header.
    $header = ($table === 'types')
      ? $this->getTypesHeader()
      : $this->getPropertiesHeader();

    // Base query.
    $base_query = $this->database->select('schemadotorg_' . $table, $table);
    $base_query->fields($table, array_keys($header));
    $base_query->orderBy('label');
    if ($id) {
      $or = $base_query->orConditionGroup()
        ->condition('label', '%' . $id . '%', 'LIKE')
        ->condition('comment', '%' . $id . '%', 'LIKE');
      $base_query->condition($or);
    }

    // Add conditions based on route name.
    switch ($this->routeMatch->getRouteName()) {
      case 'schemadotorg_report.properties.inverse_of':
        $base_query->condition('inverse_of', '', '<>');
        break;

      case 'schemadotorg_report.properties.identifier':
        $base_query->condition('sub_property_of', 'https://schema.org/identifier');
        break;
    }

    // Total.
    $total_query = clone $base_query;
    $count = $total_query->countQuery()->execute()->fetchField();

    // Result.
    $result_query = clone $base_query;
    /** @var \Drupal\Core\Database\Query\PagerSelectExtender $result_query */
    $result_query = $result_query->extend(PagerSelectExtender::class);
    $result_query = $result_query->limit(100);
    $result = $result_query->execute();

    // Rows.
    $rows = [];
    while ($record = $result->fetchAssoc()) {
      $row = [];
      foreach ($record as $name => $value) {
        $row[$name] = $this->buildTableCell($name, $value);
      }
      $rows[] = $row;
    }

    $t_args = [
      '@type' => ($table === 'types') ? $this->t('types') : $this->t('properties'),
    ];

    $build = parent::buildHeader($table);
    if (!$this->isAjax()) {
      $build['filter'] = $this->getFilterForm($table, $id);
    }

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

  /**
   * Gets Schema.org types table header.
   *
   * @return array[]
   *   Schema.org types table header.
   */
  protected function getTypesHeader(): array {
    return [
      'label' => [
        'data' => $this->t('Label'),
      ],
      'comment' => [
        'data' => $this->t('Comment'),
      ],
      'enumerationtype' => [
        'data' => $this->t('Enumeration type'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'sub_types' => [
        'data' => $this->t('Sub types'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];
  }

  /**
   * Gets properties table header.
   *
   * @return array[]
   *   Properties table header.
   */
  protected function getPropertiesHeader(): array {
    return [
      'label' => [
        'data' => $this->t('Label'),
      ],
      'comment' => [
        'data' => $this->t('Comment'),
      ],
      'inverse_of' => [
        'data' => $this->t('Inverse of'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'domain_includes' => [
        'data' => $this->t('Domain includes'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'range_includes' => [
        'data' => $this->t('Range includes'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'sub_property_of' => [
        'data' => $this->t('Sub property of'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];
  }

}
