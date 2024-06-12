<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Component\Utility\Html;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Schema.org schema type builder service.
 *
 * The Schema.org schema type builder service helps render Schema.org's types
 * and properties via Drupal.
 *
 * The service is primarily used by the Schema.org Blueprints UI
 * and report modules.
 */
class SchemaDotOrgSchemaTypeBuilder implements SchemaDotOrgSchemaTypeBuilderInterface {

  /**
   * Constructs a SchemaDotOrgSchemaTypeBuilder object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   The current user.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected AccountInterface $currentUser,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getItemUrl(string $id): Url {
    return ($this->moduleHandler->moduleExists('schemadotorg_report')
      && $this->currentUser->hasPermission('access site reports'))
      ? Url::fromRoute('schemadotorg_report', ['id' => $id])
      : Url::fromUri('https://schema.org/' . $id);
  }

  /**
   * {@inheritdoc}
   */
  public function buildItemsLinks(string|array $text, array $options = []): array {
    $options += [
      'prefix' => ', ',
      'attributes' => [],
    ];

    $ids = (is_string($text)) ? $this->schemaTypeManager->parseIds($text) : $text;
    $links = [];
    foreach ($ids as $id) {
      $prefix = ($links && $options['prefix']) ? $options['prefix'] : '';

      $is_item = $this->schemaTypeManager->isItem($id);
      $is_uri = (str_starts_with($id, 'http'));
      if ($is_item || $is_uri) {
        $links[] = [
          '#type' => 'link',
          '#title' => $id,
          '#url' => $is_item ? $this->getItemUrl($id) : Url::fromUri($id),
          '#prefix' => $prefix,
          '#attributes' => $options['attributes'],
        ];
      }
      else {
        $links[] = ['#plain_text' => $id, '#prefix' => $prefix];
      }
    }
    return $links;
  }

  /**
   * {@inheritdoc}
   */
  public function buildTypeTree(array $tree, array $options = []): array {
    $options += [
      'base_path' => $this->getDefaultBasePath(),
      'attributes' => [],
    ];
    $build = $this->buildTypeTreeRecursive($tree, $options);
    $build['#attributes'] = ['class' => ['schemadotorg-jstree']];
    $build['#attached']['library'][] = 'schemadotorg/schemadotorg.jstree';
    return $build;
  }

  /**
   * Build Schema.org type tree as an item list recursively.
   *
   * @param array $tree
   *   An array of Schema.org type tree.
   * @param array $options
   *   Options which include:
   *   - base_path.
   *   - attributes.
   *
   * @return array
   *   A renderable array containing Schema.org type tree as an item list.
   *
   * @see \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager::getTypesChildrenRecursive
   */
  protected function buildTypeTreeRecursive(array $tree, array $options = []): array {
    if (empty($tree)) {
      return [];
    }

    $items = [];
    foreach ($tree as $type => $item) {
      $items[$type] = [
        '#type' => 'link',
        '#title' => $type,
        '#url' => Url::fromUri($options['base_path'] . $type),
      ];
      $item += ['subtypes' => [], 'enumerations' => []];
      $children = $item['subtypes'] + $item['enumerations'];
      $items[$type]['children'] = $this->buildTypeTreeRecursive($children, $options);
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formatComment(string $comment, array $options = []): string {
    $options += [
      'attributes' => [],
    ];

    if (empty($options['base_path'])) {
      $options['base_path'] = $this->getDefaultBasePath();
    }

    if (!str_contains($comment, 'href="')) {
      return $comment;
    }

    $dom = Html::load($comment);
    $a_nodes = $dom->getElementsByTagName('a');
    foreach ($a_nodes as $a_node) {
      $a_node->removeAttribute('class');
      foreach ($options['attributes'] as $attribute_name => $attribute_value) {
        $a_node->setAttribute($attribute_name, $attribute_value);
      }

      $href = $a_node->getAttribute('href');
      if (preg_match('#^/([0-9A-Za-z]+)$#', $href, $match)) {
        $a_node->setAttribute('href', $options['base_path'] . $match[1]);
      }
      elseif (str_starts_with($href, '/')) {
        $a_node->setAttribute('href', 'https://schema.org' . $href);
      }
    }
    return Html::serialize($dom);
  }

  /**
   * Gets the default Schema.org base path for the current user.
   *
   * @return string
   *   The default Schema.org base path for the current user.
   */
  protected function getDefaultBasePath(): string {
    return ($this->moduleHandler->moduleExists('schemadotorg_report')
      && $this->currentUser->hasPermission('access site reports'))
      ? Url::fromRoute('schemadotorg_report')->setAbsolute()->toString() . '/'
      : 'https://schema.org/';
  }

}
