<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\Config;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for configuring Schema.org Blueprints settings.
 */
abstract class SchemaDotOrgSettingsFormBase extends ConfigFormBase {

  /**
   * The module handler.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config_names = $this->getEditableConfigNames();
    $config_name = reset($config_names);
    $config = $this->config($config_name);

    // Set the default values for the form being built.
    // Sub-modules default values are set via a form alter hook.
    // @see \Drupal\schemadotorg\Form\SchemaDotOrgSettingsFormBase::formAlter
    $settings_name = explode('.', $config_name)[1];
    if (isset($form[$settings_name])) {
      $elements =& $form[$settings_name];
    }
    else {
      $elements =& $form;
    }
    static::setElementRecursive($elements, $config);

    $form['#tree'] = TRUE;
    $form['#after_build'][] = [get_class($this), 'afterBuildDetails'];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Form #after_build callback: Track details element's open/close state.
   */
  public static function afterBuildDetails(array $form, FormStateInterface $form_state): array {
    $form_id = $form_state->getFormObject()->getFormId();

    // Only open the first details element.
    $is_first = ($form_id !== 'schemadotorg_general_settings_form');
    $has_details = FALSE;
    foreach (Element::children($form) as $child_key) {
      if (NestedArray::getValue($form, [$child_key, '#type']) === 'details') {
        $form[$child_key]['#open'] = $is_first;
        $is_first = FALSE;
        $has_details = TRUE;
        $form[$child_key]['#attributes']['data-schemadotorg-details-key'] = "details-$form_id-$child_key";
      }
    }
    $form['#attached']['library'][] = 'schemadotorg/schemadotorg.details';

    // Make sure all the schemadotorg_* module settings are sorted alphabetically.
    $weight = 0;
    $keys = Element::children($form);
    sort($keys);
    foreach ($keys as $key) {
      if (str_starts_with($key, 'schemadotorg_')
        && !NestedArray::keyExists($form, [$key, 'weight'])) {
        $form[$key]['#weight'] = $weight++;
      }
    }

    // Hide the submit button if the form has no details elements.
    if (!$has_details) {
      $form['actions']['#access'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Update configuration for schemadotorg_* sub-modules.
    foreach (Element::children($form) as $element_key) {
      if (str_starts_with($element_key, 'schemadotorg_')
        && $this->moduleHandler->moduleExists($element_key)
        && !$this->configFactory()->get($element_key . '.settings')->isNew()) {
        $config = $this->configFactory()->getEditable($element_key . '.settings');
        $data = $config->getRawData();
        $values = $form_state->getValue($element_key);
        foreach ($values as $key => $value) {
          if (array_key_exists($key, $data)) {
            $config->set($key, $value);
          }
        }
        $config->save();
      }
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * Alter Schema.org settings forms.
   *
   * Automatically set the default values and additional properties for
   * Schema.org settings forms that are altered by sub-modules.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @see schemadotorg_form_alter()
   */
  public static function formAlter(array &$form, FormStateInterface $form_state): void {
    if (!$form_state->getFormObject() instanceof SchemaDotOrgSettingsFormBase) {
      return;
    }

    foreach (Element::children($form) as $module_name) {
      $config = \Drupal::configFactory()->getEditable("$module_name.settings");
      if (!$config->isNew()) {
        static::setElementRecursive($form[$module_name], $config);
      }
    }
  }

  /**
   * Set Schema.org settings form element properties and default values.
   *
   * @param array $element
   *   A form element.
   * @param \Drupal\Core\Config\Config $config
   *   The form elements associated module config.
   * @param array $parents
   *   The form element's parent and config key path.
   */
  protected static function setElementRecursive(array &$element, Config $config, array $parents = []): void {
    $children = Element::children($element);
    if ($children) {
      foreach ($children as $child) {
        static::setElementRecursive($element[$child], $config, array_merge($parents, [$child]));
      }
    }
    elseif (isset($element['#type'])) {
      // Set checkbox #return_value to TRUE.
      if ($element['#type'] === 'checkbox') {
        $element['#return_value'] = $element['#return_value'] ?? TRUE;
      }

      // Set checkboxes #element_validate callback to filter submitted values.
      // @see \Drupal\schemadotorg\Utility\SchemaDotOrgElementHelper::validateCheckboxes
      if ($element['#type'] === 'checkboxes') {
        $element['#element_validate'][] = '::validateCheckboxes';
      }

      // Set the default value for the config settings.
      $config_key = implode('.', $element['#parents'] ?? $parents);
      $config_value = $config->get($config_key);
      if (!isset($element['#default_value']) && !is_null($config_value)) {
        $element['#default_value'] = $config_value;
      }
    }
  }

  /**
   * Form API callback. Remove unchecked options from #value array.
   */
  public static function validateCheckboxes(array &$element, FormStateInterface $form_state, array &$completed_form): void {
    $values = $element['#value'] ?: [];
    // Filter unchecked/unselected options whose value is 0.
    $values = array_filter(
      $values,
      fn($value) => $value !== 0
    );
    $values = array_values($values);
    $form_state->setValueForElement($element, $values);
  }

}
