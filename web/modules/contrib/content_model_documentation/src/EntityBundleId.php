<?php

namespace Drupal\content_model_documentation;

/**
 * Simplifies handling of entity id/bundle id combos.
 */
class EntityBundleId {

  /**
   * The bundle's machine name.
   *
   * @var string
   */
  public $bundleId = '';

  /**
   * The entity's machine name.
   *
   * @var string
   */
  public $entityId = '';

  /**
   * Creates a new EntityBundleId.
   *
   * @param string $entity_id
   *   The entity ID.
   * @param string $bundle_id
   *   The bundle ID.
   */
  public function __construct(string $entity_id, string $bundle_id) {
    $this->entityId = $entity_id;
    $this->bundleId = $bundle_id;
  }

  /**
   * Tests whether another EntityBundleId is equal to this one.
   *
   * @param \Drupal\content_model_documentation\EntityBundleId $that
   *   The EntityBundleId to test for equality.
   *
   * @return bool
   *   Returns TRUE if they are equal. Otherwise, FALSE.
   */
  public function equals(EntityBundleId $that) {
    return $this->bundleId === $that->bundleId && $this->entityId === $that->entityId;
  }

  /**
   * Magic function to stringify entity bundle ids.
   *
   * NOTE: There is no delimiter between entity and bundle because Mermaid
   * chokes on the word 'default'.
   *
   * @return string
   *   Stringified ID.
   */
  public function __toString() {
    return "{$this->entityId}{$this->bundleId}";
  }

}
