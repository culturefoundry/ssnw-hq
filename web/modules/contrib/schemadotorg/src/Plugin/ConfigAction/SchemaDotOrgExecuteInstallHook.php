<?php

namespace Drupal\schemadotorg\Plugin\ConfigAction;

use Drupal\Core\Config\Action\Attribute\ConfigAction;
use Drupal\Core\Config\Action\ConfigActionException;
use Drupal\Core\Config\Action\ConfigActionPluginInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Config action to execute a module's install hook with $is_syncing set to FALSE.
 *
 * You can call 'executeInstallHook' with a module prefix
 * or explicitly list the module install hooks that should be triggered.
 *
 * <code>
 * # Use a module prefix for executing install hooks.
 * config:
 *   actions:
 *     core.extension:
 *       executeInstallHook: schemadotorg
 * </code>
 *
 * <code>
 * # Explicitly list of modules for executing install hooks.
 * config:
 *   actions:
 *     core.extension:
 *       executeInstallHook:
 *         - schemadotorg_media
 *         - schemadotorg_taxonomy
 * </code>
 */
#[ConfigAction(
  id: 'executeInstallHook',
  admin_label: new TranslatableMarkup("Execute a module's install hook with \$is_syncing set to FALSE."),
)]
class SchemaDotOrgExecuteInstallHook implements ConfigActionPluginInterface, ContainerFactoryPluginInterface {

  /**
   * The module handler.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Rhe state storage service.
   */
  protected StateInterface $state;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = new static();
    $instance->moduleHandler = $container->get('module_handler');
    $instance->state = $container->get('state');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function apply(string $configName, mixed $value): void {
    if ($configName !== 'core.extension') {
      throw new ConfigActionException("The 'executeInstallHook' config action can only be triggered via core.extension.");
    }

    // Installed hooks are tracked to ensure that they are only triggered once.
    // @see schemadotorg_modules_uninstalled()
    $installed_hooks = $this->state->get('schemadotorg.installed_hooks') ?? [];

    if (is_string($value)) {
      $install_modules = [];
      foreach (array_keys($this->moduleHandler->getModuleList()) as $module) {
        if (str_starts_with($module, $value)) {
          $install_modules[] = $module;
        }
      };
    }
    elseif (is_array($value)) {
      $install_modules = $value;
    }
    else {
      throw new ConfigActionException("Unexpected value passed to the 'executeInstallHook' config action.");
    }

    foreach ($install_modules as $module) {
      if (isset($installed_hooks[$module])) {
        continue;
      }

      $this->moduleHandler->loadInclude($module, 'install');

      $hook_install = $module . '_install';
      if (!function_exists($hook_install)) {
        continue;
      }

      $hook_install_function = new \ReflectionFunction($hook_install);
      if (empty($hook_install_function->getParameters())) {
        continue;
      }

      $hook_install(FALSE);

      $installed_hooks[$module] = $module;
    }
    $this->state->set('schemadotorg.installed_hooks', $installed_hooks);
  }

}
