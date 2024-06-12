<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;

/**
 * Schema.org names service.
 *
 * The Schema.org names service converts Schema.org's naming convention and
 * labels into Drupal friendly machine names and labels.
 *
 * For example, Schema.org uses camel-case for ids and labels, while
 * Drupal uses snake case with character limits for ids and uses sentence case
 * for most labels.
 */
class SchemaDotOrgNames implements SchemaDotOrgNamesInterface {

  /**
   * Constructs a SchemaDotOrgNames object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getFieldPrefix(): string {
    return $this->configFactory
      ->get('schemadotorg_field_prefix.settings')
      ->get('field_prefix') ?? 'schema_';
  }

  /**
   * {@inheritdoc}
   */
  public function getNameMaxLength(string $table): int {
    if ($table === 'properties') {
      return 32 - strlen($this->getFieldPrefix());
    }
    else {
      $config_names = $this->configFactory->listAll('schemadotorg.schemadotorg_mapping_type.');
      $prefix_lengths = [0];
      foreach ($config_names as $config_name) {
        $prefix_lengths[] = strlen((string) $this->configFactory->get($config_name)->get('id_prefix'));
      }
      return 32 - max($prefix_lengths);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function snakeCaseToUpperCamelCase(string $string): string {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
  }

  /**
   * {@inheritdoc}
   */
  public function snakeCaseToCamelCase(string $string): string {
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
  }

  /**
   * {@inheritdoc}
   */
  public function camelCaseToSnakeCase(string $string): string {
    $intermediate = preg_replace('/(?!^)([[:upper:]][[:lower:]]+)/', '_$0', $string);
    $snake_case = preg_replace('/(?!^)([[:lower:]])([[:upper:]])/', '$1_$2', $intermediate);
    return strtolower($snake_case);
  }

  /**
   * {@inheritdoc}
   */
  public function camelCaseToTitleCase(string $string): string {
    // CamelCase to Title Case PHP Regex.
    // @see https://gist.github.com/justjkk/1402061
    $intermediate = preg_replace('/(?!^)([[:upper:]][[:lower:]]+)/', ' $0', $string);
    $title = preg_replace('/(?!^)([[:lower:]])([[:upper:]])/', '$1 $2', $intermediate);

    // Custom words.
    $custom_words = $this->getNamesConfig()->get('custom_words');
    if ($custom_words) {
      foreach ($custom_words as $search => $replace) {
        $title = str_replace($search, $replace, $title);
      }
    }

    // Acronyms.
    $acronyms = $this->getNamesConfig()->get('acronyms');
    if ($acronyms) {
      $title = preg_replace_callback(
        '/(\b)(' . implode('|', $acronyms) . ')(\b)/i',
        fn($matches) => $matches[1] . strtoupper($matches[2]) . $matches[3],
        $title
      );
    }

    // Minor words.
    $minor_words = $this->getNamesConfig()->get('minor_words');
    if ($minor_words) {
      $title = preg_replace_callback(
        '/ (' . implode('|', $minor_words) . ')(\b)/i',
        fn($matches) => ' ' . strtolower($matches[1]) . $matches[2],
        $title
      );
    }

    return ucfirst($title);
  }

  /**
   * {@inheritdoc}
   */
  public function camelCaseToSentenceCase(string $string): string {
    $sentence = $this->camelCaseToTitleCase($string);
    $sentence = preg_replace_callback(
      '/ ([A-Z])([a-z])/',
      fn($matches) => ' ' . strtolower($matches[1]) . $matches[2],
      $sentence
    );
    return ucfirst($sentence);
  }

  /**
   * {@inheritdoc}
   */
  public function camelCaseToDrupalName(string $string, array $options = []): string {
    $max_length = $options['maxlength'] ?? NULL;
    $truncate = $options['truncate'] ?? FALSE;

    $drupal_name = $this->camelCaseToSnakeCase($string);

    // Custom names.
    $custom_names = $this->getNamesConfig()->get('custom_names');
    if (isset($custom_names[$drupal_name])) {
      return $custom_names[$drupal_name];
    }

    // Prefixes.
    // NOTE: Prefixes are always applied to names to ensure consistency when
    // visually scanning names.
    $prefixes = $this->getNamesConfig()->get('prefixes');
    foreach ($prefixes as $search => $replace) {
      $drupal_name = preg_replace('/^' . $search . '_/', $replace . '_', $drupal_name);
    }
    if (!$max_length || strlen($drupal_name) <= $max_length) {
      return $drupal_name;
    }

    // Abbreviations.
    $abbreviations = $this->getNamesConfig()->get('abbreviations');
    foreach ($abbreviations as $search => $replace) {
      $drupal_name = preg_replace('/_' . $search . '_/', '_' . $replace . '_', $drupal_name);
    }
    if (strlen($drupal_name) <= $max_length) {
      return $drupal_name;
    }

    // Suffixes.
    $suffixes = $this->getNamesConfig()->get('suffixes');
    foreach ($suffixes as $search => $replace) {
      $drupal_name = preg_replace('/_' . $search . '$/', '_' . $replace, $drupal_name);
    }

    // Truncate.
    if ($truncate && strlen($drupal_name) > $max_length) {
      $drupal_name = substr($drupal_name, 0, $max_length);
      $drupal_name = rtrim($drupal_name, '_');
    }

    return $drupal_name;
  }

  /**
   * {@inheritdoc}
   */
  public function schemaIdToDrupalLabel(string $table, string $string): string {
    return ($table === 'types')
      ? $this->camelCaseToTitleCase($string)
      : $this->camelCaseToSentenceCase($string);
  }

  /**
   * {@inheritdoc}
   */
  public function schemaIdToDrupalName(string $table, string $string): string {
    $max_length = $this->getNameMaxLength($table);
    return $this->camelCaseToDrupalName($string, ['maxlength' => $max_length]);
  }

  /**
   * Get the Schema.org settings configuration.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The Schema.org settings configuration.
   */
  protected function getSettingsConfig(): ImmutableConfig {
    return $this->configFactory->get('schemadotorg.settings');
  }

  /**
   * Get the Schema.org names configuration.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The Schema.org names configuration.
   */
  protected function getNamesConfig(): ImmutableConfig {
    return $this->configFactory->get('schemadotorg.names');
  }

}
