<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Textfield;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Provides a Schema.org (type or property) autocomplete form element.
 *
 * @FormElement("schemadotorg_autocomplete")
 *
 * @see \Drupal\schemadotorg\Controller\SchemaDotOrgAutocompleteController::autocomplete
 * @see \Drupal\schemadotorg_report\Form\SchemaDotOrgReportFilterForm::buildForm
 * @see \Drupal\schemadotorg_ui\Form\SchemaDotOrgUiMappingTypeSelectForm::buildForm
 */
class SchemaDotOrgAutocomplete extends Textfield {

  /**
   * Schema.org type.
   */
  const SCHEMA_TYPES = SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES;

  /**
   * Schema.org property.
   */
  const SCHEMA_PROPERTIES = SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_PROPERTIES;

  /**
   * Schema.org Thing without Enumerations.
   *
   * This limits the autocompletion to just Things without Enumerations.
   *
   * @see \Drupal\schemadotorg\Controller\SchemaDotOrgAutocompleteController::autocomplete
   */
  const SCHEMA_THINGS = 'Thing';

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $class = static::class;

    $info['#target_type'] = static::SCHEMA_TYPES;
    $info['#tags'] = FALSE;
    $info['#action'] = NULL;
    $info['#novalidate'] = FALSE;

    $info['#element_validate'] = [[$class, 'validateSchemaDotOrgAutocomplete']];
    array_unshift($info['#process'], [$class, 'processSchemaDotOrgAutocomplete']);

    return $info;
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input === FALSE && isset($element['#default_value']) && is_array($element['#default_value'])) {
      $element['#default_value'] = implode(', ', $element['#default_value']);
    }
  }

  /**
   * Adds Schema.org ID autocomplete functionality to a form element.
   *
   * @param array $element
   *   The form element to process.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The form element.
   */
  public static function processSchemaDotOrgAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    $element['#autocomplete_route_name'] = 'schemadotorg.autocomplete';
    $element['#autocomplete_route_parameters'] = ['table' => $element['#target_type']];
    $element['#attributes']['class'][] = 'schemadotorg-autocomplete';
    if ($element['#action']) {
      $element['#attributes']['data-schemadotorg-autocomplete-action'] = $element['#action'];
      $element['#attached']['library'] = 'schemadotorg/schemadotorg.autocomplete';
    }
    return $element;
  }

  /**
   * Form element validation handler for entity_autocomplete elements.
   */
  public static function validateSchemaDotOrgAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $value = trim($element['#value']);
    $schema_ids = $value ? preg_split('/\s*,\s*/', $value) : [];

    // Validate the Schema.org type or property.
    if ($schema_ids && empty($element['#novalidate'])) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
      $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');

      $target_type = $element['#target_type'];
      $table = $target_type === static::SCHEMA_THINGS
        ? static::SCHEMA_TYPES
        : $target_type;

      foreach ($schema_ids as $schema_id) {
        // Check if this is valid Schema.org id (type or property)
        if (!$schema_type_manager->isId($table, $schema_id)) {
          $t_args = [
            '@type' => ($table === static::SCHEMA_TYPES) ? t('type') : t('property'),
            '%id' => $schema_id,
          ];
          $form_state->setError($element, t('The Schema.org @type %id is not valid.', $t_args));
        }

        // Check if Schema.org type is subtype of Thing and not a subtype
        // of Enumeration.
        if ($target_type === static::SCHEMA_THINGS) {
          if (!$schema_type_manager->isSubTypeOf($schema_id, ['Thing']) || $schema_type_manager->isSubTypeOf($schema_id, ['Enumeration'])) {
            $t_args = [
              '%thing' => 'Thing',
              '%id' => $schema_id,
            ];
            $form_state->setError($element, t('The Schema.org type %id is not a valid %thing.', $t_args));
          }
        }
      }
    }

    // For #tags convert the value to an array of ids.
    if ($element['#tags']) {
      $form_state->setValueForElement($element, array_combine($schema_ids, $schema_ids));
    }
  }

}
