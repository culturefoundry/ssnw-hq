<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_epp;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
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
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected AccountProxyInterface $currentUser,
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
    // Check that the node is mapped to a Schema.org type.
    $mapping = $this->getMappingStorage()->loadByEntity($node);
    if (!$mapping) {
      return [];
    }

    // Get the node's node links settings.
    $settings = $this->schemaTypeManager->getSetting(
      $this->configFactory->get('schemadotorg_epp.settings')->get('node_links'),
      $mapping,
      ['multiple' => TRUE],
    ) ?? [];

    // Create the node links.
    $node_links = [];
    foreach ($settings as $setting) {
      foreach ($setting as $link => $title) {
        // Parse the SchemaType?schemeProperty01&schemaProperty02 link patterns.
        $target_schema_properties = preg_split('/[?&]/', $link);
        $target_schema_type = array_shift($target_schema_properties);

        // Loop through target mappings and create node links.
        $target_bundles = ($this->schemaTypeManager->isType($target_schema_type))
          ? $this->getMappingStorage()->getRangeIncludesTargetBundles('node', [$target_schema_type])
          : [$target_schema_type];
        foreach ($target_bundles as $target_bundle) {
          // Check create content access for node links.
          if (!$this->currentUser->hasPermission('create ' . $target_bundle . ' content')) {
            continue;
          }

          $target_mapping = $this->getMappingStorage()
            ->loadByBundle('node', $target_bundle);
          if (!$target_mapping) {
            continue;
          }

          $target_label = $this->entityTypeManager
            ->getStorage('node_type')
            ->load($target_bundle)->label();

          // Get custom parameters.
          $query_custom_parameters = [];
          foreach ($target_schema_properties as $target_schema_property) {
            if (str_contains($target_schema_property, '=')) {
              [$query_param, $query_value] = explode('=', $target_schema_property);
              $query_custom_parameters[$query_param] = $query_value;
            }
          }

          // Get Schema.org parameters.
          $query_schema_properties = array_flip(
            array_intersect_key(
              array_flip($target_mapping->getAllSchemaProperties()),
              array_flip($target_schema_properties)
            )
          );

          // Build the query from the custom parameters and Schema.org parameters.
          $query = $query_custom_parameters;
          foreach ($query_schema_properties as $query_schema_property) {
            $query_parameter_name = $this->getQueryParameterName($query_schema_property);
            $query[$query_parameter_name] = $node->id();
          }

          if (empty($query)) {
            continue;
          }

          // Build the node link.
          $key = $target_bundle . '--' . implode('--', array_keys($query));
          $node_links[$key] = [
            'title' => $this->t($title, ['@label' => $target_label]),
            'url' => Url::fromRoute(
              'node.add',
              ['node_type' => $target_bundle],
              ['query' => $query],
            ),
          ];
        }
      }
    }
    return $node_links;
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
