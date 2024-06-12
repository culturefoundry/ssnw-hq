<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Utility;

/**
 * Provides helper to operate on arrays.
 */
class SchemaDotOrgArrayHelper {

  /**
   * Inserts a new key/value before the key in the array.
   *
   * @param array &$array
   *   An array to insert in to.
   * @param string $target_key
   *   The key to insert before.
   * @param string $new_key
   *   The key to insert.
   * @param mixed $new_value
   *   An value to insert.
   */
  public static function insertBefore(array &$array, string $target_key, string $new_key, mixed $new_value): void {
    $new = [];
    foreach ($array as $k => $value) {
      if ($k === $target_key) {
        $new[$new_key] = $new_value;
      }
      $new[$k] = $value;
    }
    $array = $new;
  }

  /**
   * Inserts a new key/value after the key in the array.
   *
   * @param array &$array
   *   An array to insert in to.
   * @param string $target_key
   *   The key to insert after.
   * @param string $new_key
   *   The key to insert.
   * @param mixed $new_value
   *   An value to insert.
   */
  public static function insertAfter(array &$array, string $target_key, string $new_key, mixed $new_value): void {
    $new = [];
    foreach ($array as $key => $value) {
      $new[$key] = $value;
      if ($key === $target_key) {
        $new[$new_key] = $new_value;
      }
    }
    $array = $new;
  }

}
