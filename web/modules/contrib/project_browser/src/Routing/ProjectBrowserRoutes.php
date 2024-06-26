<?php

namespace Drupal\project_browser\Routing;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\project_browser\Controller\InstallerController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Project Browser.
 *
 * @internal
 *   Routing callbacks are internal.
 */
class ProjectBrowserRoutes implements ContainerInjectionInterface {

  /**
   * Constructor for project browser routes.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   */
  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
    );
  }

  /**
   * Returns an array of route objects.
   *
   * @return \Symfony\Component\Routing\Route[]
   *   An array of route objects.
   */
  public function routes(): array {
    if (!$this->moduleHandler->moduleExists('package_manager')) {
      return [];
    }
    $routes = [];
    $routes['project_browser.stage.begin'] = new Route(
      '/admin/modules/project_browser/install-begin/{source}/{project_id}',
      [
        '_controller' => InstallerController::class . '::begin',
        '_title' => 'Create phase',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.stage.require'] = new Route(
      '/admin/modules/project_browser/install-require/{source}/{project_id}',
      [
        '_controller' => InstallerController::class . '::require',
        '_title' => 'Require phase',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.stage.apply'] = new Route(
      '/admin/modules/project_browser/install-apply',
      [
        '_controller' => InstallerController::class . '::apply',
        '_title' => 'Apply phase',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.stage.post_apply'] = new Route(
      '/admin/modules/project_browser/install-post_apply',
      [
        '_controller' => InstallerController::class . '::postApply',
        '_title' => 'Post apply phase',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.stage.destroy'] = new Route(
      '/admin/modules/project_browser/install-destroy',
      [
        '_controller' => InstallerController::class . '::destroy',
        '_title' => 'Destroy phase',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.activate'] = new Route(
      '/admin/modules/project_browser/activate/{source}/{project_id}',
      [
        '_controller' => InstallerController::class . '::activate',
        '_title' => 'Install module in core',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.module.install_in_progress'] = new Route(
      '/admin/modules/project_browser/install_in_progress/{source}/{project_id}',
      [
        '_controller' => InstallerController::class . '::inProgress',
        '_title' => 'Install in progress',
      ],
      [
        '_permission' => 'administer modules',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    $routes['project_browser.install.unlock'] = new Route(
      '/admin/modules/project_browser/install/unlock',
      [
        '_controller' => InstallerController::class . '::unlock',
        '_title' => 'Unlock',
      ],
      [
        '_permission' => 'administer modules',
        '_csrf_token' => 'TRUE',
        '_custom_access' => InstallerController::class . '::access',
      ],
    );
    return $routes;
  }

}
