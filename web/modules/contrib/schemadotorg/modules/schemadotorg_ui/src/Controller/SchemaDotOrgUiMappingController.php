<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;

/**
 * Returns responses for Schema.org UI routes.
 */
class SchemaDotOrgUiMappingController extends ControllerBase {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Returns response for Schema.org UI create and update mappings.
   */
  public function index(): array {
    $build = [];

    $create_mapping_links = $this->getCreateMappingLinks();
    if ($create_mapping_links) {
      $build['create'] = [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => $this->t('Create new Schema.org mapping'),
          'content' => [
            '#theme' => 'admin_block_content',
            '#content' => $create_mapping_links,
          ],
        ],
      ];
    }

    $update_mapping_links = $this->getUpdateMappingLinks();
    if ($update_mapping_links) {
      $build['update'] = [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => $this->t('Add Schema.org mapping to entity types'),
          'content' => [
            '#theme' => 'admin_block_content',
            '#content' => $update_mapping_links,
          ],
        ],
      ];
    }
    return $build;
  }

  /**
   * Get add Schema.org mapping links.
   *
   * @return array
   *   An array containing add Schema.org mapping links.
   */
  protected function getCreateMappingLinks(): array {
    $content = [];
    $entity_type_definitions = $this->getMappingTypeStorage()->getEntityTypeBundleDefinitions();
    foreach ($entity_type_definitions as $entity_type_id => $entity_type_definition) {
      $bundle_entity_type_id = $entity_type_definition->id();
      $url = Url::fromRoute("schemadotorg.{$bundle_entity_type_id}.type_add");
      if ($url->access($this->currentUser())) {
        $content[$entity_type_id] = [
          'title' => $entity_type_definition->getLabel(),
          'url' => $url,
        ];
      }
    }
    return $content;
  }

  /**
   * Get update Schema.org mapping links.
   *
   * @return array
   *   An array containing update Schema.org mapping links.
   */
  protected function getUpdateMappingLinks(): array {
    $content = [];
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface[] $mapping_types */
    $mapping_types = $this->getMappingTypeStorage()->loadMultiple();
    foreach ($mapping_types as $entity_type_id => $mapping_type) {
      $default_schema_types = $mapping_type->get('default_schema_types');
      // Make sure the default schema types are set and the entity is supported.
      if (empty($default_schema_types)
        || !$this->entityTypeManager()->hasDefinition($entity_type_id)) {
        continue;
      }

      $entity_type = $this->entityTypeManager()->getDefinition($entity_type_id);
      $bundle_entity_type = $entity_type->getBundleEntityType();

      foreach ($default_schema_types as $bundle => $schema_type) {
        // Skipped mapped entities.
        if ($this->getMappingStorage()->isBundleMapped($entity_type_id, $bundle)) {
          continue;
        }

        $route_name = "entity.{$entity_type_id}.schemadotorg_mapping";
        $route_parameter = [];
        $title = $entity_type->getLabel();
        if ($bundle_entity_type) {
          // Make sure the entity exists.
          $entity = $this->entityTypeManager()->getStorage($bundle_entity_type)->load($bundle);
          if (!$entity) {
            continue;
          }

          $route_parameter[$bundle_entity_type] = $bundle;
          $title .= ': ' . $entity->label();
        }

        $url = Url::fromRoute($route_name, $route_parameter);
        if ($url->access($this->currentUser())) {
          $content["$entity_type_id:$bundle"] = [
            'title' => $this->t('@title (@type)', ['@title' => $title, '@type' => $schema_type]),
            'url' => Url::fromRoute($route_name, $route_parameter),
          ];
        }
      }
    }
    return $content;
  }

}
