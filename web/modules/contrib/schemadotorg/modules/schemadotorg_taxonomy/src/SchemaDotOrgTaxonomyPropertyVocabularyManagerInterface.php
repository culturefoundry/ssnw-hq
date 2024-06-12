<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

/**
 * Schema.org taxonomy vocabulary property manager interface.
 */
interface SchemaDotOrgTaxonomyPropertyVocabularyManagerInterface {

  /**
   * Implements hook_schemadotorg_property_field_type_alter().
   */
  public function propertyFieldTypeAlter(array &$field_types, string $schema_type, string $schema_property): void;

  /**
   * Implements hook_schemadotorg_property_field_alter().
   */
  public function propertyFieldAlter(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void;

}
