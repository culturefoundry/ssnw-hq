<?php

namespace Drupal\content_model_documentation;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * Creates list of entity relations and queries for bundle relations.
 */
class RelatedEntities {

  /**
   * Entity types used for building diagram.
   *
   * @var string[]
   */
  public function entityTypes() {
    $entity_types = [];
    foreach ($this->entityTypeManager->getDefinitions() as $id => $type) {
      if ($type->entityClassImplements(FieldableEntityInterface::class)) {
        $entity_types[$id] = $type->getLabel();
      }
    }
    asort($entity_types);
    return $entity_types;
  }

  /**
   * Fields to ignore when looking for relationships.
   *
   * @var string[]
   */
  public static $skipFields = [
    'type',
    'id',
    'uuid',
    'langcode',
    'revision_user',
    'revision_created',
    'revision_log_message',
    'status',
    'uid',
    'bundle',
    'nid',
    'vid',
    'revision_timestamp',
    'revision_uid',
    'revision_log',
    'parent',
  ];

  /**
   * Drupal\Core\Entity\EntityFieldManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Drupal\Core\Entity\EntityTypeBundleInfoInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Array of EntityRelation objects for all entity relations.
   *
   * @var array
   */
  protected $relationList = [];

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs the EntityDiagramController.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The EntityFieldManagerInterface service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The EntityTypeBundleInfoInterface service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The EntityFieldManagerInterface service.
   */
  public function __construct(EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityTypeManager = $entity_type_manager;
    $this->fillRelationList();
  }

  /**
   * Get bundle's ancestors and decendents to the specified depth.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $entity_bundle
   *   The bundle to start from.
   * @param int $max_depth
   *   How far down, or up, to look.
   *
   * @return array
   *   Array of entity relations.
   */
  public function getRelations(EntityBundleId $entity_bundle, int $max_depth) {
    $parents = $this->getAncestors($entity_bundle, $max_depth);
    $children = $this->getDecendents($entity_bundle, $max_depth);
    return array_unique(array_merge($children, $parents));
  }

  /**
   * Gets parents of specified entity/bundle.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $start
   *   The entity/bundle to find parents for.
   * @param int $max_depth
   *   How far to recurse. Zero means unlimited depth.
   * @param int $depth
   *   Current recursion depth. Omit when calling.
   * @param array $processed
   *   List of processed entity bundles. Omit when calling.
   *
   * @return array
   *   An array of enitity relations.
   */
  public function getAncestors(EntityBundleId $start, int $max_depth, int $depth = 0, array $processed = []) {

    if (in_array($start, $processed)) {
      return [];
    }

    $relations = $this->getParents($start);

    $processed[] = $start;
    $extended_relations = [];

    $depth++;
    if ($max_depth === 0 || $depth < $max_depth) {
      /** @var \Drupal\content_model_documentation\EntityRelation $relation */
      foreach ($relations as $relation) {
        $extended_relations = array_merge($extended_relations, $this->getAncestors($relation->getSource(), $max_depth, $depth, $processed));
      }
    }

    return array_merge($relations, $extended_relations);
  }

  /**
   * Gets children of specified entity/bundle.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $start
   *   The entity/bundle to find children for.
   * @param int $max_depth
   *   How far to recurse. Zero means unlimited depth.
   * @param int $depth
   *   Current recursion depth. Omit when calling.
   * @param array $processed
   *   List of processed entity bundles. Omit when calling.
   *
   * @return array
   *   An array of enitity relations.
   */
  public function getDecendents(EntityBundleId $start, int $max_depth, int $depth = 0, array $processed = []) {

    if (in_array($start, $processed)) {
      return [];
    }

    $relations = $this->getChildren($start);

    $processed[] = $start;
    $extended_relations = [];

    $depth++;
    if ($max_depth === 0 || $depth < $max_depth) {
      /** @var \Drupal\content_model_documentation\EntityRelation $relation */
      foreach ($relations as $relation) {
        $extended_relations = array_merge($extended_relations, $this->getDecendents($relation->getDest(), $max_depth, $depth, $processed));
      }
    }

    return array_merge($relations, $extended_relations);
  }

