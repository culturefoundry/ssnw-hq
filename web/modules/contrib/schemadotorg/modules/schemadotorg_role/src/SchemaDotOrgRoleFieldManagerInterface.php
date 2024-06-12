<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_role;

use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org role manager interface.
 */
interface SchemaDotOrgRoleFieldManagerInterface {

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
   * Replaces field creation form with text and edit links.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function mappingFormAlter(array &$form, FormStateInterface $form_state): void;

  /**
   * Add role field definitions to a content entity when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Get role field definitions.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string|null $bundle
   *   The bundle.
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return array
   *   The role field definitions.
   */
  public function getFieldDefinitions(string $entity_type_id = '', ?string $bundle = NULL, string $schema_type = ''): array;

  /**
   * Get role field definitions for a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return array
   *   The role field definitions for a Schema.org mapping.
   */
  public function getFieldDefinitionsFromMapping(SchemaDotOrgMappingInterface $mapping): array;

}
