<?php

namespace Drupal\content_model_documentation\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display entity operations without a delete option.
 *
 * @ViewsField("non_delete_operations")
 */
class NonDeleteOperations extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\Core\Entity\EntityTypeManager $entity_type_manager */
    $entity_type_manager = \Drupal::entityTypeManager();
    $entity_type = $values->type;
    $entity = $values->entity;
    $links = [];
    if ($entity_type_manager->hasHandler($entity_type, 'list_builder')) {
      $links = $entity_type_manager
        ->getListBuilder($entity_type)
        ->getOperations($entity);
      // Remove delete option - users must edit content type directly.
      if (array_key_exists('delete', $links)) {
        unset($links['delete']);
      }
    }
    return [
      '#type' => 'operations',
      '#links' => $links,
    ];
  }

}
