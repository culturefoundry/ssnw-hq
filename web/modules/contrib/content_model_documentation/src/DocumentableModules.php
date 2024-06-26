<?php

namespace Drupal\content_model_documentation;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Service providing information on the Documentable Modules.
 */
class DocumentableModules {

  use CMDocumentConnectorTrait;
  use StringTranslationTrait;

  /**
   * Module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory service.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extension_list_module
   *   The module extension list.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(
    ConfigFactoryInterface $configFactory,
    ModuleExtensionList $extension_list_module,
    ModuleHandlerInterface $module_handler) {
    $this->config = $configFactory->get('content_model_documentation.settings');
    $this->moduleExtensionList = $extension_list_module;
    $this->moduleHandler = $module_handler;
  }

  /**
   * Gets an array of all modules.
   *
   * Disabled and enabled modules are included because we may want to document
   * a module that we keep around even when disabled.
   *
   * @return array
   *   Modules listed for documentation.
   */
  public function getDocumentableModulesSelectList(): array {
    $module_list = [];
    // Elements should follow the pattern 'machine_name' => 'name'.
    $extensions = $this->moduleExtensionList->getList();
    foreach ($extensions as $extension) {
      if ($extension->info['type'] === 'module') {
        $project = $this->getProject($extension);
        if ($this->isSubmodule($extension)) {
          // This is likely a submodule.
          $module_list["module.{$project}.{$extension->getName()}"] = "module.{$project}.{$extension->getName()}";
        }
        elseif (!empty($project)) {
          // The project is the module.
          $module_list["module.{$project}"] = "module.{$project}";
        }
      }
    }

    $module_list = $this->removeModules($module_list);
    asort($module_list, SORT_NATURAL);

    return $module_list;
  }

  /**
   * Get the project name (machine name).
   *
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module).
   *
   * @return string
   *   The machine name of the project or empty string if not found.
   */
  protected function getProject(Extension $extension) {
    return (!empty($extension->info['project'])) ? $extension->info['project'] : $extension->origin;
  }

  /**
   * Get the submodule name (machine name).
   *
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module).
   *
   * @return string
   *   The submodule name of the project or empty string if there is none.
   */
  protected function getSubmodule(Extension $extension) {
    if ($this->isSubmodule($extension)) {
      return $extension->getName();
    }
    return '';
  }

  /**
   * Checks if the extension is a submodule.
   *
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module).
   *
   * @return bool
   *   TRUE if it is a submodule, FALSE otherwise.
   */
  protected function isSubmodule(Extension $extension): bool {
    if ($extension->info['type'] === 'module') {
      return (isset($extension->info['project']) && $extension->getName() !== $extension->info['project']) || $this->getProject($extension) === 'core';
    }
    return FALSE;
  }

  /**
   * Get the info for reporting on enabled modules.
   *
   * @param bool $csv
   *   Indicates it is for csv so use paths instead of links.
   * @param string|bool $domain
   *   A fully qualified scheme and domain [https://example-site.com].
   *
   * @return array
   *   A keyed array of module properties.
   */
  public function getEnabledReporting(bool $csv = FALSE, $domain = FALSE): array {
    $modules = [];
    // We never return disabled modules because that would be a security risk
    // since the report we show may not require the same user as module admin.
    $extensions = $this->moduleExtensionList->getList();
    ksort($extensions, SORT_NATURAL);

    foreach ($extensions as $machine_name => $extension) {
      if ($extension->getType() === 'module' && $extension->status) {
        $project_link = $this->buildProjectUrl($extension, $csv, $domain);
        $machine_display = $machine_name;
        if ($this->isSubmodule($extension)) {
          $machine_display .= " ({$this->t('submodule')})";
        }

        $module = [
          'name' => "{$extension->info['name']} ({$extension->info['version']})",
          'machine_name' => $machine_display,
          'description' => $extension->info['description'] ?? '',
          'project' => (!empty($project_link)) ? $project_link : '?',
          'help' => $this->buildHelpUrl($machine_name, $extension, $csv, $domain),
        ];
        if ($this->config->get('modules')) {
          // Modules are documentable, so include the links to documentation.
          if ($csv) {
            // Make a path.
            $module['cm_document'] = $this->buildCmDocumentPath($machine_name, $extension, $domain);
          }
          else {
            // Make a link.
            $module['cm_document'] = $this->buildCmDocumentUrl($machine_name, $extension);
          }

        }
        $modules[] = $module;
      }
    }

    return $modules;
  }

