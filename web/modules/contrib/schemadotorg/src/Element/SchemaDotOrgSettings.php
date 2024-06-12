<?php

/* phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint */
/* phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint */

declare(strict_types=1);

namespace Drupal\schemadotorg\Element;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element\Textarea;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides a form element for Schema.org Blueprints settings.
 *
 * @FormElement("schemadotorg_settings")
 */
class SchemaDotOrgSettings extends Textarea {

  /**
   * Settings modes mapped to CodeMirror modes.
   */
  protected static array $modes = [
    'yaml' => 'yaml',
    'json' => 'application/ld+json',
  ];

  /**
   * Settings modes mapped to CodeMirror libraries.
   */
  protected static array $libraries = [
    'yaml' => 'schemadotorg/codemirror.yaml',
    'json' => 'schemadotorg/codemirror.javascript',
  ];

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#process' => [
        [$class, 'processSchemaDotOrgSettings'],
        [$class, 'processAjaxForm'],
        [$class, 'processGroup'],
      ],
      '#description' => '',
      '#description_link' => '',
      '#token_link' => FALSE,
      '#token_types' => [],
      '#example' => '',
      '#config_name' => '',
      '#config_key' => '',
      '#attributes' => ['wrap' => 'off'],
      '#mode' => 'yaml',
      '#raw' => FALSE,
    ] + parent::getInfo();
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input === FALSE) {
      $config_name = static::getConfigName($element);
      $config_key = static::getConfigKey($element);
      $element['#default_value'] = \Drupal::config($config_name)->get($config_key)
        ?: $element['#default_value']
        ?? NULL;
    }
    elseif (!$element['#raw'] && is_string($input)) {
      try {
        return static::decode($element['#mode'], $input);
      }
      catch (InvalidDataTypeException $exception) {
        // Do nothing and allow validation to catch the exception.
      }
    }
  }

  /**
   * Processes a 'schemadotorg_settings' element.
   */
  public static function processSchemaDotOrgSettings(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    $mode = $element['#mode'];
    $raw = $element['#raw'];

    if (!$raw) {
      if (isset($element['#default_value']) && is_array($element['#default_value'])) {
        $element['#default_value'] = static::encode($mode, $element['#default_value']);
      }
      if (isset($element['#value']) && is_array($element['#value'])) {
        $element['#value'] = static::encode($mode, $element['#value']);
      }
    }

    // Append token tree link to the description.
    if ($element['#token_link']
      && \Drupal::moduleHandler()->moduleExists('token')) {
      // Build the token tree link.
      $build = [
        '#theme' => 'token_tree_link',
        '#token_types' => $element['#token_types'],
      ];

      // If token types are empty, set the token types to support mapping types.
      if (empty($build['#token_types'])) {
        $mapping_types = \Drupal::entityTypeManager()
          ->getStorage('schemadotorg_mapping_type')
          ->loadMultiple();
        if ($mapping_types) {
          $mapping_type_ids = array_keys($mapping_types);
          $token_types = array_combine($mapping_type_ids, $mapping_type_ids);
          if (isset($token_types['taxonomy_term'])) {
            $token_types['term'] = 'term';
          }
          $build['#token_types'] = $token_types;
        }
      }

      // Render and append the token tree link to the description.
      /** @var \Drupal\Core\Render\RendererInterface $renderer */
      $renderer = \Drupal::service('renderer');
      $element['#description'] = $element['#description'] ?? '';
      $element['#description'] .= '<br/>';
      $element['#description'] .= $renderer->render($build);
    }

    // Append Schema.org browse types or properties link to the description.
    $link_table = $element['#description_link'];
    if (in_array($link_table, ['types', 'properties'])
      && \Drupal::moduleHandler()->moduleExists('schemadotorg_report')) {
      $link_text = ($link_table === 'types')
        ? t('Browse Schema.org types.')
        : t('Browse Schema.org properties.');
      $link_url = Url::fromRoute("schemadotorg_report.$link_table");
      $element['#description'] .= (!empty($element['#description'])) ? ' ' : '';
      $element['#description'] .= '<span class="schemadotorg-settings-browse">' . Link::fromTextAndUrl($link_text, $link_url)->toString() . '</span>';
      $element['#attached']['library'][] = 'schemadotorg/schemadotorg.dialog';
    }

    // Append an example to the description.
    if ($element['#example']) {
      // Make sure the example if valid.
      static::validate($mode, $element['#example']);

      $id = $element['#id'];
      $element['#description'] = [
        'content' => [
          '#markup' => $element['#description'],
        ],
        'example' => [
          '#type' => 'inline_template',
          '#template' => '<div class="schemadotorg-settings-example">
  <div class="schemadotorg-settings-example--link"><a role="button" href="#{{ id }}-example">{{ "Example"|t }}</a></div>
  <div class="schemadotorg-settings-example--content" id="{{ id }}-example">
    <pre data-schemadotorg-codemirror-mode="{{ mode }}">{{ example }}</pre>
  </div>
</div>',
          '#context' => [
            'mode' => static::$modes[$mode],
            'id' => $id,
            'example' => $element['#example'],
          ],
        ],
      ];
    }

    // Set CodeMirror class and mode attributes and attach the library.
    $element['#attributes']['class'][] = 'schemadotorg-codemirror';
    $element['#attributes']['data-mode'] = static::$modes[$mode];
    $element['#attached']['library'][] = static::$libraries[$mode];

    // Attach the library.
    $element['#attached']['library'][] = 'schemadotorg/schemadotorg.settings.element';

    // Set validation.
    $element += ['#element_validate' => []];
    array_unshift($element['#element_validate'], [static::class, 'validateSchemaDotOrgSettings']);
    return $element;
  }

  /**
   * Form element validation handler for #type 'schemadotorg_settings'.
   */
  public static function validateSchemaDotOrgSettings(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $mode = $element['#mode'];
    $raw = $element['#raw'];

    // Validate the raw YAML or JSON string.
    try {
      static::validate($mode, $element['#value']);
    }
    catch (\Exception $exception) {
      $t_args = [
        '@name' => $element['#title'],
        '@mode' => strtoupper($mode),
        '%error' => $exception->getMessage(),
      ];
      $form_state->setError($element, t('@name field is not valid @mode. %error', $t_args));
      return;
    }

    // Exit, if we are dealing the raw YAML or JSON string.
    if ($raw) {
      return;
    }

    // Convert element value to settings data array.
    $settings = static::decode($mode, $element['#value']);
    $form_state->setValueForElement($element, $settings);

    // Validate the settings against the config's schema.
    $config_name = static::getConfigName($element);
    $config_key = static::getConfigKey($element);
    if ($config_name && $config_key) {
      /** @var \Drupal\schemadotorg\SchemaDotOrgConfigManagerInterface $schema_config_manager */
      $schema_config_manager = \Drupal::service('schemadotorg.config_manager');
      $t_args = ['@name' => $element['#title']];
      try {
        $errors = $schema_config_manager->checkConfigValue($config_name, $config_key, $settings);
        if (is_array($errors)) {
          // Prefix the error with the exact config key triggering the error.
          [, $error_config_key] = explode(':', array_key_first($errors));
          $t_args['%error'] = $error_config_key . ' - ' . reset($errors);
          $form_state->setError($element, new TranslatableMarkup('@name field is invalid.<br/>%error', $t_args));
        }
      }
      catch (\Exception $exception) {
        $t_args['%error'] = $exception->getMessage();
        $form_state->setError($element, new TranslatableMarkup('@name field is invalid.<br/>%error', $t_args));
      }
    }
  }

  /* ************************************************************************ */
  // Decode and encode methods.
  /* ************************************************************************ */

  /**
   * Validate YAML or JSON.
   *
   * @param string $mode
   *   The data's mode (YAML or JSON).
   * @param string|null $raw
   *   The raw data YAML or JSON string to be decoded.
   *
   * @throws \Exception
   *   Throw an exception when the raw data YAML or JSON string
   *   can't be decoded.
   */
  public static function validate(string $mode, ?string $raw): void {
    if ($raw === '') {
      return;
    }

    switch ($mode) {
      case 'yaml':
        Yaml::decode($raw);
        return;

      case 'json':
        // Replace all tokens with 'null' to allow the JSON to be validated.
        $raw = preg_replace('#\[[a-z][^]]+\]#', 'null', $raw);
        @json_decode($raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
          throw new \Exception(json_last_error_msg());
        }
        return;

      default;
        throw new \Exception('Unknown "' . $mode . '" settings mode.');
    }
  }

  /**
   * Decodes YAML or JSON into an array.
   *
   * @param string $mode
   *   The data's mode (YAML or JSON).
   * @param string|null $raw
   *   The raw data YAML or JSON string to be decoded.
   *
   * @return array
   *   The raw data YAML or JSON string decoded into an array.
   */
  protected static function decode(string $mode, ?string $raw): array {
    return match ($mode) {
      'yaml' => $raw ? Yaml::decode($raw) : [],
      'json' => $raw ? Json::decode($raw) : [],
      default => throw new \Exception('Unknown "' . $mode . '" settings mode.'),
    };
  }

  /**
   * Encodes data into YAML or JSON.
   *
   * @param string $mode
   *   The data's mode (YAML or JSON).
   * @param array|null $data
   *   The data to encode.
   *
   * @return string
   *   The data encoded into YAML.
   */
  protected static function encode(string $mode, ?array $data): string {
    switch ($mode) {
      case 'yaml':
        $yaml = $data ? Yaml::encode($data) : '';
        // Remove return after array delimiter.
        $yaml = preg_replace('#((?:\n|^)[ ]*-)\n[ ]+(\w|[\'"])#', '\1 \2', $yaml);
        return $yaml;

      case 'json':
        return ($data) ? Json::encode($data) : '';

      default;
        throw new \Exception('Unknown "' . $mode . '" settings mode.');
    }
  }

  /* ************************************************************************ */
  // Config methods.
  /* ************************************************************************ */

  /**
   * Get the config key.
   *
   * @param array $element
   *   The Schema.org settings form element.
   *
   * @return string
   *   The config key.
   */
  protected static function getConfigName(array $element): string {
    static::setConfigKeyProperty($element);
    return $element['#config_name'];
  }

  /**
   * Get the config key.
   *
   * @param array $element
   *   The Schema.org settings form element.
   *
   * @return string
   *   The config key.
   */
  protected static function getConfigKey(array $element): string {
    static::setConfigKeyProperty($element);
    return $element['#config_key'];
  }

  /**
   * Set config name and key from the element's parents.
   *
   * This assumes the element has two parents which are the module name
   * and the config key.
   *
   * @param array &$element
   *   The Schema.org settings form element.
   *
   * @see MODULE_form_schemadotorg_types_settings_form_alter
   * @see MODULE_form_schemadotorg_properties_settings_form_alter
   */
  protected static function setConfigKeyProperty(array &$element): void {
    if ($element['#config_name'] || $element['#config_key']) {
      return;
    }

    $configs = [];

    // Get config name/key via [MODULE_NAME][KEY][KEY].
    $parents = $element['#parents'];
    $module_name = array_shift($parents);
    $config_key = implode('.', $parents);
    $config_name = $module_name . '.settings';
    $configs[] = [$config_name, $config_key];

    // Get config name/key via [CONFIG_NAME][CONFIG_KEY][CONFIG_KEY].
    $parents = $element['#parents'];
    $settings_name = array_shift($parents);
    $config_key = implode('.', $parents);
    $config_name = 'schemadotorg.' . $settings_name;
    $configs[] = [$config_name, $config_key];

    // Get config name/key via [CONFIG_KEY][CONFIG_KEY][CONFIG_KEY].
    $config_key = implode('.', $element['#parents']);
    $config_names = ['schemadotorg.settings', 'schemadotorg.names'];
    foreach ($config_names as $config_name) {
      $configs[] = [$config_name, $config_key];
    }

    foreach ($configs as $config) {
      [$config_name, $config_key] = $config;
      if ($config_key
        && !is_null(\Drupal::config($config_name)->get($config_key))) {
        $element['#config_name'] = $config_name;
        $element['#config_key'] = $config_key;
        return;
      }
    }
  }

}
