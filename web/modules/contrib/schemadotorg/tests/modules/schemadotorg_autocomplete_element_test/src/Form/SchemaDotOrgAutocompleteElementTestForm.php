<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_autocomplete_element_test\Form;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\schemadotorg\Element\SchemaDotOrgAutocomplete;

/**
 * Provides a Scheme.org Blueprint Autocomplete Element test form.
 */
class SchemaDotOrgAutocompleteElementTestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_autocomplete_element_test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['schemadotorg_autocomplete_type'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org type'),
      '#target_type' => SchemaDotOrgAutocomplete::SCHEMA_TYPES,
      '#default_value' => 'Person',
    ];
    $form['schemadotorg_autocomplete_types'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org types'),
      '#target_type' => SchemaDotOrgAutocomplete::SCHEMA_TYPES,
      '#tags' => TRUE,
      '#default_value' => ['Person', 'Organization'],
    ];
    $form['schemadotorg_autocomplete_novalidate'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org novalidate'),
      '#novalidate' => TRUE,
      '#default_value' => 'Dog',
    ];
    $form['schemadotorg_autocomplete_thing'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org thing'),
      '#target_type' => 'Thing',
      '#default_value' => 'Thing',
    ];
    $form['schemadotorg_autocomplete_property'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org property'),
      '#target_type' => SchemaDotOrgAutocomplete::SCHEMA_PROPERTIES,
      '#default_value' => 'name',
    ];
    $form['schemadotorg_autocomplete_properties'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org properties'),
      '#target_type' => SchemaDotOrgAutocomplete::SCHEMA_PROPERTIES,
      '#tags' => TRUE,
      '#default_value' => ['name', 'additionalName'],
    ];
    $form['schemadotorg_autocomplete_action_path'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org action path'),
      '#description' => $this->t('https://schema.org/'),
      '#action' => 'https://schema.org/',
    ];
    $form['schemadotorg_autocomplete_action_query'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org action query'),
      '#description' => $this->t('https://schema.org/docs/search_results.html?q='),
      '#action' => 'https://schema.org/docs/search_results.html?q=',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = $form_state->cleanValues()->getValues();
    $this->messenger()->addStatus(Markup::create('<pre>' . Yaml::encode($values) . '</pre>'));
  }

}
