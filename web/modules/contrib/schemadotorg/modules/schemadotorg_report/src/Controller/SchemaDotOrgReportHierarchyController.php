<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

/**
 * Returns responses for Schema.org report hierarchy routes.
 */
class SchemaDotOrgReportHierarchyController extends SchemaDotOrgReportControllerBase {

  /**
   * Builds the Schema.org types hierarchy.
   *
   * @param string $type
   *   The root Schema.org type.
   *
   * @return array
   *   A renderable array containing Schema.org types hierarchy.
   */
  public function index(string $type = 'Thing'): array {
    if ($type === 'DataTypes') {
      $types = $this->database->select('schemadotorg_types', 'types')
        ->fields('types', ['label'])
        ->condition('sub_type_of', '')
        ->condition('label', ['True', 'False', 'Thing'], 'NOT IN')
        ->orderBy('label')
        ->execute()
        ->fetchCol();
      $tree = $this->schemaTypeManager->getTypeTree($types);
      $count = count($this->schemaTypeManager->getDataTypes());
    }
    else {
      $ignored_types = ['Intangible', 'Enumeration', 'StructuredValue'];
      $ignored_types = array_combine($ignored_types, $ignored_types);
      unset($ignored_types[$type]);
      $tree = $this->schemaTypeManager->getTypeTree($type, $ignored_types);
      $count = count($this->schemaTypeManager->getAllTypeChildren($type, ['label'], $ignored_types));
    }
    $build = [];
    $build['info'] = $this->buildInfo($type, $count);
    $build['tree'] = $this->schemaTypeBuilder->buildTypeTree($tree);
    return $build;
  }

}
