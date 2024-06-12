<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_settings_element_test\Form;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Provides a Scheme.org Blueprint Settings Element test form.
 */
class SchemaDotOrgSettingsElementTestForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['schemadotorg_settings_element_test.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_settings_element_test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['schemadotorg_settings_element_test'] = [
      '#tree' => TRUE,
    ];

    // Create examples of all settings types.
    $settings_types = [
      'indexed',
      'indexed_grouped',
      'indexed_grouped_named',
      'associative',
      'associative_grouped',
      'associative_grouped_named',
      'links_grouped',
      'associative_advanced',
      'yaml',
      'yaml_raw',
      'json_raw',
    ];
    foreach ($settings_types as $settings_type) {
      $form['schemadotorg_settings_element_test'][$settings_type] = [
        '#type' => 'schemadotorg_settings',
        '#title' => $settings_type,
        '#mode' => str_starts_with($settings_type, 'json') ? 'json' : 'yaml',
        '#raw' => str_ends_with($settings_type, '_raw'),
      ];
    }

    // Add 'Browse Schema.org types.' to the first element.
    $form['schemadotorg_settings_element_test']['indexed']['#description_link'] = 'types';
    $form['schemadotorg_settings_element_test']['indexed']['#token_link'] = TRUE;

    // Add an example to the first element.
    $form['schemadotorg_settings_element_test']['indexed']['#example'] = '- one
- two
- three';

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
    // Save settings.
    $settings = $form_state->getValue('schemadotorg_settings_element_test');
    $this->config('schemadotorg_settings_element_test.settings')
      ->setData($settings)
      ->save();

    // Display the updated settings.
    $this->messenger()->addStatus(Markup::create('<pre>' . Yaml::encode($settings) . '</pre>'));
  }

}
