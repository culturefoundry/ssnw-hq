<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Database\Connection;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\schemadotorg\Utility\SchemaDotOrgStringHelper;

/**
 * Schema.org schema type manager.
 *
 * The Schema.org schema type manager provides an API for understanding and
 * access Schema.org types, properties, and relationships in Drupal.
 *
 * This service queries and collects data from 'schemadotorg_types' and
 * 'schemadotorg_properties' database tables.
 */
class SchemaDotOrgSchemaTypeManager implements SchemaDotOrgSchemaTypeManagerInterface {
  use StringTranslationTrait;

  /**
   * Pattern used to match settings.
   *
   * @see \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager::getSetting
   */
  protected array $settingPatterns = [
    ['entity_type_id', 'bundle', 'field_name'],
    ['entity_type_id', 'bundle'],
    ['entity_type_id', 'field_name'],

    ['entity_type_id', 'schema_type', 'schema_property'],
    ['entity_type_id', 'schema_type', 'field_name'],
    ['entity_type_id', 'bundle', 'schema_type', 'schema_property'],
    ['entity_type_id', 'bundle', 'schema_type'],
    ['entity_type_id', 'bundle', 'schema_property'],
    ['entity_type_id', 'schema_property'],
    ['entity_type_id', 'schema_type'],

    ['bundle', 'field_name'],
    ['bundle', 'schema_type'],
    ['bundle', 'schema_property'],

    ['schema_type', 'field_name'],
    ['schema_type', 'schema_property'],

    ['schema_property'],
    ['schema_type'],
    ['field_name'],
    ['bundle'],
    ['entity_type_id'],
  ];

  /**
   * Cache of Schema.org tree data.
   *
   * @see \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager::getAllSubTypes
   */
  protected array $tree;

  /**
   * Schema.org items cache.
   */
  protected array $itemsCache = [];

  /**
   * Schema.org superseded cache.
   */
  protected array $supersededCache;

