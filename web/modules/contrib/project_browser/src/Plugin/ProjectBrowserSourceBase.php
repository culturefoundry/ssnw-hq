<?php

namespace Drupal\project_browser\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\project_browser\ProjectBrowser\ProjectsResultsPage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines an abstract base class for a Project Browser source.
 *
 * @see \Drupal\project_browser\Annotation\ProjectBrowserSource
 * @see \Drupal\project_browser\Plugin\ProjectBrowserSourceManager
 * @see plugin_api
 */
abstract class ProjectBrowserSourceBase extends PluginBase implements ProjectBrowserSourceInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
  }

  /**
   * Returns the available sort options that plugins will parse.
   *
   * @return array
   *   Options offered.
   */
  public function getSortOptions(): array {
    return [
      'usage_total' => [
        'id' => 'usage_total',
        'text' => $this->t('Most Popular'),
      ],
      'a_z' => [
        'id' => 'a_z',
        'text' => $this->t('A-Z'),
      ],
      'z_a' => [
        'id' => 'z_a',
        'text' => $this->t('Z-A'),
      ],
      'created' => [
        'id' => 'created',
        'text' => $this->t('Newest First'),
      ],
      'best_match' => [
        'id' => 'best_match',
        'text' => $this->t('Most Relevant'),
      ],
    ];
  }

  /**
   * Creates a page of results (projects) to send to the client side.
   *
   * @param \Drupal\project_browser\ProjectBrowser\Project[] $results
   *   The projects to list on the page.
   * @param bool $package_manager_required
   *   Whether Package Manager is required for these projects.
   * @param int|null $total_results
   *   (optional) The total number of results. Defaults to the size of $results.
   *
   * @return \Drupal\project_browser\ProjectBrowser\ProjectsResultsPage
   *   A list of projects to send to the client.
   */
  protected function createResultsPage(array $results, bool $package_manager_required, ?int $total_results = NULL): ProjectsResultsPage {
    return new ProjectsResultsPage(
      $total_results ?? count($results),
      array_values($results),
      (string) $this->getPluginDefinition()['label'],
      $this->getPluginId(),
      $package_manager_required,
    );
  }

}
