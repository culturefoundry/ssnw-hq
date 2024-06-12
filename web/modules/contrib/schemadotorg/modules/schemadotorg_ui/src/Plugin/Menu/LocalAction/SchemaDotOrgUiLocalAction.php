<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Plugin\Menu\LocalAction;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Menu\LocalActionDefault;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a local action plugin.
 */
class SchemaDotOrgUiLocalAction extends LocalActionDefault {

  /**
   * {@inheritdoc}
   */
  public function getOptions(RouteMatchInterface $route_match): array {
    $options = parent::getOptions($route_match);
    if (isset($this->pluginDefinition['dialog'])) {
      $options['attributes'] = [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => $this->pluginDefinition['dialog'],
        ]),
      ];
    }
    return $options;
  }

}
