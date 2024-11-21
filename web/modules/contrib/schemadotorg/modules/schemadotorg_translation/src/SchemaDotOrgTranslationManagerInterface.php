<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_translation;

use Drupal\Core\Field\FieldConfigInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org translate manager interface.
 */
interface SchemaDotOrgTranslationManagerInterface {

  /**
   * Enable translation for a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function enableMapping(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Enable translation for a Schema.org mapping field.
   *
   * @param \Drupal\Core\Field\FieldConfigInterface $field_config
   *   The field.
   */
  public function enableMappingField(FieldConfigInterface $field_config): void;

}
