<?php

namespace Drupal\content_model_documentation;

/**
 * Commons methods for FieldsReportManagerInterface services.
 */
interface FieldsReportManagerInterface {

  /**
   * Get entity type entities.
   *
   * @param array $entity_type_ids
   *   (optional) A given list of entity type IDs.
   *
   * @return array
   *   The list of entity type entities.
   */
  public function getContentEntityTypes(array $entity_type_ids = NULL);

  /**
   * Get all active field definitions.
   *
   * @param array $entity_type_ids
   *   (optional) A given list of entity type IDs.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   The list of field definitions, keyed by the entity type.
   */
  public function getFieldDefinitions(array $entity_type_ids = NULL);

  /**
   * Gets the definition of a field.
   *
   * @param string $entity_type_id
   *   A given entity type.
   * @param string $name
   *   The name of the field.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface|null
   *   The definition of the field or null if the field does not exist.
   */
  public function getFieldDefinition(string $entity_type_id, string $name);

}
