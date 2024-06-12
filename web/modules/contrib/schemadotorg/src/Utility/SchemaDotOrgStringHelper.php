<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Utility;

use Drupal\Component\Render\MarkupInterface;

/**
 * Helper class Schema.org string methods.
 */
class SchemaDotOrgStringHelper {

  /**
   * Get first sentence from text.
   *
   * @param string $text
   *   The text.
   *
   * @return string
   *   The first sentence from the text.
   */
  public static function getFirstSentence(string $text): string {
    if (!$text || !str_contains($text, '.')) {
      return $text;
    }

    $text = preg_replace('#\s*\(source: Wikipedia, [^)]+\)#', '', $text);

    $text = preg_replace_callback(
      '#(etc\.|\w\.\w\.?|\.\.\.|[ \(]vs\. |(\d*\.)?\d+|https?://[^\s"\)]+)#',
      fn($matches) => str_replace('.', '%2E', $matches[0]),
      $text
    );

    if (str_contains($text, '.')) {
      $text = substr($text, 0, strpos($text, '.') + 1);
    }

    $text = str_replace('%2E', '.', $text);

    return $text;
  }

  /**
   * Convert all render(able) markup into strings.
   *
   * This method is used to prevent objects from being serialized on form's
   * that are using #ajax callbacks or rebuilds.
   *
   * @param array $elements
   *   An associative array of elements.
   */
  public static function convertRenderMarkupToStrings(array &$elements): void {
    foreach ($elements as $key => &$value) {
      if (is_array($value)) {
        self::convertRenderMarkupToStrings($value);
      }
      elseif ($value instanceof MarkupInterface) {
        $elements[$key] = (string) $value;
      }
    }
  }

}
