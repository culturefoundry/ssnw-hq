<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Utility;

use Drupal\Core\Render\Element;

/**
 * Helper class Schema.org element methods.
 */
class SchemaDotOrgElementHelper {

  /**
   * Set #parents property for child elements.
   *
   * @param array &$elements
   *   The elements.
   * @param array $parents
   *   The parents for the child elements.
   */
  public static function setElementParents(array &$elements, array $parents): void {
    foreach (Element::children($elements) as $key) {
      $elements[$key]['#parents'] = array_merge($parents, [$key]);
    }
  }

}
