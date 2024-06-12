<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_epp;

use Drupal\node\NodeInterface;

/**
 * Schema.org Entity Prepopulate interface.
 */
interface SchemaDotOrgEppManagerInterface {

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

  /**
   * Alter the links of a node.
   *
   * @param array &$links
   *   A renderable array representing the node links.
   * @param \Drupal\node\NodeInterface $node
   *   The node being rendered.
   * @param array &$context
   *   Various aspects of the context in which the node links are going to be
   *    displayed.
   */
  public function nodeLinksAlter(array &$links, NodeInterface $node, array &$context): void;

  /**
   * Get node links with entity prepopulate query string parameters.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return array
   *   An array of links with title and url.
   */
  public function getNodeLinks(NodeInterface $node): array;

}
