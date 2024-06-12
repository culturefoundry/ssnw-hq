<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_mappings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org additional mappings manager interface.
 */
interface SchemaDotOrgAdditionalMappingsManagerInterface {

  /**
   * Alter Schema.org mapping entity default values.
   *
   * @param array $defaults
   *   The Schema.org mapping entity default values.
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void;

  /**
   * Alter the Schema.org Blueprints UI mapping form.
   *
   * Adds a 'Additional mappings' details widget.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function mappingFormAlter(array &$form, FormStateInterface $form_state): void;

  /**
   * Add additional mappings to an entity after a mapping is inserted or updated.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingPostSave(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Get default additional mappings for a mapping.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   Default additional mappings for a mapping.
   */
  public function getDefaultAdditionalMappings(string $entity_type_id, ?string $bundle, string $schema_type): array;

}
