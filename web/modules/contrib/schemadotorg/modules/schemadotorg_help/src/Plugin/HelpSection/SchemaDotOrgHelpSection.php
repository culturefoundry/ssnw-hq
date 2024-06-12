<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_help\Plugin\HelpSection;

use Drupal\Core\Extension\ExtensionLifecycle;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\help\Plugin\HelpSection\HelpSectionPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Schema.org Blueprints section for the help page.
 *
 * @HelpSection(
 *   id = "schemadotorg",
 *   title = @Translation("Schema.org Blueprints"),
 *   weight = 20,
 *   description = @Translation("The Schema.org Blueprints module uses Schema.org as the blueprint for the content architecture and structured data in a Drupal website."),
 *   permission = "access help pages"
 * )
 */
final class SchemaDotOrgHelpSection extends HelpSectionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The module extension list.
   */
  protected ModuleExtensionList $moduleExtensionList;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->moduleExtensionList = $container->get('extension.list.module');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function listTopics() {
    $modules = array_filter(
      $this->moduleExtensionList->getAllInstalledInfo(),
      fn(array $info) => str_starts_with($info['package'], 'Schema.org Blueprints')
    );
    ksort($modules);

    $topics = [];
    foreach ($modules as $module_name => $module_info) {
      $title = $module_info['name'];
      $title = str_replace('Schema.org Blueprints ', '', $title);
      $url = Url::fromRoute('schemadotorg_help.page', ['name' => $module_name]);
      $topics[$module_name] = Link::fromTextAndUrl($title, $url)->toRenderable();
      if ($module_info[ExtensionLifecycle::LIFECYCLE_IDENTIFIER] === ExtensionLifecycle::EXPERIMENTAL) {
        $topics[$module_name]['#suffix'] = ' <span class="schemadotorg-help-experimental">(' . $this->t('Experimental') . ')<span>';
      }
    }
    // Bold the core Schema.org Blueprints module's help page.
    $topics['schemadotorg']['#prefix'] = '<strong>';
    $topics['schemadotorg']['#suffix'] = '</strong>';
    return $topics;
  }

}
