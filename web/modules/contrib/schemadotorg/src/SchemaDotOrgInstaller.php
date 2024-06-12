<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\schemadotorg\Utility\SchemaDotOrgStringHelper;

/**
 * Schema.org installer service.
 *
 * The Schema.org installer service creates the 'schemadotorg_types' and
 * 'schemadotorg_properties' database tables and populates these tables using
 * the CSV data provided by Schema.org.
 *
 * This service also checks the requirements for installing the Schema.org
 * Blueprints module and allows Schema.org label and comments to be translated
 * using Drupal's string translation system.
 *
 * @see https://github.com/schemaorg/schemaorg/tree/main/data
 * @see data/VERSION/schemaorg-current-https-types.csv
 * @see data/VERSION/schemaorg-current-https-properties.csv
 * @see data/VERSION/schemaorg-current-https-properties.csv
 * @see data/VERSION/schemaorg-current-https-types.csv
 */
class SchemaDotOrgInstaller implements SchemaDotOrgInstallerInterface {
  use StringTranslationTrait;

  /**
   * Schema.org version.
   */
  const VERSION = '26.0';

  /**
   * Constructs a SchemaDotOrgInstaller object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected Connection $database,
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function requirements(string $phase): array {
    if ($phase !== 'runtime') {
      return [];
    }

    $requirements = [];
    $this->checkNamesRequirements($requirements);
    $this->checkRecommendedRequirements($requirements);
    $this->checkIntegrationRequirements($requirements);
    return $requirements;
  }

  /**
   * Check for Schema.org names requirements.
   *
   * @param array &$requirements
   *   An associative array of requirements for status reporting.
   *
   * @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportNamesController::overview
   */
  protected function checkNamesRequirements(array &$requirements): void {
    $truncated = [];

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

        $max_length = $this->schemaNames->getNameMaxLength($table);
        $name = $this->schemaNames->camelCaseToSnakeCase($label);
        $drupal_name = $this->schemaNames->schemaIdToDrupalName($table, $label);
        $drupal_name_length = strlen($drupal_name);
        if ($drupal_name_length > $max_length) {
          $truncated[$name] = [
            'label' => $label,
            'name' => $drupal_name,
            'length' => $drupal_name_length,
            'max_length' => $max_length,
          ];
        }
      }
    }

    if ($truncated) {
      $requirements['schemadotorg_names'] = [
        'title' => $this->t('Schema.org Blueprints: Names'),
        'value' => $this->t('Schema.org type and property names are being truncated. Please update the <a href=":href">Schema.org names settings</a>.', [':href' => Url::fromRoute('schemadotorg.settings.names')->toString()]),
        'description' => [
          'content' => [
            '#markup' => $this->t('The below type and property names need to truncated.'),
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ],
          'table' => [
            '#type' => 'table',
            '#header' => [
              $this->t('Label'),
              [
                'data' => $this->t('Name'),
                'class' => [RESPONSIVE_PRIORITY_LOW],
              ],
              [
                'data' => $this->t('Length'),
                'class' => [RESPONSIVE_PRIORITY_LOW],
              ],
              [
                'data' => $this->t('Max length'),
                'class' => [RESPONSIVE_PRIORITY_LOW],
              ],
            ],
            '#rows' => $truncated,
          ],
        ],
        'severity' => REQUIREMENT_ERROR,
      ];
    }
  }

  /**
   * Check for recommended modules.
   *
   * @param array &$requirements
   *   An associative array of requirements for status reporting.
   */
  protected function checkRecommendedRequirements(array &$requirements): void {
    $recommended_modules = $this->configFactory
      ->get('schemadotorg.settings')
      ->get('requirements.recommended_modules');
    if (!$recommended_modules) {
      return;
    }

    // NOTE: Suggestions are also included the Schema.org Blueprints
    // composer.json file.
    $recommended_modules = [
      'address' => [
        'title' => $this->t('Address'),
        'description' => $this->t('Provides functionality for storing, validating and displaying international postal addresses.'),
        'uri' => 'https://www.drupal.org/project/address',
      ],
      'datetime' => [
        'title' => $this->t('Datetime'),
        'description' => $this->t('Defines datetime form elements and a datetime field type.'),
        'uri' => 'https://www.drupal.org/docs/8/core/modules/datetime',
      ],
      'link' => [
        'title' => $this->t('Link'),
        'description' => $this->t('Provides a simple link field type.'),
        'uri' => 'https://www.drupal.org/docs/8/core/modules/link',
      ],
      'media' => [
        'title' => $this->t('Media'),
        'description' => $this->t('Manages the creation, configuration, and display of media items.'),
        'uri' => 'https://www.drupal.org/docs/8/core/modules/media',
      ],
      'media_library' => [
        'title' => $this->t('Media Library'),
        'description' => $this->t('Enhances the media list with additional features to more easily find and use existing media items.'),
        'uri' => 'https://www.drupal.org/docs/8/core/modules/media_library',
      ],
      'redirect' => [
        'title' => $this->t('Redirect'),
        'description' => $this->t('Provides the ability to create manual redirects and maintain a canonical URL for all content, redirecting all other requests to that path. It is recommended to enable the "@option" option.', ['@option' => $this->t('Automatically create redirects when URL aliases are changed.')]),
        'uri' => 'https://www.drupal.org/project/redirect',
      ],
      'telephone' => [
        'title' => $this->t('Telephone'),
        'description' => $this->t('Defines a field type for telephone numbers.'),
        'uri' => 'https://www.drupal.org/docs/8/core/modules/telephone',
      ],
    ];

    $installed_modules = $this->moduleHandler->getModuleList();
    $missing_modules = array_diff_key($recommended_modules, $installed_modules);
    if (!$missing_modules) {
      return;
    }

    $module_names = [];
    $module_items = [];
    foreach ($missing_modules as $missing_module) {
      $module_names[] = $missing_module['title'];
      $module_items[] = [
        'title' => [
          '#type' => 'link',
          '#title' => $missing_module['title'],
          '#url' => Url::fromUri($missing_module['uri']),
          '#suffix' => '</br>',
        ],
        'description' => [
          '#markup' => $missing_module['description'],
        ],
      ];
    }
    $requirements['schemadotorg_recommended_modules'] = [
      'title' => $this->t('Schema.org Blueprints: Recommended modules missing'),
      'value' => $this->t('Recommended modules missing: %module_list.', ['%module_list' => implode(', ', $module_names)]),
      'description' => [
        'content' => [
          '#markup' => $this->t('The below recommended modules help integrate and support Schema.org mappings, entities, and fields.'),
        ],
        'items' => [
          '#theme' => 'item_list',
          '#items' => $module_items,
        ],
      ],
      'severity' => REQUIREMENT_WARNING,
    ];
  }

  /**
   * Check for integration modules requirements.
   *
   * @param array &$requirements
   *   An associative array of requirements for status reporting.
   */
  protected function checkIntegrationRequirements(array &$requirements): void {
    $integration_modules = [
      'media' => [
        'title' => $this->t('Schema.org Blueprints Media'),
        'description' => $this->t('Integrates the Media and Media Library module with the Schema.org Blueprints module.'),
        'uri' => 'https://git.drupalcode.org/project/schemadotorg/-/tree/1.0.x/modules/schemadotorg_media',
      ],
      'paragraphs' => [
        'title' => $this->t('Schema.org Blueprints Paragraphs'),
        'description' => $this->t('Integrates the Paragraphs and Paragraphs Library module with the Schema.org Blueprints module.'),
        'uri' => 'https://git.drupalcode.org/project/schemadotorg/-/tree/1.0.x/modules/schemadotorg_paragraphs',
      ],
      'taxonomy' => [
        'title' => $this->t('Schema.org Blueprints Taxonomy'),
        'description' => $this->t('Assists with creating and mapping taxonomy vocabularies and terms.'),
        'uri' => 'https://git.drupalcode.org/project/schemadotorg/-/tree/1.0.x/modules/schemadotorg_taxonomy)**',
      ],
    ];
    foreach ($integration_modules as $module_name => $integration_module) {
      if (!$this->moduleHandler->moduleExists($module_name)
        || $this->moduleHandler->moduleExists('schemadotorg_' . $module_name)) {
        unset($integration_modules[$module_name]);
      }
    }
    if (!$integration_modules) {
      return;
    }

    $module_names = [];
    $module_items = [];
    foreach ($integration_modules as $integration_module) {
      $module_names[] = $integration_module['title'];
      $module_items[] = [
        'title' => [
          '#type' => 'link',
          '#title' => $integration_module['title'],
          '#url' => Url::fromUri($integration_module['uri']),
          '#suffix' => '</br>',
        ],
        'description' => [
          '#markup' => $integration_module['description'],
        ],
      ];
    }
    $requirements['schemadotorg_integration_modules'] = [
      'title' => $this->t('Schema.org Blueprints: Integration modules missing'),
      'value' => $this->t('Integration modules missing: %module_list.', ['%module_list' => implode(', ', $module_names)]),
      'description' => [
        'content' => [
          '#markup' => $this->t('The below modules are required to support Schema.org mappings and entities.'),
        ],
        'items' => [
          '#theme' => 'item_list',
          '#items' => $module_items,
        ],
      ],
      'severity' => REQUIREMENT_ERROR,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function install(): void {
    // Recreate Schema.org types and properties tables.
    // Recreating these readonly tables allows us to continually refine and
    // optimize the table schemas.
    $this->reinstallSchema();

    // Import Schema.org types and properties tables.
    $this->importTable('types');
    $this->importTable('properties');
  }

  /**
   * {@inheritdoc}
   */
  public function schema(): array {
    $schema = [];

    // Schema.org: Types.
    // @see https://raw.githubusercontent.com/schemaorg/schemaorg/main/data/releases/13.0/schemaorg-current-https-types.csv
    $schema['schemadotorg_types'] = [
      'description' => 'Schema.org types',
      'fields' => [
        'id' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
        ],
        'label' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'comment' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'sub_type_of' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'enumerationtype' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'equivalent_class' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'properties' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'sub_types' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'supersedes' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'superseded_by' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'is_part_of' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
      ],
      'primary key' => ['id'],
      'indexes' => [
        'label' => ['label'],
        'enumerationtype' => ['enumerationtype'],
      ],
    ];
    // Schema.org: Properties.
    // @see https://raw.githubusercontent.com/schemaorg/schemaorg/main/data/releases/13.0/schemaorg-current-https-properties.csv
    $schema['schemadotorg_properties'] = [
      'description' => 'Schema.org properties',
      'fields' => [
        'id' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
        ],
        'label' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'comment' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'sub_property_of' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'equivalent_property' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'subproperties' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'domain_includes' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'range_includes' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'inverse_of' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'supersedes' => [
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
        'superseded_by' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
        'is_part_of' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ],
      ],
      'primary key' => ['id'],
      'indexes' => [
        'label' => ['label'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function downloadCsvData(): void {
    $version = static::VERSION;

    $directory = __DIR__ . "/../data/$version";
    if (!file_exists($directory)) {
      mkdir($directory);
    }

    $tables = ['properties', 'types'];
    foreach ($tables as $table) {
      $source = "https://github.com/schemaorg/schemaorg/blob/main/data/releases/$version/schemaorg-all-https-$table.csv?raw=true";
      $destination = "$directory/schemaorg-current-https-$table.csv";
      copy($source, $destination);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function translateCsvData(): void {
    $tables = ['properties', 'types'];
    $strings = [];
    foreach ($tables as $table) {
      $result = $this->database
        ->select('schemadotorg_' . $table, 'table')
        ->fields('table', ['id', 'label', 'comment'])
        ->orderBy('label')
        ->execute();
      while ($record = $result->fetchAssoc()) {
        $strings[] = '// ' . $record['id'];
        $strings[] = $this->formatStringTranslation($record['label']);
        $strings[] = $this->formatStringTranslation($record['comment']);
        $first_sentence = SchemaDotOrgStringHelper::getFirstSentence($record['comment']);
        if ($record['comment'] !== $first_sentence) {
          $strings[] = $this->formatStringTranslation($first_sentence);
        }
        $strings[] = '';
      }
      $filename = __DIR__ . '/../data/' . static::VERSION . '/schemaorg.translations.' . $table . '.inc';
      $translatable_strings = implode(PHP_EOL, $strings);
      $contents = <<<EOT
        <?php

        /**
         * @file
         * Translate Schema.org $table id and comment strings.
         *
         * Passing Schema.org label and comments to Drupal's t() function to
         * ensure all translatable Schema.org strings discovered by
         * localize.drupal.org.
         *
         * DO NOT EDIT.
         * This file is generated using `drush schemadotorg:translate-schema`.
         *
         * @see https://localize.drupal.org/translate/languages/en-gb/translate?project=schemadotorg
         */

        declare(strict_types=1);

        // phpcs:disable Drupal.Semantics.FunctionT.BackslashSingleQuote

        $translatable_strings
        EOT;
      file_put_contents($filename, $contents);
    }
  }

  /**
   * Format string for translation using the t() function.
   *
   * @param string $string
   *   A translatable string.
   *
   * @return string
   *   A translatable string formatted for translation using the t() function.
   */
  protected function formatStringTranslation(string $string): string {
    if (str_contains($string, '"') && str_contains($string, "'")) {
      return "t('" . str_replace("'", "\'", $string) . "');";
    }
    elseif (str_contains($string, '"')) {
      return "t('" . $string . "');";
    }
    else {
      return 't("' . $string . '");';
    }
  }

  /**
   * Import Schema.org types and properties tables.
   */
  public function importTables(): void {
    $this->importTable('types');
    $this->importTable('properties');
  }

  /**
   * {@inheritdoc}
   */
  public function validateFileName(string $file): bool {
    return (bool) $this->getFileName('types', $file);
  }

  /**
   * Installs and populates Schema.org table.
   *
   * @param string $name
   *   The Schema.org table type (properties or types).
   */
  protected function importTable(string $name): void {
    $table = 'schemadotorg_' . $name;
    $filename = $this->getFileName($name);
    if (!$filename) {
      return;
    }

    // Truncate table.
    $this->database->truncate($table)->execute();

    // Load CSV.
    $handle = fopen($filename, 'r');

    // Get field names.
    $fields = fgetcsv($handle);
    array_walk(
      $fields,
      fn(&$field_name) => ($field_name = $this->schemaNames->camelCaseToSnakeCase($field_name))
    );

    // Insert multiple records.
    $query = $this->database->insert($table)->fields($fields);
    while ($row = fgetcsv($handle)) {
      $values = [];
      foreach ($fields as $index => $field_name) {
        $values[$field_name] = $row[$index] ?? '';
      }
      $query->values($values);
    }
    $query->execute();
  }

  /**
   * Reinstall Schema.org tables.
   */
  protected function reinstallSchema(): void {
    $tables = $this->schema();
    foreach ($tables as $name => $table) {
      if ($this->database->schema()->tableExists($name)) {
        $this->database->schema()->dropTable($name);
      }
      $this->database->schema()->createTable($name, $table);
    }
  }

  /**
   * Get the Schema.org data file name/URL.
   *
   * @param string $name
   *   The table name.
   * @param string|null $file
   *   The Schema.org data file name/URL.
   *
   * @return string|null
   *   The Schema.org data file name/URL.
   */
  protected function getFileName(string $name, ?string $file = NULL): ?string {
    $file = $file ?: $this->configFactory->get('schemadotorg.settings')
      ->get('schema_data.file');

    $file = str_replace('[VERSION]', static::VERSION, $file);
    $file = str_replace('[TABLE]', $name, $file);

    // Check file with absolute URL.
    // @see https://www.geeksforgeeks.org/how-to-check-the-existence-of-url-in-php/
    if (str_starts_with($file, 'http')) {
      $headers = @get_headers($file);
      if ($headers && !str_contains($headers[0], '404')) {
        return $file;
      }
    }

    $files = [
      __DIR__ . '/..' . $file,
      __DIR__ . '/../' . $file,
      DRUPAL_ROOT . '/' . $file,
      DRUPAL_ROOT . $file,
      $file,
    ];
    foreach ($files as $file) {
      if (file_exists($file)) {
        return $file;
      }
    }
    return NULL;
  }

}
