<?php

namespace Drupal\content_model_documentation\Plugin\views\field;

use Drupal\field\FieldConfigInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get the base field cardinality.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("configuration_field_cardinality")
 */
class ConfigurationFieldCardinality extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field. The data already exists.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    if (!empty($values->entity) && $values->entity instanceof FieldConfigInterface) {
      /** @var Drupal\Core\Field\FieldStorageDefinitionInterface $fieldStorageDefinition */
      $fieldStorageDefinition = $values->entity->getFieldStorageDefinition() ?? NULL;
      $cardinality = $fieldStorageDefinition->getCardinality() ?? '';
      $cardinality = ($cardinality === -1) ? $this->t('unlimited') : $cardinality;
    }
    return $cardinality ?? '';
  }

}
