<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_translation;

use Drupal\Core\Field\FieldConfigBase;
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
   * @param \Drupal\Core\Field\FieldConfigBase $field_config
   *   The field.
   */
  public function enableMappingField(FieldConfigBase $field_config): void;

}
