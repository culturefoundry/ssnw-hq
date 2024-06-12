<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_content_model_documentation;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Schema.org Content Model Documentation builder interface.
 */
interface SchemaDotOrgContentModelDocumentationBuilderInterface {

  /**
   * Alter the Content Model Documentation entity form to support Schema.org mappings.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   String representing the name of the form itself. Typically this is the
   *    name of the function that generated the form.
   */
  public function cmDocumentFormAlter(array &$form, FormStateInterface &$form_state, string $form_id): void;

  /**
   * Alter the displaying of Content Module Documentation.
   *
   * @param array &$build
   *   A renderable array representing the entity content.
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The entity object being rendered.
   * @param \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
   *   The entity view display holding the display options configured for the
   *    entity components.
   */
  public function cmDocumentViewAlter(array &$build, CMDocumentInterface $cm_document, EntityViewDisplayInterface $display): void;

  /**
   * Alter documentation markup field to open links in modal dialogs.
   *
   * @param array $element
   *   The field widget form element as constructed by
   *    \Drupal\Core\Field\WidgetBaseInterface::form().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $context
   *   An associative array. See hook_field_widget_single_element_form_alter()
   *    for the structure and content of the array.
   */
  public function fieldWidgetSingleElementMarkupFormAlter(array &$element, FormStateInterface $form_state, array $context): void;

  /**
   * Append Relationships operation to Schema.org mappings.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity on which the linked operations will be performed.
   */
  public function entityOperation(EntityInterface $entity): ?array;

  /**
   * Render Content Model Documentation help for an entity.
   *
   * @param string $route_name
   *   For page-specific help, use the route name as identified in the
   *    module's routing.yml file.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function help(string $route_name, RouteMatchInterface $route_match): array|NULL;

  /**
   * Hide node help content if the Content Model Documentation field exists.
   *
   * @param array $variables
   *   An array of block variables.
   */
  public function preprocessBlock(array &$variables): void;

}
