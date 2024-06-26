<?php

declare(strict_types=1);

namespace Drupal\project_browser;

use Drupal\project_browser\ProjectBrowser\Project;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines an interface for services which can activate projects.
 *
 * An activator is the "source of truth" about the state of a particular project
 * in the current site -- for example, an activator that handles modules knows
 * if the module is already installed.
 */
interface ActivatorInterface {

  /**
   * Determines if a particular project is activated on the current site.
   *
   * @param \Drupal\project_browser\ProjectBrowser\Project $project
   *   A project to check.
   *
   * @return bool
   *   TRUE if the project is activated on the current site, FALSE otherwise.
   */
  public function isActive(Project $project): bool;

  /**
   * Determines if this activator can handle a particular project.
   *
   * For example, an activator that handles themes might return TRUE from this
   * method if the project's Composer package type is `drupal-theme`.
   *
   * @param \Drupal\project_browser\ProjectBrowser\Project $project
   *   A project to check.
   *
   * @return bool
   *   TRUE if this activator is responsible for the given project, FALSE
   *   otherwise.
   */
  public function supports(Project $project): bool;

  /**
   * Activates a project on the current site.
   *
   * @param \Drupal\project_browser\ProjectBrowser\Project $project
   *   The project to activate.
   *
   * @return \Symfony\Component\HttpFoundation\Response|null
   *   Optionally, a response that should be presented to the user in Project
   *   Browser. This could be a set of additional instructions to display in a
   *   modal, for example, or a redirect to a configuration form.
   */
  public function activate(Project $project): ?Response;

}
