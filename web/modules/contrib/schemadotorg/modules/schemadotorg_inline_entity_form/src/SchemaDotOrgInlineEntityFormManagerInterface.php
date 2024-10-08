<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_inline_entity_form;

use Drupal\Core\Entity\EntityInterface;

/**
 * The Schema.org Inline Entity Form manager interface.
 */
interface SchemaDotOrgInlineEntityFormManagerInterface {

  /**
   * Act on a Schema.org bundle entity type type before it is created.
   *
   * Adds 'inline entity form' form display to specific
   * Schema.org node type mappings.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   */
  public function entityInsert(EntityInterface $entity): void;

  /**
   * Alter field storage and field values before they are created.
   *
   * @param string $schema_type
   *   The Schema.org type.
   * @param string $schema_property
   *   The Schema.org property.
   * @param array $field_storage_values
   *   Field storage config values.
   * @param array $field_values
   *   Field config values.
   * @param string|null $widget_id
   *   The plugin ID of the widget.
   * @param array $widget_settings
   *   An array of widget settings.
   * @param string|null $formatter_id
   *   The plugin ID of the formatter.
   * @param array $formatter_settings
   *   An array of formatter settings.
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
