<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Schema.org report names routes.
 */
class SchemaDotOrgReportNamesController extends SchemaDotOrgReportControllerBase {

  /**
   * The Schema.org names service.
   */
  protected SchemaDotOrgNamesInterface $schemaDotOrgNames;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaDotOrgNames = $container->get('schemadotorg.names');
    return $instance;
  }

  /**
   * Builds the Schema.org names overview or table.
   *
   * @param string $display
   *   The Schema.org names display (overview or table).
   *
   * @return array
   *   A renderable array containing Schema.org names overview or table.
   */
  public function index(string $display): array {
    if ($display === 'overview') {
      return $this->overview();
    }
    else {
      return $this->table($display);
    }
  }

  /**
   * Builds the Schema.org names overview.
   *
   * @return array
   *   A renderable array containing Schema.org names overview.
   */
  public function overview(): array {
    $names = [];
    $abbreviated = [];
    $truncated = [];

    $prefixes = [];
    $suffixes = [];
    $abbreviations = [];

    $tables = ['types', 'properties'];
    foreach ($tables as $table) {
      $labels = $this->database->select('schemadotorg_' . $table, 't')
        ->fields('t', ['label'])
        ->orderBy('label')
        ->execute()
        ->fetchCol();

      foreach ($labels as $label) {
        // For types, we only care about Things and Intangibles.
        if ($table === 'types') {
          $is_enumeration = ($this->schemaTypeManager->isEnumerationValue($label) || $this->schemaTypeManager->isEnumerationType($label));
          if ($is_enumeration) {
            continue;
          }
        }

        $max_length = $this->schemaDotOrgNames->getNameMaxLength($table);
        $name = $this->schemaDotOrgNames->camelCaseToSnakeCase($label);
        $names[$name] = $label;

        $drupal_name = $this->schemaDotOrgNames->schemaIdToDrupalName($table, $label);
        $drupal_name_length = strlen($drupal_name);
        if ($drupal_name_length > $max_length) {
          $truncated[$name] = [
            'label' => $label,
            'name' => $drupal_name,
            'length' => $drupal_name_length,
            'max_length' => $max_length,
          ];
        }
        elseif ($name !== $drupal_name) {
          $abbreviated[$name] = $drupal_name;
        }

        $name_parts = explode('_', $name);
        if (count($name_parts) > 1) {
          $prefix_parts = array_slice($name_parts, 0, 2);
          $one_word_prefix = $prefix_parts[0];
          $prefixes += [$one_word_prefix => 0];
          $prefixes[$one_word_prefix]++;

          $two_word_prefix = implode('_', $prefix_parts);
          $prefixes += [$two_word_prefix => 0];
          $prefixes[$two_word_prefix]++;

          $suffix = end($name_parts);
          $suffixes += [$suffix => 0];
          $suffixes[$suffix]++;
        }

        reset($name_parts);
        foreach ($name_parts as $name_part) {
          $abbreviations += [$name_part => 0];
          $abbreviations[$name_part]++;
        }
      }
    }

    $build = [];

    // General.
    $build['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General summary'),
      '#open' => TRUE,
    ];
    $build['general']['names'] = [
      '#type' => 'item',
      '#title' => $this->t('Total names'),
      'value' => ['#plain_text' => count($names)],
    ];
    $build['general']['abbreviated'] = [
      '#type' => 'item',
      '#title' => $this->t('Total abbreviated names'),
      'value' => ['#plain_text' => count($abbreviated)],
    ];
    if ($truncated) {
      $build['general']['truncated'] = [
        '#type' => 'item',
        '#title' => $this->t('Total truncated names'),
        'value' => ['#plain_text' => count($truncated)],
        'table' => [
          '#type' => 'table',
          '#header' => [
            $this->t('Label'),
            $this->t('Name'),
            $this->t('Length'),
            $this->t('Max length'),
          ],
          '#rows' => $truncated,
          '#attributes' => ['class' => ['schemadotorg-report-table']],
        ],
      ];
    }

    // Usage.
    $build['usage'] = [
      '#type' => 'details',
      '#title' => $this->t('Usage summary'),
      '#open' => TRUE,
    ];
    $build['usage']['prefixes'] = $this->buildWordUsage(
      'prefixes',
      $prefixes,
      $names
    );
    $build['usage']['abbreviations'] = $this->buildWordUsage(
      'abbreviations',
      $abbreviations,
      $names
    );
    $build['usage']['suffixes'] = $this->buildWordUsage(
      'suffixes',
      $suffixes,
      $names
    );

    return $build;
  }

  /**
   * Build word usage.
   *
   * @param string $type
   *   The type of word usage.
   * @param array $words
   *   Words.
   * @param array $names
   *   Schema.org type and property names.
   *
   * @return array
   *   A renderable array containing word usage.
   */
  protected function buildWordUsage(string $type, array $words, array $names): array {
    $config = $this->config('schemadotorg.names');
    $replacements = $config->get($type);
    switch ($type) {
      case 'prefixes':
        $title = $this->t('Prefixes');
        $label = $this->t('Prefix');
        $pattern = '/^%s_/';
        break;

      case 'abbreviations':
        $title = $this->t('Abbreviations');
        $label = $this->t('Abbreviation');
        $pattern = '/_%s_/';
        break;

      case 'suffixes':
        $title = $this->t('Suffixes');
        $label = $this->t('Suffix');
        $pattern = '/_%s$/';
        break;

      default:
        throw new \Exception("Word usage $type not supported");
    }

    // Remove words that are less than 5 characters.
    $words = array_filter(
      $words,
      fn($word) => strlen($word) > 5,
      ARRAY_FILTER_USE_KEY
    );

    // Remove words that are only used once.
    $words = array_filter(
      $words,
      fn($usage) => $usage > 1
    );

    // Sort by usage.
    asort($words, SORT_NUMERIC);
    $words = array_reverse($words);

    // Find unused replacements.
    $names = array_keys($names);
    $unused_replacements = [];
    foreach ($replacements as $search => $replacement) {
      if (!preg_grep(sprintf($pattern, $search), $names)) {
        $unused_replacements[$search] = $search;
      }
    }

    $build = [
      '#type' => 'details',
      '#title' => $title,
    ];

    // Words.
    $header = [
      'word' => $label,
      'word_usage' => $this->t('Used'),
      'abbreviation' => $this->t('Abbreviation'),
    ];
    $rows = [];
    foreach ($words as $word => $usage) {
      $replacement = $replacements[$word] ?? '';
      $row = [];
      $row['word'] = $word;
      $row['word_usage'] = $usage;
      $row['abbreviation'] = $replacement;
      if ($replacement) {
        $rows[] = ['data' => $row, 'class' => ['color-success']];
      }
      else {
        $rows[] = $row;
      }
    }
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => ['class' => ['schemadotorg-report-table']],
    ];

    // Replacements.
    $used_replacements_rows = [];
    $unused_replacements_rows = [];
    foreach ($replacements as $source => $abbreviation) {
      if (isset($unused_replacements[$source])) {
        $unused_replacements_rows[$source] = [$source, $abbreviation];
      }
      else {
        $used_replacements_rows[$source] = [$source, $abbreviation];
      }
    }
    $build['used_replacements'] = [
      '#type' => 'details',
      '#title' => $this->t('Used replacements'),
      'table' => [
        '#type' => 'table',
        '#header' => [$label, $this->t('Replacement')],
        '#rows' => $used_replacements_rows,
        '#attributes' => ['class' => ['schemadotorg-report-table']],
      ],
    ];
    if ($unused_replacements_rows) {
      $build['unused_replacements'] = [
        '#type' => 'details',
        '#title' => $this->t('Unused replacements'),
        'table' => [
          '#type' => 'table',
          '#header' => [$label, $this->t('Replacement')],
          '#rows' => $unused_replacements_rows,
          '#attributes' => ['class' => ['schemadotorg-report-table']],
        ],
      ];
    }
    return $build;
  }

  /**
   * Builds the Schema.org names table.
   *
   * @param string $display
   *   The Schema.org names display (overview or table).
   *
   * @return array
   *   A renderable array containing Schema.org names table.
   */
  public function table(string $display): array {
    $tables = ['types', 'properties'];
    $is_schema_item = in_array($display, $tables);

    $header = [
      'schema_item' => [
        'data' => $this->t('Schema.org item'),
      ],
      'schema_id' => [
        'data' => $this->t('Schema.org ID'),
      ],
      'schema_label' => [
        'data' => $this->t('Schema.org label'),
      ],
      'original_name' => [
        'data' => $this->t('Original name'),
      ],
      'original_name_length' => [
        'data' => $this->t('#'),
      ],
      'drupal_name' => [
        'data' => $this->t('Drupal name'),
      ],
      'drupal_name_length' => [
        'data' => $this->t('#'),
      ],
    ];

    if ($is_schema_item) {
      $tables = [$display];
      unset($header['schema_item']);
    }

    $rows = [];
    foreach ($tables as $table) {
      $schema_ids = $this->database->select('schemadotorg_' . $table, $table)
        ->fields($table, ['label'])
        ->orderBy('label')
        ->execute()
        ->fetchCol();
      $max_length = $this->schemaDotOrgNames->getNameMaxLength($table);
      foreach ($schema_ids as $schema_id) {
        // For types, we only care about Things and Intangibles.
        if ($table === 'types') {
          $is_enumeration = ($this->schemaTypeManager->isEnumerationValue($schema_id) || $this->schemaTypeManager->isEnumerationType($schema_id));
          if ($is_enumeration) {
            continue;
          }
        }

        $schema_item = ($table === 'types') ? $this->t('Type') : $this->t('Properties');
        $schema_label = $this->schemaDotOrgNames->schemaIdToDrupalLabel($table, $schema_id);
        $original_name = $this->schemaDotOrgNames->camelCaseToSnakeCase($schema_id);
        $original_name_length = strlen($original_name);
        $drupal_name = $this->schemaDotOrgNames->schemaIdToDrupalName($table, $schema_id);
        $drupal_name_length = strlen($drupal_name);

        $row = [];
        if (!$is_schema_item) {
          $row['schema_item'] = $schema_item;
        }
        $row['schema_id'] = [
          'data' => [
            '#type' => 'link',
            '#title' => $schema_id,
            '#url' => $this->schemaTypeBuilder->getItemUrl($schema_id),
          ],
        ];
        $row['schema_label'] = $schema_label;
        $row['original_name'] = $original_name;
        $row['original_name_length'] = $original_name_length;
        $row['drupal_name'] = $drupal_name;
        $row['drupal_name_length'] = $drupal_name_length;

        if ($drupal_name_length > $max_length) {
          $class = ['color-error'];
        }
        elseif ($original_name !== $drupal_name) {
          $class = ['color-warning'];
        }
        else {
          $class = [];
        }
        if ($display !== 'abbreviations' || $class) {
          $rows[$schema_id] = ['data' => $row];
          $rows[$schema_id]['class'] = $class;
        }
      }
    }
    ksort($rows);

    $build = [];
    $build['info'] = $this->buildInfo($display, count($rows));
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => ['class' => ['schemadotorg-report-table']],
    ];
    return $build;
  }

}
