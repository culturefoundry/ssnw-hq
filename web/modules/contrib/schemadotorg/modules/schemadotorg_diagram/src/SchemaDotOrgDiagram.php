<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_diagram;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Schema.org diagram service.
 *
 * @see https://mermaid.js.org/intro/
 * @see https://mermaid.js.org/syntax/flowchart.html
 * @see https://jojozhuang.github.io/tutorial/mermaid-cheat-sheet/
 */
class SchemaDotOrgDiagram implements SchemaDotOrgDiagramInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Current node.
   */
  const CURRENT_NODE = 'current';

  /**
   * Parent node.
   */
  const PARENT_NODE = 'parent';

  /**
   * Child node.
   */
  const CHILD_NODE = 'child';

  /**
   * Max depth for hierarchy.
   */
  protected int $maxDepth = 3;

  /**
   * The parent Schema.org property.
   */
  protected string|null $parentProperty;

  /**
   * The child Schema.org property.
   */
  protected string|null $childProperty;

  /**
   * Constructs a SchemaDotOrgDiagram object.
   *
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder
   *   The Schema.org schema type builder.
   */
  public function __construct(
    protected AccountInterface $currentUser,
    protected ConfigFactoryInterface $configFactory,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function buildDiagrams(NodeInterface $node): array {
    $diagram_settings = $this->configFactory
      ->get('schemadotorg_diagram.settings')
      ->get('diagrams');

    $diagrams = [];
    foreach ($diagram_settings as $diagram_name => $diagram_setting) {
      $diagrams[$diagram_name] = $this->buildDiagram(
        $node,
        $diagram_setting['parent'] ?? NULL,
        $diagram_setting['child'] ?? NULL,
        $diagram_setting['title'] ?? NULL,
      );
    }
    return array_filter($diagrams);
  }

  /**
   * {@inheritdoc}
   */
  public function buildDiagram(NodeInterface $node, ?string $parent_property, ?string $child_property, ?string $title): ?array {
    $this->parentProperty = $parent_property;
    $this->childProperty = $child_property;

    // The current node's depth is 1, the parent nodes' depth is 0,
    // and child nodes' depth starts at 2.
    $depth = 1;

    // Build parent nodes output.
    $parent_output = [];
    $this->buildParentNodesOutput($parent_output, $node);

    // Build child nodes output.
    $child_output = [];
    $this->buildChildNodesOutputRecursive($child_output, $node);

    // Exit, if there are no parent or child outputs.
    if (empty($parent_output) && empty($child_output)) {
      return NULL;
    }

    // Start flowchart.
    $output = ['flowchart TB'];

    // Build current node container.
    $node_id = $depth . '-' . $node->id();
    $this->appendNodeToOutput($output, $node_id, $node, static::CURRENT_NODE);

    // Merge parent and child output.
    $output = array_merge($output, $parent_output, $child_output);

    $build = [];

    // Title.
    if ($title) {
      $build['title'] = [
        '#markup' => $title,
        '#prefix' => '<h2>',
        '#suffix' => '</h2>',
      ];
    }

    // Relationships.
    $build['relationships'] = [
      '#prefix' => '<p class="schemadotorg-diagram-relationships">',
      '#suffix' => '</p>',
    ];
    // Relationships: Parent.
    if ($this->parentProperty && $parent_output) {
      $build['relationships']['parent'] = $this->schemaTypeBuilder->buildItemsLinks('https://schema.org/' . $this->parentProperty);
    }
    // Relationships: Current.
    $schema_type = $this->getNodeSchemaType($node) ?? (string) $this->t('{current}');
    $build['relationships']['current'] = $this->schemaTypeBuilder->buildItemsLinks('https://schema.org/' . $schema_type);
    if (($this->parentProperty && $parent_output)) {
      $build['relationships']['current']['#prefix'] = ' → ';
    }
    if ($this->childProperty && $child_output) {
      $build['relationships']['current']['#suffix'] = ' → ';
    }
    // Relationships: Child.
    if ($this->childProperty && $child_output) {
      $build['relationships']['child'] = $this->schemaTypeBuilder->buildItemsLinks('https://schema.org/' . $this->childProperty);
    }

    // Mermaid.js diagram.
    $build['mermaid'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['mermaid', 'schemadotorg-mermaid']],
      '#markup' => implode(PHP_EOL, $output),
    ];

    // Attach the mermaid.js and dialog libraries.
    $build['#attached']['library'][] = 'schemadotorg/schemadotorg.mermaid';
    $build['#attached']['library'][] = 'schemadotorg/schemadotorg.dialog';

    // @todo Determine how best to cache the diagram.
    $build['#cache']['max-age'] = 0;

    return $build;
  }

  /**
   * Build parent nodes.
   *
   * @param array &$output
   *   Parent output.
   * @param \Drupal\node\NodeInterface $node
   *   The node.
   */
  protected function buildParentNodesOutput(array &$output, NodeInterface $node): void {
    $parent_field_name = $this->getEntityReferenceFieldName($node, $this->parentProperty);
    if (!$parent_field_name) {
      return;
    }

    $node_id = '1-' . $node->id();

    foreach ($node->$parent_field_name as $item) {
      /** @var \Drupal\node\NodeInterface|null $parent_node */
      $parent_node = $item->entity;
      if (!$parent_node) {
        continue;
      }

      $parent_id = '0-' . $parent_node->id();

      // Build parent container and link.
      $this->appendNodeToOutput($output, $parent_id, $parent_node, static::PARENT_NODE);

      // Build connector from parent to child.
      $output[] = $parent_id . ' --- ' . $node_id;
    }
  }

  /**
   * Build child nodes recursively.
   *
   * @param array &$output
   *   Child output.
   * @param \Drupal\node\NodeInterface $node
   *   The node.
   * @param int $depth
   *   The current depth of the recursion.
   */
  protected function buildChildNodesOutputRecursive(array &$output, NodeInterface $node, int $depth = 2): void {
    $child_field_name = $this->getEntityReferenceFieldName($node, $this->childProperty);
    if (!$child_field_name) {
      return;
    }

    $parent_id = ($depth - 1) . '-' . $node->id();
    foreach ($node->$child_field_name as $item) {
      /** @var \Drupal\node\NodeInterface|null $child_node */
      $child_node = $item->entity;
      if (!$child_node) {
        continue;
      }

      $child_id = $depth . '-' . $child_node->id();

      // Build connector from parent to child with entity reference override
      // as the connector label.
      $override = $item->override ?? NULL;
      $override_format = $item->override_format ?? NULL;
      if ($override) {
        $connector_label = $override_format
          ? (string) check_markup($override, $override_format)
          : $override;
        $connector_label = Unicode::truncate($connector_label, 30, TRUE, TRUE);
        $output[] = $parent_id . ' --- |"`' . $connector_label . '`"|' . $child_id;
      }
      else {
        $output[] = $parent_id . ' --- ' . $child_id;
      }

      // Build child container and link.
      $this->appendNodeToOutput($output, $child_id, $child_node);

      if ($depth < $this->maxDepth) {
        $this->buildChildNodesOutputRecursive($output, $child_node, $depth + 1);
      }
    }
  }

  /**
   * Append the node to the diagram's output.
   *
   * @param array &$output
   *   The output.
   * @param string $id
   *   The node's id prefixed with its depth.
   * @param \Drupal\node\NodeInterface $node
   *   The node.
   * @param string|null $type
   *   The node's container type.
   */
  protected function appendNodeToOutput(array &$output, string $id, NodeInterface $node, ?string $type = NULL): void {
    // URI.
    $node_url = ($this->routeMatch->getRouteName() === 'entity.node.schemadotorg_diagram')
      ? Url::fromRoute('entity.node.schemadotorg_diagram', ['node' => $node->id()])
      : $node->toUrl();
    $node_uri = $node_url->setAbsolute()->toString();

    // Title with Schema.org type.
    $node_title = '**' . $node->label() . '**';
    $schema_type = $this->getNodeSchemaType($node);
    if ($schema_type) {
      $node_title .= PHP_EOL . '(' . $schema_type . ')';
    }

    // Node type shapes.
    switch ($type) {
      case static::CURRENT_NODE;
        // Pink circle with thick border.
        $output[] = $id . '(("`' . $node_title . '`"))';
        $output[] = "style $id fill:#ffaacc,stroke:#333,stroke-width:4px;";
        break;

      case static::PARENT_NODE;
      case static::CHILD_NODE;
      default;
        // Rectangle.
        $output[] = $id . '["`' . $node_title . '`"]';
        break;
    }

    // Link.
    $output[] = 'click ' . $id . ' "' . $node_uri . '"';
  }

  /**
   * Get a node's Schema.org type.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return string|null
   *   A node's Schema.org type.
   */
  protected function getNodeSchemaType(NodeInterface $node): ?string {
    $mapping = $this->getMappingStorage()->loadByEntity($node);
    if (!$mapping) {
      return NULL;
    }

    $field_name = $mapping->getSchemaPropertyFieldName('additionalType');
    return ($field_name && $node->hasField($field_name) && $node->get($field_name)->value)
      ? $node->get($field_name)->value
      : $mapping->getSchemaType();
  }

  /**
   * Get a node's entity reference field for a Schema.org property.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   * @param string|null $schema_property
   *   A Schema.org property.
   *
   * @return string|null
   *   A node's entity reference field for a Schema.org property.
   */
  protected function getEntityReferenceFieldName(NodeInterface $node, ?string $schema_property): ?string {
    if (!$schema_property) {
      return NULL;
    }

    $mapping = $this->getMappingStorage()->loadByEntity($node);
    if (!$mapping) {
      return NULL;
    }

    $field_name = $mapping->getSchemaPropertyFieldName($schema_property);
    if (!$field_name
      || !$node->hasField($field_name)
      || !($node->$field_name instanceof EntityReferenceFieldItemListInterface)) {
      return NULL;
    }

    return $field_name;
  }

}