  /**
   * Builds a link to the Drupal.org project page.
   *
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module) that needs a help link.
   * @param bool $csv
   *   Indicates it is for csv so use paths instead of links.
   * @param string|bool $domain
   *   A fully qualified scheme and domain [https://example-site.com].
   *
   * @return link|string
   *   The link to the project page, or empty string if does not exist.
   */
  protected function buildProjectUrl(Extension $extension, $csv = FALSE, $domain = FALSE) {
    $project = $this->getProject($extension);
    // Sometimes project is empty, as in the case of a non-tagged release.
    $link_text = $project ?: $extension->getName();
    if ($project === 'core') {
      // Build a core Url.
      // @todo Figure out where this should go.  Documentation is version
      // specific so may be problematic trying to link to it.
    }
    elseif (!empty($extension->subpath) && (strpos($extension->subpath, 'modules/custom') === 0)) {
      // This is a bit of an assumption on where custom should appear.
      $link_text = $this->t('Custom, see your repository.');
    }
    else {
      $machine_name = $extension->info["project"] ?? $extension->getName();
      $destination = (empty($machine_name)) ? '' : "https://www.drupal.org/project/{$machine_name}";
    }

    if (!empty($destination)) {
      $url = Url::fromUri($destination);
      if (empty($csv)) {
        $link = Link::fromTextAndUrl($link_text, $url);
      }
      // If link was not built, then csv will just get the destination.
      return $link ?? $destination;
    }
    else {
      // There is no destination so no link.
      return $link_text;
    }

  }

  /**
   * Builds a link to the internal cm document if it exists.
   *
   * @param string $machine_name
   *   The machine name of the module.
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module) that needs a help link.
   *
   * @return link|string
   *   The link to the CM Document, or empty string if does not exist.
   */
  protected function buildCmDocumentUrl($machine_name, Extension $extension) {
    if (!empty($machine_name)) {
      $project = $this->getProject($extension);
      $link = $this->getCmDocumentLink('module', $project, $machine_name);
    }

    return $link ?? '';
  }

  /**
   * Builds a link to the internal cm document if it exists.
   *
   * @param string $machine_name
   *   The machine name of the module.
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module) that needs a help link.
   * @param string|bool $domain
   *   A fully qualified scheme and domain [https://example-site.com].
   *
   * @return link|string
   *   The link to the CM Document, or empty string if does not exist.
   */
  protected function buildCmDocumentPath($machine_name, Extension $extension, $domain = FALSE) {
    if (!empty($machine_name)) {
      $project = $this->getProject($extension);
      $link = $this->getVerifiedCmDocumentPath('module', $project, $machine_name, $domain);
    }
    return $link ?? '';
  }

  /**
   * Builds a link to the internal module help page if one is defined.
   *
   * @param string $machine_name
   *   The machine name of the module.
   * @param \Drupal\Core\Extension\Extension $extension
   *   The extension (module) that needs a help link.
   * @param bool $csv
   *   Indicates it is for csv so use paths instead of links.
   * @param string|bool $domain
   *   A fully qualified scheme and domain [https://example-site.com].
   *
   * @return link|string
   *   The link to the help, or empty string if does not exist.
   */
  protected function buildHelpUrl($machine_name, Extension $extension, $csv = FALSE, $domain = FALSE) {
    if (!empty($machine_name)) {
      // Generate link for module's help page. Assume that if a hook_help()
      // implementation exists then the module provides an overview page, rather
      // than checking to see if the page exists, which is costly.
      $help_exists = ($this->moduleHandler->moduleExists('help') && $extension->status && $this->moduleHandler->hasImplementations('help', $extension->getName()));
      if ($help_exists) {
        $url = Url::fromRoute('help.page', ['name' => $extension->getName()]);
        if ($csv) {
          $path = $url->toString();
          $path = ($domain) ? $domain . $path : $path;
        }
        else {
          $link = Link::fromTextAndUrl($this->t('Help'), $url);
        }
      }
    }

    return $path ?? $link ?? '';
  }

  /**
   * Removes modules that should not be revealed to non-admins (devel, etc).
   *
   * @param array $documentable_modules
   *   An array of key value pairs to have modules removed from.
   *
   * @return array
   *   An array of key value pairs for the module that are documentable.
   */
  protected function removeModules(array $documentable_modules): array {
    $remove_these = [
      'module.devel' => NULL,
    ];

    return array_diff_key($documentable_modules, $remove_these);
  }

}