  /**
   * Returns all entity relations in database.
   *
   * @return array
   *   Array of entity relations.
   */
  public function getAllRelations() {
    return $this->relationList;
  }

  /**
   * Gets entity that reference the selected entity.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $entity_bundle
   *   The selected entity.
   *
   * @return array
   *   An array of entity relations with selected entity as dest.
   */
  public function getParents(EntityBundleId $entity_bundle) {
    return array_filter($this->relationList,
        function (EntityRelation $relation) use ($entity_bundle) {
          return $relation->getDest()->equals($entity_bundle);
        }
      ) ?? [];
  }

  /**
   * Gets entities that the selected entity references.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $entity_bundle
   *   The selected bundle.
   *
   * @return array
   *   An array of entity relations with selected entity as source.
   */
  public function getChildren(EntityBundleId $entity_bundle) {
    return array_filter($this->relationList,
        function (EntityRelation $relation) use ($entity_bundle) {
          return $relation->getSource()->equals($entity_bundle);
        }
      ) ?? [];
  }

  /**
   * Populates relation list with all relations for bundles in entity list.
   */
  protected function fillRelationList() {
    $this->relationList = [];
    foreach (array_keys($this->entityTypes()) as $entity_id) {
      $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_id);

      foreach (array_keys($bundles) as $bundle_id) {
        $entity_bundle = new EntityBundleId($entity_id, $bundle_id);
        $fields = $this->entityFieldManager->getFieldDefinitions($entity_bundle->entityId, $entity_bundle->bundleId);
        foreach ($fields as $field_id => $field) {
          if (in_array($field_id, self::$skipFields) || !in_array($field->getType(), [
            'entity_reference',
            'entity_reference_revisions',
          ])) {
            continue;
          }
          $settings = $field->getSettings();
          $target_type = $settings['target_type'];
          $target_bundles = [];
          if (isset($settings['handler_settings']['target_bundles'])) {
            // This is a normal entity reference.
            $target_bundles = $settings['handler_settings']['target_bundles'];
          }
          elseif ($settings['handler'] === 'views') {
            // This uses a View to control what entities may be targeted.
            $references = $this->getViewReferences($settings);
            $target_bundles = $references['target_bundles'];
            $target_type = $references['target_type'];
          }

          foreach (array_keys($target_bundles) as $target_bundle) {
            if (empty($target_bundle)) {
              continue;
            }

            $dest = new EntityBundleId($target_type, $target_bundle);
            $field_label = $this->cleanFieldId($field_id, $entity_bundle->bundleId);
            $this->relationList[] = new EntityRelation($entity_bundle, $dest, $field_label);
          }
        }
      }
    }
  }

  /**
   * Strips 'field_' and bundle prefixes from field name.
   *
   * NOTE: Prefixing bundle id is a convention for NSF codebase. Maybe not
   * relevant elsewhere.
   *
   * @param string $field_id
   *   The field's machine name.
   * @param string $bundle_id
   *   The bundle's machine name.
   *
   * @return string
   *   Field id, stripped of prefixes.
   */
  protected function cleanFieldId(string $field_id, string $bundle_id) {
    // Strip 'field_' and bundle prefixes if they exist.
    if (preg_match('/field_(.*)/', $field_id, $matches)) {
      $field_id = $matches[1];
    }
    if (preg_match("/{$bundle_id}_(.*)/", $field_id, $matches)) {
      $field_id = $matches[1];
    }
    return $field_id;
  }

  /**
   * Extracts target bundle and target type from Views filter entity reference.
   *
   * @param array $settings
   *   The field settings.
   *
   * @return array
   *   An array containing keys target_type and target_bundles
   */
  protected function getViewReferences(array $settings): array {
    if ($settings['handler'] === 'views') {
      $view_id = $settings['handler_settings']['view']['view_name'] ?? '';
      $view_display_name = $settings['handler_settings']['view']['display_name'] ?? '';
      // Load the view to get the targets from the filters.
      $view = $this->entityTypeManager->getStorage('view')->load($view_id);
      $display = $view->getDisplay($view_display_name);
    }
    $references = [
      'target_bundles' => $display['display_options']['filters']['vid']['value'] ?? [],
      'target_type' => $display['display_options']['filters']['vid']['entity_type'] ?? '',
    ];

    return $references;
  }

}
