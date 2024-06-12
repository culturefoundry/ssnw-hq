<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_epp;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Schema.org Entity Prepopulate manager.
 */
class SchemaDotOrgEppManager implements SchemaDotOrgEppManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgEppManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
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
  ): void {
    // Make sure the field entity type is a node.
    if ($field_storage_values['entity_type'] !== 'node') {
      return;
    }

    // Make sure the field type is set to 'entity_reference'.
    if (!str_starts_with($field_storage_values['type'], 'entity_reference')) {
      return;
    }

    // Allow all entity reference to be prepopulated via query string parameters.
    $query_param_name = $this->getQueryParameterName($schema_property);
    $field_values['third_party_settings']['epp']['value'] = 'target_id: [current-page:query:' . $query_param_name . ']';
  }

  /**
   * {@inheritdoc}
   */
  public function nodeLinksAlter(array &$links, NodeInterface $node, array &$context): void {
    // Check that we are on a full page view of a node.
    if ($context['view_mode'] !== 'full' || !node_is_page($node)) {
      return;
    }

    $node_links = $this->getNodeLinks($node);
    if (empty($node_links)) {
      return;
    }

    $node_links_dropdown = $this->configFactory->get('schemadotorg_epp.settings')
      ->get('node_links_dropdown');
    if ($node_links_dropdown) {
      // Unset the default links wrapper.
      // @see \Drupal\node\NodeViewBuilder::renderLinks
      unset($links['#theme'], $links['#pre_render'], $links['#attributes']);

      // Add button--action plus sing to all links.
      foreach ($node_links as &$node_link) {
        $node_link['attributes'] = ['class' => ['button--action']];
      }

      $links['schemadotorg_epp'] = [
        '#type' => 'operations',
        '#links' => $node_links,
        '#weight' => -100,
        '#prefix' => '<div class="schemadotorg-epp-node-links-dropdown">',
        '#suffix' => '</div>',
      ];
    }
    else {
      // Style all links as action buttons.
      foreach ($node_links as &$node_link) {
        $node_link['attributes'] = ['class' => ['button', 'button-small', 'button--extrasmall', 'button--action']];
      }

      $links['schemadotorg_epp'] = [
        '#theme' => 'links__node__schemadotorg_epp',
        '#links' => $node_links,
        '#attributes' => ['class' => ['links', 'inline']],
      ];
    }
  }

  /**
   * Get node links with entity prepopulate query string parameters.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return array
   *   An array of links with title and url.
   */
  public function getNodeLinks(NodeInterface $node): array {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');

    // Check that the node is mapped to a Schema.org type.
    $mapping = $mapping_storage->loadByEntity($node);
    if (!$mapping) {
      return [];
    }

    $node_links = [];
    $node_field_names = $this->getNodeFieldNames($node);
    foreach ($node_field_names as $bundle => $field_names) {
      $fields = [];
      foreach ($field_names as $field_name => $query_param) {
        $bundle_label = $this->entityTypeManager
          ->getStorage('node_type')
          ->load($bundle)->label();
        $field_config = FieldConfig::loadByName('node', $bundle, $field_name);
        if (!$field_config) {
          continue;
        }

        $field_label = $field_config->label();

        $t_args = [
          '@type' => $bundle_label,
          '@field' => $field_label,
        ];
        $node_links["$bundle--$query_param"] = [
          'title' => $this->formatPlural(count($field_names), 'Add @type', 'Add @type (@field)', $t_args),
          'url' => Url::fromRoute(
            'node.add',
            ['node_type' => $bundle],
            ['query' => [$query_param => $node->id()]],
          ),
        ];

        // Track the bundle's fields query and label.
        $fields[$query_param] = $field_label;
        if (count($fields) > 1) {
          $t_args = [
            '@type' => $bundle_label,
            '@field' => implode(' + ', $fields),
          ];
          $node_links[$bundle . '--' . implode('--', array_keys($fields))] = [
            'title' => $this->t('Add @type (@field)', $t_args),
            'url' => Url::fromRoute(
              'node.add',
              ['node_type' => $bundle],
              ['query' => array_fill_keys(array_keys($fields), $node->id())],
            ),
          ];
        }
      }
    }

    return $node_links;
  }

  /**
   * Get a Schema.org mapping's parent Schema.org types.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   *
   * @return array
   *   A Schema.org mapping's parent Schema.org types.
   */
  protected function getParentSchemaTypes(SchemaDotOrgMappingInterface $mapping): array {
    $parent_schema_types = [];
    $schema_types = $mapping->getAllSchemaTypes();
    foreach ($schema_types as $schema_type) {
      $parent_schema_types += array_reverse(
        $this->schemaTypeManager->getParentTypes($schema_type)
      );
    }
    return $parent_schema_types;
  }

  /**
   * Get a node's prepopulated bundles and field names.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node.
   *
   * @return array
   *   An associative array containing a node's
   *   prepopulated bundles and field names.
   */
  protected function getNodeFieldNames(NodeInterface $node): array {
    // Check that the node is mapped to a Schema.org type.
    $mapping = $this->getMappingStorage()->loadByEntity($node);
    if (!$mapping) {
      return [];
    }

    $query = [];
    $parent_schema_types = $this->getParentSchemaTypes($mapping);
    foreach ($parent_schema_types as $parent_schema_type) {
      $schema_type_node_links = $this->configFactory
        ->get('schemadotorg_epp.settings')
        ->get("node_links.$parent_schema_type");
      if (!$schema_type_node_links) {
        continue;
      }

      foreach ($schema_type_node_links as $node_link_schema_property => $node_link_schema_type) {
        $node_link_mappings = $this->getMappingStorage()
          ->loadMultipleBySchemaType('node', $node_link_schema_type);
        foreach ($node_link_mappings as $node_link_mapping) {
          $node_link_bundle = $node_link_mapping->getTargetBundle();
          $node_link_schema_type = $node_link_mapping->getSchemaType();

          // Get the node link's field name.
          $node_link_field_name = $node_link_mapping->getSchemaPropertyFieldName($node_link_schema_property);
          if (!$node_link_field_name) {
            continue;
          }

          // Make sure the target entity reference supports the node's bundle.
          /** @var \Drupal\field\FieldConfigInterface $node_link_field */
          $node_link_field = FieldConfig::loadByName('node', $node_link_bundle, $node_link_field_name);
          if (!NestedArray::keyExists($node_link_field->getSettings(), [
            'handler_settings',
            'target_bundles',
            $node->getType(),
          ])) {
            continue;
          };

          $query_param_name = $this->getQueryParameterName($node_link_schema_property);
          $target_bundles = $this->getMappingStorage()
            ->getRangeIncludesTargetBundles('node', [$node_link_schema_type]);

          $node_types = $this->entityTypeManager->getStorage('node_type')
            ->loadMultiple($target_bundles);
          foreach ($node_types as $node_type) {
            $query += [$node_type->id() => []];
            $query[$node_type->id()][$node_link_field_name] = $query_param_name;
          }
        }
      }
    }
    return $query;
  }

  /**
   * Get query string parameter name for a Schema.org property.
   *
   * NOTE: We are mot using abbreviations for query params.
   *
   * @param string $schema_property
   *   A Schema.org property.
   *
   * @return string
   *   The query string parameter name for a Schema.org property.
   */
  protected function getQueryParameterName(string $schema_property): string {
    return $this->schemaNames->camelCaseToSnakeCase($schema_property);
  }

}
