<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\Core\Url;

/**
 * Returns responses for Schema.org report references routes.
 */
class SchemaDotOrgReportReferencesController extends SchemaDotOrgReportControllerBase {

  /**
   * Builds the Schema.org references.
   *
   * @return array
   *   A renderable array containing the Schema.org references.
   */
  public function index(): array {
    $config = $this->config('schemadotorg_report.settings');

    $build = [];

    // About.
    $about = $config->get('about');
    if ($about) {
      $build['about'] = [
        'title' => [
          '#markup' => $this->t('About'),
          '#prefix' => '<h2>',
          '#suffix' => '</h2>',
        ],
        'links' => [
          '#theme' => 'item_list',
          '#items' => $this->buildReportLinks($about),
        ],
      ];
    }

    // Links to references and issues/discussions.
    $links = [
      'types' => $this->t('References'),
      'issues' => $this->t('Issues/Discussions'),
    ];
    foreach ($links as $name => $title) {
      $type_links = $config->get($name);
      if ($type_links) {
        $build[$name]['title'] = [
          '#markup' => $title,
          '#prefix' => '<h2>',
          '#suffix' => '</h2>',
        ];
        $build[$name]['types'] = [];
        foreach ($type_links as $type => $links) {
          $build[$name]['types'][$type] = [
            '#theme' => 'item_list',
            '#title' => [
              '#type' => 'link',
              '#title' => $type,
              '#url' => Url::fromRoute('schemadotorg_report', ['id' => $type]),
              '#attributes' => ['id' => $type],
            ],
            '#items' => $this->buildReportLinks($links),
          ];
        }
      }
    }

    return $build;
  }

}
