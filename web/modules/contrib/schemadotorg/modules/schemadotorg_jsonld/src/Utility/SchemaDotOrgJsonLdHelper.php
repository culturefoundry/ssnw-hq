<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld\Utility;

/**
 * Provides helper to operate on JSON-LD data.
 */
class SchemaDotOrgJsonLdHelper {

  /**
   * Append a value to JSON-LD data for specific Schema.org property.
   *
   * @param array &$data
   *   The array of JSON-LD data the value should be appended.
   * @param string $schema_property
   *   The specific Schema.org property.
   * @param mixed $value
   *   The value to be appended.
   */
  public static function appendValue(array &$data, string $schema_property, mixed $value): void {
    if (!isset($data[$schema_property])) {
      $data[$schema_property] = $value;
    }
    elseif (is_array($data[$schema_property])
      && array_is_list($data[$schema_property])) {
      $data[$schema_property][] = $value;
    }
    else {
      $data[$schema_property] = [$data[$schema_property]];
      $data[$schema_property][] = $value;
    }
  }

}
