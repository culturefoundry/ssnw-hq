<?php

namespace Drupal\project_browser\ProjectBrowser;

/**
 * One page of search results from a query.
 */
class ProjectsResultsPage implements \JsonSerializable {

  /**
   * Constructor for project browser results page.
   *
   * @param int $totalResults
   *   Total number of results.
   * @param \Drupal\project_browser\ProjectBrowser\Project[] $list
   *   A numerically indexed array of projects.
   * @param string $pluginLabel
   *   The source plugin's label.
   * @param string $pluginId
   *   The source plugin's ID.
   * @param bool $isPackageManagerRequired
   *   True if Package Manager is required.
   */
  public function __construct(
    public readonly int $totalResults,
    public readonly array $list,
    public readonly string $pluginLabel,
    public readonly string $pluginId,
    public readonly bool $isPackageManagerRequired,
  ) {
    assert(array_is_list($list));
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize(): array {
    $values = get_object_vars($this);

    $map = function (Project $project): object {
      $serialized = $project->jsonSerialize();
      $serialized->id = $this->pluginId . '/' . $project->id;
      return $serialized;
    };
    $values['list'] = array_map($map, $values['list']);

    return $values;
  }

}
