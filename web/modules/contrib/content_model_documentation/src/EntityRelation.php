<?php

namespace Drupal\content_model_documentation;

/**
 * Holds information about relation between bundles.
 */
class EntityRelation {

  /**
   * The source (parent) bundle.
   *
   * @var \Drupal\content_model_documentation\EntityBundleId
   */
  protected $sourceEntityBundle;

  /**
   * The dest (child) bundle.
   *
   * @var \Drupal\content_model_documentation\EntityBundleId
   */
  protected $destEntityBundle;

  /**
   * The source field that references the dest bundle.
   *
   * @var string
   */
  protected $relatingFieldId;

  /**
   * Construct an entity relation from component parts.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $source_entity_bundle
   *   The source.
   * @param \Drupal\content_model_documentation\EntityBundleId $dest_entity_bundle
   *   The destination.
   * @param string $relating_field_id
   *   The referencing field.
   */
  public function __construct(EntityBundleId $source_entity_bundle, EntityBundleId $dest_entity_bundle, string $relating_field_id) {
    $this->sourceEntityBundle = $source_entity_bundle;
    $this->destEntityBundle = $dest_entity_bundle;
    $this->relatingFieldId = $relating_field_id;
  }

  /**
   * Gets the source value.
   *
   * @return \Drupal\content_model_documentation\EntityBundleId
   *   The source.
   */
  public function getSource() {
    return $this->sourceEntityBundle;
  }

  /**
   * Gets the destination value.
   *
   * @return \Drupal\content_model_documentation\EntityBundleId
   *   The destination.
   */
  public function getDest() {
    return $this->destEntityBundle;
  }

  /**
   * Gets the field value.
   *
   * @return string
   *   The field.
   */
  public function getField() {
    return $this->relatingFieldId;
  }

  /**
   * Stringifies the entity relation.
   *
   * @return string
   *   Stringified entity relation.
   */
  public function __toString() {
    $parts = [
      (string) $this->getSource(),
      (string) $this->getDest(),
      $this->getField(),
    ];
    return implode(':', $parts);
  }

}