  /**
   * Constructs a SchemaDotOrgSchemaTypeManager object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   */
  public function __construct(
    protected Connection $database,
    protected SchemaDotOrgNamesInterface $schemaNames,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getUri(string $id): string {
    return static::URI . $id;
  }

  /**
   * {@inheritdoc}
   */
  public function isId(string $table, string $id): bool {
    $label = $this->database->select('schemadotorg_' . $table, 't')
      ->fields('t', ['label'])
      ->condition('label', $id)
      ->execute()
      ->fetchField();
    // Making sure the 'label' (aka type or property id) is exact match because
    // SQL queries are case-insensitive.
    return ($label === $id);
  }

  /**
   * {@inheritdoc}
   */
  public function isItem(string $id): bool {
    return $this->isType($id) || $this->isProperty($id);
  }

  /**
   * {@inheritdoc}
   */
  public function isType(string $id): bool {
    return $this->isId(static::SCHEMA_TYPES, $id);
  }

  /**
   * {@inheritdoc}
   */
  public function isSubTypeOf(string $type, string|array $subtype_of): bool {
    $subtype_of = (array) $subtype_of;
    $breadcrumbs = $this->getTypeBreadcrumbs($type);
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb = array_reverse($breadcrumb);
      foreach ($breadcrumb as $breadcrumb_type) {
        if (in_array($breadcrumb_type, $subtype_of)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isThing(string $id): bool {
    $type_definition = $this->getType($id);
    return (!empty($type_definition)
      && $type_definition['label'] === $id
      && !empty($type_definition['properties'])
      && !in_array($id, ['Enumeration', 'Intangible'])
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isDataType(string $id): bool {
    $data_types = $this->getDataTypes();
    return (isset($data_types[$id]));
  }

  /**
   * {@inheritdoc}
   */
  public function isIntangible(string $id): bool {
    if (!$this->isType($id)) {
      return FALSE;
    }
    elseif ($id === 'Intangible') {
      return TRUE;
    }
    else {
      $breadcrumbs = $this->getTypeBreadcrumbs($id);
      foreach ($breadcrumbs as $breadcrumb => $items) {
        if (str_contains($breadcrumb, '/Intangible/')) {
          return TRUE;
        }
      }
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isEnumerationType(string $id): bool {
    return (boolean) $this->database->select('schemadotorg_types', 'types')
      ->fields('types', ['id'])
      ->condition('enumerationtype', $this->getUri($id))
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function isEnumerationValue(string $id): bool {
    $item = $this->getItem(static::SCHEMA_TYPES, $id);
    return (!empty($item['enumerationtype']));
  }

  /**
   * {@inheritdoc}
   */
  public function isProperty(string $id): bool {
    return $this->isId(static::SCHEMA_PROPERTIES, $id);
  }

  /**
   * {@inheritdoc}
   */
  public function isSubPropertyOf(string $property, string $subproperty_of): bool {
    $property_definition = $this->getProperty($property);
    return ($property_definition && $property_definition['sub_property_of'] === 'https://schema.org/' . $subproperty_of);
  }

  /**
   * {@inheritdoc}
   */
  public function isSuperseded(string $id): bool {
    if (!isset($this->supersededCache)) {
      $this->supersededCache = [];
      foreach ([static::SCHEMA_TYPES, static::SCHEMA_PROPERTIES] as $table) {
        $ids = $this->database->select('schemadotorg_' . $table, $table)
          ->fields($table, ['label'])
          ->condition('superseded_by', '', '<>')
          ->execute()
          ->fetchCol();
        $this->supersededCache += array_combine($ids, $ids);
      }
    }
    return !empty($this->supersededCache[$id]);
  }

  /**
   * {@inheritdoc}
   */
  public function isPropertyMainEntity(string $id): bool {
    return (in_array($id, ['itemListElement', 'hasPart', 'mainEntity', 'mainEntityOfPage']));
  }

  /**
   * {@inheritdoc}
   */
  public function parseIds(string $text): array {
    $text = trim($text);
    if (empty($text)) {
      return [];
    }

    $items = explode(', ', str_replace(static::URI, '', $text));
    return array_combine($items, $items);
  }

  /**
   * {@inheritdoc}
   */
  public function getItem(string $table, string $id, array $fields = []): array|FALSE {
    $table_name = 'schemadotorg_' . $table;
    if (empty($fields)) {
      if (!isset($this->itemsCache[$table][$id])) {
        $item = $this->database->query('SELECT *
          FROM {' . $this->database->escapeTable($table_name) . '}
          WHERE label=:id', [':id' => $id])->fetchAssoc();
        $this->itemsCache[$table][$id] = $this->setItemDrupalFields($table, $item);
      }
      return $this->itemsCache[$table][$id];
    }
    else {
      $item = $this->database->select($table_name, 't')
        ->fields('t', $fields)
        ->condition('label', (array) $id)
        ->execute()
        ->fetchAssoc();
      return $this->setItemDrupalFields($table, $item);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getType(string $type, array $fields = []): array|FALSE {
    return $this->getItem(static::SCHEMA_TYPES, $type, $fields);
  }

  /**
   * {@inheritdoc}
   */
  public function getProperty(string $property, array $fields = []): array|FALSE {
    return $this->getItem(static::SCHEMA_PROPERTIES, $property, $fields);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyRangeIncludes(string $property): array {
    $property_definition = $this->getProperty($property);
    $range_includes = $property_definition['range_includes'] ?? '';
    return $this->parseIds($range_includes);
  }

  /**
   * Get a Schema.org property's default Schema.org type from range_includes.
   *
   * @param string $property
   *   The Schema.org property.
   *
   * @return string|null
   *   The Schema.org property's default Schema.org type from range_includes.
   */
  public function getPropertyDefaultType(string $property): ?string {
    $property_definition = $this->getProperty($property);
    if (!$property_definition) {
      return NULL;
    }

    $range_includes = $this->parseIds($property_definition['range_includes']);
    $type_definitions = $this->getTypes($range_includes);

    $sub_types_of_thing = [];
    foreach ($type_definitions as $type => $type_definition) {
      // Remove all types definitions without properties
      // (i.e. Enumerations and Data types).
      if (empty($type_definition['properties'])) {
        // If the property range can be a simple data type, do not return a
        // default type.
        if ($this->isDataType($type)) {
          return NULL;
        }
        // Otherwise. unset the enumeration.
        else {
          unset($type_definitions[$type]);
        }
      }
      // Track subtypes of Thing.
      elseif ($type_definition['sub_type_of'] === 'https://schema.org/Thing') {
        $sub_types_of_thing[$type] = $type;
      }
    }

    // Make sure subtypes of Thing comes first.
    $type_definitions = $sub_types_of_thing + $type_definitions;

    // Finally, return the first type in range_includes type definitions.
    return array_key_first($type_definitions);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyUnit(string $property, int|string|null $value = 0): string|TranslatableMarkup|NULL {
    if ($value === NULL) {
      return NULL;
    }

    $property_definition = $this->getItem(static::SCHEMA_PROPERTIES, $property);
    if (!$property_definition) {
      return NULL;
    }

    $range_includes = ['https://schema.org/Energy', 'https://schema.org/Mass'];
    if (!in_array($property_definition['range_includes'], $range_includes)) {
      return NULL;
    }

    preg_match('/\b(grams|milligrams|calories)\b/', $property_definition['comment'], $match);
    $unit = $match[1] ?? NULL;

    return match ($unit) {
      'grams' => ($value == '1') ? $this->t('gram') : $this->t('grams'),
      'milligrams' => ($value == '1') ? $this->t('milligram') : $this->t('milligrams'),
      'calories' => ($value == '1') ? $this->t('calorie') : $this->t('calories'),
      default => NULL,
    };
  }

  /**
   * {@inheritdoc}
   */
  public function getItems(string $table, array $ids, array $fields = []): array {
    if (empty($ids)) {
      return [];
    }

    $table_name = 'schemadotorg_' . $table;
    if (empty($fields)) {
      $result = $this->database->query('SELECT *
        FROM {' . $this->database->escapeTable($table_name) . '}
        WHERE label IN (:ids[])', [':ids[]' => $ids]);
    }
    else {
      $result = $this->database->select($table_name, 't')
        ->fields('t', $fields)
        ->condition('label', $ids, 'IN')
        ->execute();
    }

    $items = [];
    while ($record = $result->fetchAssoc()) {
      $items[$record['label']] = $record;
    }
    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypes(array $types, array $fields = []): array {
    return $this->getItems(static::SCHEMA_TYPES, $types, $fields);
  }

  /**
   * {@inheritdoc}
   */
  public function getProperties(array $properties, array $fields = []): array {
    return $this->getItems(static::SCHEMA_PROPERTIES, $properties, $fields);
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeProperties(string $type, array $fields = []): array {
    $type_definition = $this->getType($type);
    if (empty($type_definition['properties'])) {
      return [];
    }

    $properties = $this->parseIds($type_definition['properties']);
    $items = $this->database->select('schemadotorg_properties', 'properties')
      ->fields('properties', $fields)
      ->condition('label', $properties, 'IN')
      ->orderBy('label')
      ->execute()
      ->fetchAllAssoc('label', \PDO::FETCH_ASSOC);
    foreach ($items as $index => $item) {
      $items[$index] = $this->setItemDrupalFields('properties', $item);
    }
    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeChildren(string $type): array {
    $type_definition = $this->getType($type, ['sub_types']);

    $children = [];

    // Subtypes.
    if (!empty($type_definition['sub_types'])) {
      $children = $this->parseIds($type_definition['sub_types']);
    }

    // Enumerations.
    $enumeration_types = $this->database->select('schemadotorg_types', 'types')
      ->fields('types', ['label'])
      ->condition('enumerationtype', $this->getUri($type))
      ->orderBy('label')
      ->execute()
      ->fetchCol();
    if ($enumeration_types) {
      $children += array_combine($enumeration_types, $enumeration_types);
    }

    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeChildrenAsOptions(string $type): array {
    $options = $this->getTypeChildren($type);
    array_walk($options, function (&$value): void {
      $value = $this->schemaNames->camelCaseToTitleCase($value);
    });
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllTypeChildrenAsOptions(string $type, array $ignored_types = []): array {
    return $this->getAllTypeChildrenAsOptionsRecursive($type, $ignored_types);
  }

  /**
   * Gets all child Schema.org types below a specified type recursively.
   *
   * @param string $type
   *   The Schema.org type.
   * @param array $ignored_types
   *   An array of ignored Schema.org type ids.
   * @param string $indent
   *   The indentation.
   *
   * @return array
   *   An associative array of Schema.org types as options
   */
  protected function getAllTypeChildrenAsOptionsRecursive(string $type, array $ignored_types = [], string $indent = ''): array {
    $options = [];
    $types = $this->getTypeChildren($type);
    foreach ($types as $subtype) {
      if ($this->isSuperseded($subtype) || in_array($subtype, $ignored_types)) {
        continue;
      }
      $title = $this->schemaNames->camelCaseToTitleCase($subtype);
      $options[$subtype] = ($indent ? $indent . ' ' : '') . $title;
      $options += $this->getAllTypeChildrenAsOptionsRecursive($subtype, $ignored_types, $indent . '-');
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubtypes(string $type): array {
    $type_definition = $this->getType($type, ['sub_types']);
    return $this->parseIds($type_definition['sub_types']);
  }

  /**
   * {@inheritdoc}
   */
  public function getEnumerations(string $type): array {
    $enumeration_types = $this->database->select('schemadotorg_types', 'types')
      ->fields('types', ['label'])
      ->condition('enumerationtype', $this->getUri($type))
      ->orderBy('label')
      ->execute()
      ->fetchCol();
    return ($enumeration_types) ? array_combine($enumeration_types, $enumeration_types) : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDataTypes(): array {
    // Data types are hard coded because they never change.
    return [
      'Boolean' => 'Boolean',
      'Date' => 'Date',
      'DateTime' => 'DateTime',
      'False' => 'False',
      'Number' => 'Number',
      'Text' => 'Text',
      'Time' => 'Time',
      'True' => 'True',
      'Float' => 'Float',
      'Integer' => 'Integer',
      'CssSelectorType' => 'CssSelectorType',
      'PronounceableText' => 'PronounceableText',
      'URL' => 'URL',
      'XPathType' => 'XPathType',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeTree(string|array $type, array $ignored_types = []): array {
    if ($ignored_types) {
      $ignored_types = array_combine($ignored_types, $ignored_types);
    }
    return $this->getTypeTreeRecursive((array) $type, $ignored_types);
  }

  /**
   * Build Schema.org type hierarchical tree recursively.
   *
   * @param array $types
   *   An array of Schema.org type.
   * @param array $ignored_types
   *   An array of ignored Schema.org types.
   *
   * @return array
   *   A renderable array containing Schema.org type hierarchical tree.
   */
  protected function getTypeTreeRecursive(array $types, array $ignored_types = []): array {
    if (empty($types)) {
      return [];
    }

    // We must make sure the types are not deprecated or does not exist.
    // @see https://schema.org/docs/attic.home.html
    $types = $this->database->select('schemadotorg_types', 'types')
      ->fields('types', ['label'])
      ->condition('label', $types, 'IN')
      ->orderBy('label')
      ->execute()
      ->fetchCol();
    $types = array_combine($types, $types);

    // Remove ignored types.
    if ($types) {
      $types = array_diff_key($types, $ignored_types);
    }

    $tree = [];
    foreach ($types as $type) {
      $subtypes = $this->getSubtypes($type);
      $enumerations = $this->getEnumerations($type);
      $tree[$type] = [
        'subtypes' => $this->getTypeTreeRecursive($subtypes, $ignored_types),
        'enumerations' => $this->getTypeTreeRecursive($enumerations, $ignored_types),
      ];
    }
    return $tree;
  }

  /**
   * {@inheritdoc}
   */
  public function getParentTypes(string $type): array {
    $breadcrumbs = $this->getTypeBreadcrumbs($type);

    // Build parents types with a sortable key that ensure that the
    // parent types are sorted from top to bottom.
    // (i.e. Thing comes before Place or Organization)
    $parent_types = [];
    foreach (array_values($breadcrumbs) as $breadcrumb_index => $breadcrumb) {
      foreach (array_values($breadcrumb) as $type_index => $type) {
        $index = str_pad((string) $type_index, 3, '0', STR_PAD_LEFT)
          . '-'
          . str_pad((string) $breadcrumb_index, 3, '0', STR_PAD_LEFT);
        $parent_types[$index] = $type;
      }
    }
    ksort($parent_types);
    return array_combine($parent_types, $parent_types);
  }

  /**
   * {@inheritdoc}
   */
  public function getAllSubTypes(array $types): array {
    if (!isset($this->tree)) {
      $this->tree = [];
      $result = $this->database->select('schemadotorg_types', 'types')
        ->fields('types', ['label', 'sub_types'])
        ->orderBy('label')
        ->execute();
      while ($record = $result->fetchAssoc()) {
        $this->tree[$record['label']] = $this->parseIds($record['sub_types']);
      }
    }

    $all_subtypes = [];

    $types = array_combine($types, $types);
    while ($types) {
      $all_subtypes += $types;
      $subtypes = [];
      foreach ($types as $type) {
        if (isset($this->tree[$type])) {
          $subtypes += array_combine($this->tree[$type], $this->tree[$type]);
        }
      }
      $types = $subtypes;
    }
    return $all_subtypes;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllTypeChildren(string $type, array $fields = [], array $ignored_types = []): array {
    if ($ignored_types) {
      $ignored_types = array_combine($ignored_types, $ignored_types);
    }
    return $this->getTypesChildrenRecursive([$type], $fields, $ignored_types);
  }

  /**
   * Gets all Schema.org types below a specified array of types.
   *
   * @param array $types
   *   An array of Schema.org type ids.
   * @param array $fields
   *   An array of Schema.org type fields.
   * @param array $ignored_types
   *   An array of ignored Schema.org type ids.
   *
   * @return array
   *   An associative array of Schema.org types keyed by type.
   *
   * @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportControllerBase::buildItemsRecursive
   */
  protected function getTypesChildrenRecursive(array $types, array $fields = [], array $ignored_types = []): array {
    $fields = $fields ?: ['label', 'sub_types', 'sub_type_of'];

    $items = $this->database->select('schemadotorg_types', 'types')
      ->fields('types', $fields)
      ->condition('label', $types, 'IN')
      ->orderBy('label')
      ->execute()
      ->fetchAllAssoc('label', \PDO::FETCH_ASSOC);
    foreach ($items as $id => $item) {
      // Get children.
      $children = $this->getTypeChildren($id);

      // Remove ignored types from children.
      if ($ignored_types) {
        $children = array_diff_key($children, $ignored_types);
      }

      if ($children) {
        $items += $this->getTypesChildrenRecursive($children, $fields, $ignored_types);
      }
    }
    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeBreadcrumbs(string $type): array {
    $breadcrumbs = [];
    $breadcrumb_id = $type;
    $breadcrumbs[$breadcrumb_id] = [];
    $this->getTypeBreadcrumbsRecursive($breadcrumbs, $breadcrumb_id, $type);

    $sorted_breadcrumbs = [];
    foreach ($breadcrumbs as $breadcrumb) {
      $sorted_breadcrumb = array_reverse($breadcrumb, TRUE);
      $breadcrumb_path = implode('/', array_keys($sorted_breadcrumb));
      $sorted_breadcrumbs[$breadcrumb_path] = $sorted_breadcrumb;
    }
    ksort($sorted_breadcrumbs);
    return $sorted_breadcrumbs;
  }

  /**
   * {@inheritdoc}
   */
  public function hasProperty(string $type, string $property): bool {
    $type_definition = $this->getType($type);
    return ($type_definition && str_contains($type_definition['properties'], '/' . $property));
  }

  /**
   * {@inheritdoc}
   */
  public function hasSubtypes(string $type): bool {
    $type_definition = $this->getType($type);
    return (boolean) $type_definition['sub_types'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSetting(array $settings, SchemaDotOrgMappingInterface|array $parts, bool $multiple = FALSE): mixed {
    // Get the parts from a Schema.org mapping.
    if ($parts instanceof SchemaDotOrgMappingInterface) {
      $parts = [
        'entity_type_id' => $parts->getTargetEntityTypeId(),
        'bundle' => $parts->getTargetBundle(),
        'schema_type' => $parts->getSchemaType(),
      ];
    }

    // Ignore empty parts.
    $parts = array_filter($parts);

    // Set patterns.
    // @todo Determine if patterns should be customizable.
    $patterns = $this->settingPatterns;

    // Handle settings that are a simple indexed array.
    if (array_is_list($settings)) {
      $settings = array_flip($settings);
      $settings = array_fill_keys(array_keys($settings), TRUE);
    }

    // Filter the patterns to only applicable patterns by part name.
    $part_names = array_flip($parts);
    foreach ($patterns as $index => $pattern) {
      // Remove any pattern that does not include all the part names.
      if (array_diff($pattern, $part_names)) {
        unset($patterns[$index]);
      }
    }

    // Get all the possible searches.
    if (isset($parts['schema_type'])) {
      // For Schema.org type, include the parent types in the searches.
      $parent_types = $this->getParentTypes($parts['schema_type']);
      $parent_types = array_reverse($parent_types);
      $searches = [];
      foreach ($parent_types as $parent_type) {
        $searches[] = ['schema_type' => $parent_type] + $parts;
      }
    }
    else {
      $searches = [$parts];
    }

    // Loop through all the possible searches.
    $multiple_settings = [];
    foreach ($searches as $search) {
      foreach ($patterns as $pattern) {
        // Populate the search patterns.
        // There might be a cleaner or faster way to do this.
        $search_pattern = $pattern;
        foreach ($search_pattern as $index => $pattern_part) {
          $search_pattern[$index] = $search[$pattern_part];
        }

        // Check if the settings name/key exists and return it.
        $settings_name = implode('--', $search_pattern);
        if (array_key_exists($settings_name, $settings)) {
          if (!$multiple) {
            return $settings[$settings_name];
          }
          $multiple_settings[$settings_name] = $settings[$settings_name];
        }
      }
    }

    return $multiple_settings ?: NULL;
  }

  /**
   * Build type breadcrumbs recursively.
   *
   * @param array &$breadcrumbs
   *   The type breadcrumbs.
   * @param string $breadcrumb_id
   *   The breadcrumb id which is a Schema.org type.
   * @param string $type
   *   The Schema.org type.
   */
  protected function getTypeBreadcrumbsRecursive(array &$breadcrumbs, string $breadcrumb_id, string $type): void {
    $breadcrumbs[$breadcrumb_id][$type] = $type;

    $item = $this->getItem(static::SCHEMA_TYPES, $type, ['sub_type_of']);
    if (!$item) {
      return;
    }

    $parent_types = $this->parseIds($item['sub_type_of']);
    if (empty($parent_types)) {
      return;
    }

    // Store a reference to the current breadcrumb.
    $current_breadcrumb = $breadcrumbs[$breadcrumb_id];

    // The first parent type is appended to the current breadcrumb.
    $parent_type = array_shift($parent_types);
    $this->getTypeBreadcrumbsRecursive($breadcrumbs, $breadcrumb_id, $parent_type);

    // All additional parent types needs to start a new breadcrumb.
    foreach ($parent_types as $parent_type) {
      $breadcrumbs[$parent_type] = $current_breadcrumb;
      $this->getTypeBreadcrumbsRecursive($breadcrumbs, $parent_type, $parent_type);
    }
  }

  /**
   * Set Schema.org item Drupal fields including name and label.
   *
   * @param string $table
   *   The Schema.org table.
   * @param array|false|null $item
   *   An associative array containing Schema.org type or property item.
   *
   * @return array|false|null
   *   The Schema.org type or property item with a 'drupal_name',
   *   'drupal_label', and 'drupal_description' if the Schema.org label or
   *    description (a.k.a comment) is included with the item.
   */
  protected function setItemDrupalFields(string $table, array|FALSE|NULL $item): array|FALSE|NULL {
    if (empty($item) || !isset($item['label'])) {
      return $item;
    }

    $item['drupal_name'] = $this->schemaNames->schemaIdToDrupalName($table, $item['label']);
    $item['drupal_label'] = $this->schemaNames->schemaIdToDrupalLabel($table, $item['label']);
    $item['drupal_description'] = SchemaDotOrgStringHelper::getFirstSentence($item['comment'] ?? '');
    return $item;
  }

}
