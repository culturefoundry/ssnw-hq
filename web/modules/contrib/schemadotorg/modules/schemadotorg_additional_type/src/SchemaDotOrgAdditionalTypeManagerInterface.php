<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_additional_type;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\schemadotorg\SchemaDotOrgEntityFieldManagerInterface;

/**
 * Schema.org additional type interface.
 */
interface SchemaDotOrgAdditionalTypeManagerInterface {

  /**
   * Add new field mapping option.
   */
  const ADD_FIELD = SchemaDotOrgEntityFieldManagerInterface::ADD_FIELD;

  /**
   * The additional type field name suffix.
   */
  const FIELD_NAME_SUFFIX = '_type';

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
   *
   * @see hook_schemadotorg_mapping_defaults_alter()
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void;

  /**
   * Alter the Schema.org UI mapping form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function alterMappingForm(array &$form, FormStateInterface &$form_state): void;

  /**
   * Prepares the node form.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   * @param string $operation
   *   The operation being performed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function nodePrepareForm(NodeInterface $node, string $operation, FormStateInterface $form_state): void;

  /**
   * Alter the link variables.
   *
   * @param array $variables
   *   The link variables.
   */
  public function linkAlter(array &$variables): void;

}
