<?php

namespace Drupal\content_model_documentation\Plugin\views\field;

use Drupal\field\FieldConfigInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get targets for entity reference fields.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("configuration_field_entity_reference_targets")
 */
class ConfigurationEntityReferenceTargets extends FieldPluginBase {

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
    if (!empty($values->entity)) {
      // Gather the targets for entity reference fields.
      $reference_fields = [
        // When adding a new entity reference, add it here AND add an
        // appropriately name extractTargets function.
        'entity_reference',
        'entity_reference_revisions',
        'entity_field_fetch',
      ];
      $field_type = $values->entity->getType();
      $is_reference = in_array($field_type, $reference_fields);
      if ($is_reference) {
        $field_type_camel = "extractTargets" . str_replace('_', '', ucwords($field_type, '_'));
        // Find the targets by calling the proper extractTarget function.
        $targets = self::$field_type_camel($values->entity);
      }

    }
    return $targets ?? '';
  }

  /**
   * Extracts target data from entity_reference field.
   *
   * @param \Drupal\field\FieldConfigInterface $entity
   *   The FieldConfig entity.
   *
   * @return string
   *   The text declaring the targets.
   */
  protected function extractTargetsEntityReference(FieldConfigInterface $entity) {
    $target_entity = $entity->getSetting('target_type');
    $target_bundles = $entity->getSetting('handler_settings')['target_bundles'] ?? NULL;
    $handler_settings = $entity->getSetting('handler_settings');
    if (empty($target_entity) || empty($target_bundles)) {
      // This is possibly a Views filter.
      $targets = $this->extractViewsFilterTargets($handler_settings);
    }
    else {
      $target_bundles_list = implode(' ,', $target_bundles);
      $targets = "$target_entity: $target_bundles_list";
    }

    return $targets;
  }

  /**
   * Gets target information from a View filter.
   *
   * @param array|null $handler_settings
   *   The handler settings from the field.
   *
   * @return string
   *   The concatenated strings of target information, or empty string.
   */
  protected function extractViewsFilterTargets($handler_settings): string {
    $targets = '';
    if (!empty($handler_settings) && (!empty($handler_settings['view']))) {
      // This a views filter entity ref, so we have to dig for the targets.
      $view_id = $handler_settings['view']['view_name'] ?? '';
      $view_display_name = $handler_settings['view']['display_name'] ?? '';
      // Load the view to get the targets from the filters.
      $view = \Drupal::entityTypeManager()->getStorage('view')->load($view_id);
      $display = $view->getDisplay($view_display_name);

      $target_bundles = $display['display_options']['filters']['vid']['value'] ?? [];
      $bundle_list = implode(', ', array_keys($target_bundles));
      $target_type = $display['display_options']['filters']['vid']['entity_type'] ?? '';
      $targets = "{$target_type}: {$bundle_list} - via View filter {$view_display_name}";
    }
    return $targets;
  }

  /**
   * Extracts target data from entity_reference_revision field.
   *
   * @param \Drupal\field\FieldConfigInterface $entity
   *   The FieldConfig entity.
   *
   * @return string
   *   The text declaring the targets.
   */
  protected function extractTargetsEntityReferenceRevisions(FieldConfigInterface $entity) {
    return self::extractTargetsEntityReference($entity);
  }

  /**
   * Extracts target data from entity_fetch_field field.
   *
   * @param \Drupal\field\FieldConfigInterface $entity
   *   The FieldConfig entity.
   *
   * @return string
   *   The text declaring the targets.
   */
  protected function extractTargetsEntityFieldFetch(FieldConfigInterface $entity) {
    $target_type = $entity->getSetting('target_entity_type');
    $target_entity_id = $entity->getSetting('target_entity_id');
    $target_paragraph = $entity->getSetting('target_paragraph_uuid');
    $target_field = $entity->getSetting('field_to_fetch');
    $fetched = (!empty($target_paragraph)) ? " Paragraph: $target_paragraph" : '';
    return "{$target_type}/{$target_entity_id}:{$target_field}{$fetched}";
  }

}
