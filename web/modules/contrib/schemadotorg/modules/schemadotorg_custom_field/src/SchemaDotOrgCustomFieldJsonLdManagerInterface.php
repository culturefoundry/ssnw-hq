<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_custom_field;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Schema.org Custom Field JSON-LD interface.
 */
interface SchemaDotOrgCustomFieldJsonLdManagerInterface {

  /**
   * Alter the Schema.org property JSON-LD value for an entity's field item.
   *
   * Appends units to custom_field JSON-LD data.
   *
   * @param mixed $value
   *   Alter the Schema.org property JSON-LD value.
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The entity's field item.
   */
  public function jsonLdSchemaPropertyAlter(mixed &$value, FieldItemInterface $item): void;

}
