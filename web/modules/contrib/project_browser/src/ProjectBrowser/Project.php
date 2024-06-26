<?php

namespace Drupal\project_browser\ProjectBrowser;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Utility\Xss;

/**
 * Defines a single Project.
 */
class Project implements \JsonSerializable {

  /**
   * The unqualified project ID.
   *
   * @var string
   */
  public readonly string $id;

  /**
   * Constructs a Project object.
   *
   * @param array $logo
   *   Logo of the project.
   * @param bool $isCompatible
   *   Whether the project is compatible with the current version of Drupal.
   * @param bool $isMaintained
   *   Whether the project is considered to be maintained or not.
   * @param bool $isCovered
   *   Whether the project is considered to be covered or not.
   * @param bool $isActive
   *   Whether the project is considered to be active or not.
   * @param int $starUserCount
   *   User start count of the project.
   * @param int $projectUsageTotal
   *   Total usage of the project.
   * @param string $machineName
   *   Value of project_machine_name of the project.
   * @param array $body
   *   Body field of the project in array format.
   * @param string $title
   *   Title of the project.
   * @param int $status
   *   Status of the project.
   * @param int $changed
   *   When was the project changed last timestamp.
   * @param int $created
   *   When was the project created last timestamp.
   * @param array $author
   *   Author of the project in array format.
   * @param string $packageName
   *   The Composer package name of this project, e.g. `drupal/project_browser`.
   * @param string $url
   *   URL of the project.
   * @param array $categories
   *   Value of module_categories of the project.
   * @param array $images
   *   Images of the project.
   * @param array $warnings
   *   Warnings for the project.
   * @param string $type
   *   The project type. Defaults to 'module:drupalorg' to indicate modules from
   *   D.O., but may be changed to anything else that could helpfully identify
   *   a project type.
   * @param string|bool $commands
   *   When FALSE, the project browser UI will not provide a "View Commands"
   *   button for the project UNLESS the type 'module:drupalorg', in which case
   *   it displays Svelte-generated install instructions.
   *   When it is a string and NOT 'module:drupalorg', that string will become
   *   the contents of the "View Commands" popup.
   *   To include a paste-able command that includes a copy button, use this
   *   markup structure:
   *
   *   @code
   *   <div class="command-box">
   *     <input value="THE_COMMAND_TO_BE_COPIED" readonly="" />
   *     <button data-copy-command>
   *       <img src="/PATH_TO_PROJECT_BROWSER/images/copy-icon.svg\" alt="ALT TEXT"/>
   *     </button>
   *   </div>
   *  @endcode
   * @param string $id
   *   (optional) The unqualified project ID. Cannot contain a slash. Defaults
   *   to the machine name.
   */
  public function __construct(
    public array $logo,
    public bool $isCompatible,
    public bool $isMaintained,
    public bool $isCovered,
    public bool $isActive,
    public int $starUserCount,
    public int $projectUsageTotal,
    public string $machineName,
    private array $body,
    public string $title,
    public int $status,
    public int $changed,
    public int $created,
    public array $author,
    public string $packageName,
    public string $url = '',
    public array $categories = [],
    public array $images = [],
    public array $warnings = [],
    public string $type = 'module:drupalorg',
    public string|bool $commands = FALSE,
    string $id = '',
  ) {
    $this->setSummary($body);
    // @see \Drupal\project_browser\ProjectBrowser\ProjectsResultsPage::jsonSerialize()
    // @see \Drupal\project_browser\Routing\ProjectBrowserRoutes::routes()
    if (str_contains($id, '/')) {
      throw new \InvalidArgumentException("Project IDs cannot contain slashes.");
    }
    $this->id = $id ? $id : $machineName;
  }

  /**
   * Set the project short description.
   *
   * @param array $body
   *   Body in array format.
   *
   * @return $this
   */
  public function setSummary(array $body) {
    $this->body = $body;
    if (empty($this->body['summary'])) {
      $this->body['summary'] = $this->body['value'] ?? '';
    }
    $this->body['summary'] = Html::escape(strip_tags($this->body['summary']));
    $this->body['summary'] = Unicode::truncate($this->body['summary'], 200, TRUE, TRUE);
    return $this;
  }

  /**
   * Returns the selector id of the project.
   *
   * @return string
   *   Selector id of the project.
   */
  public function getSelectorId(): string {
    return str_replace('_', '-', $this->machineName);
  }

  /**
   * {@inheritdoc}
   */
  #[\ReturnTypeWillChange]
  public function jsonSerialize() {
    return (object) [
      'is_compatible' => $this->isCompatible,
      'is_covered' => $this->isCovered,
      'project_usage_total' => $this->projectUsageTotal,
      'module_categories' => $this->categories,
      'project_machine_name' => $this->machineName,
      'project_images' => $this->images,
      'logo' => $this->logo,
      'body' => $this->body,
      'title' => $this->title,
      'author' => $this->author,
      'warnings' => $this->warnings,
      'package_name' => $this->packageName,
      // @todo Not used in Svelte. Audit in https://www.drupal.org/i/3309273.
      'is_maintained' => $this->isMaintained,
      'is_active' => $this->isActive,
      'flag_project_star_user_count' => $this->starUserCount,
      'url' => $this->url,
      'status' => $this->status,
      'changed' => $this->changed,
      'created' => $this->created,
      'selector_id' => $this->getSelectorId(),
      'type' => $this->type,
      'commands' => Xss::filter($this->commands, [...Xss::getAdminTagList(), 'input', 'button']),
    ];
  }

}
