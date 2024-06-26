<?php

declare(strict_types=1);

namespace Drupal\project_browser;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\project_browser\ProjectBrowser\Project;
use Symfony\Component\HttpFoundation\Response;

/**
 * An activator for Drupal modules.
 */
final class ModuleActivator implements ActivatorInterface {

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly ModuleInstallerInterface $moduleInstaller,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isActive(Project $project): bool {
    return $this->moduleHandler->moduleExists($project->machineName);
  }

  /**
   * {@inheritdoc}
   */
  public function supports(Project $project): bool {
    // At the moment, Project Browser only supports modules, so all projects can
    // be handled by this activator.
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function activate(Project $project): ?Response {
    $this->moduleInstaller->install([$project->machineName]);
    return NULL;
  }

}
