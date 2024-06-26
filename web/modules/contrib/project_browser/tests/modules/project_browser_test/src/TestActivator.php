<?php

declare(strict_types=1);

namespace Drupal\project_browser_test;

use Drupal\Core\State\StateInterface;
use Drupal\project_browser\ActivatorInterface;
use Drupal\project_browser\ProjectBrowser\Project;
use Symfony\Component\HttpFoundation\Response;

/**
 * A test activator that simply logs a state message.
 */
class TestActivator implements ActivatorInterface {

  public function __construct(
    private readonly ActivatorInterface $decorated,
    private readonly StateInterface $state,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function supports(Project $project): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive(Project $project): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function activate(Project $project): ?Response {
    $this->state->set('test activator', "$project->title was activated!");
    return $this->decorated->activate($project);
  }

}
