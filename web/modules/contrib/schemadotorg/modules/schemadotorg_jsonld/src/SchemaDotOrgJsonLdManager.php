<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld;

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\image\ImageStyleInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Symfony\Component\Routing\RouterInterface;

/**
 * Schema.org JSON-LD manager.
 */
class SchemaDotOrgJsonLdManager implements SchemaDotOrgJsonLdManagerInterface {
  use StringTranslationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgJsonLdManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Symfony\Component\Routing\RouterInterface $router
   *   The router.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $fieldTypePluginManager
   *   The field type plugin manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $fileUrlGenerator
   *   The file URL generator.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected RendererInterface $renderer,
    protected RouterInterface $router,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected FieldTypePluginManagerInterface $fieldTypePluginManager,
    protected DateFormatterInterface $dateFormatter,
    protected FileUrlGeneratorInterface $fileUrlGenerator,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getEntityRouteMatch(EntityInterface $entity, string $rel = 'canonical'): RouteMatchInterface|NULL {
    if (!$entity->hasLinkTemplate($rel)) {
      return NULL;
    }

    $url = $entity->toUrl($rel);
    $route_name = $url->getRouteName();
    $route = $this->router->getRouteCollection()->get($route_name);
    if (empty($route)) {
      return NULL;
    }

    $entity_type_id = $entity->getEntityTypeId();
    return new RouteMatch(
      $route_name,
      $route,
      [$entity_type_id => $entity],
      [$entity_type_id => $entity->id()]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteMatchEntity(?RouteMatchInterface $route_match = NULL): EntityInterface|NULL {
    $route_match = $route_match ?: $this->routeMatch;
    $route_name = $route_match->getRouteName();
    if (preg_match('/entity\.(.*)\.(latest[_-]version|canonical|schemadotorg_data|schemadotorg_jsonld)/', $route_name, $matches)) {
      return $route_match->getParameter($matches[1]);
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function sortProperties(array $properties): array {
    $definition_properties = [];
    $sorted_properties = [];

    // Collect the definition properties.
    foreach ($properties as $property_name => $property_value) {
      if ($property_name[0] === '@') {
        $definition_properties[$property_name] = $property_value;
        unset($properties[$property_name]);
      }
    }

    // Collect the sorted properties.
    $schema_property_order = $this->getConfig()->get('schema_property_order');
    foreach ($schema_property_order as $property_name) {
      if (isset($properties[$property_name])) {
        $sorted_properties[$property_name] = $properties[$property_name];
        unset($properties[$property_name]);
      }
    }

    // Sort the remaining properties alphabetically.
    ksort($properties);

    return $definition_properties + $sorted_properties + $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaTypeProperties(FieldItemListInterface $items): array {
    $field_storage = $items->getFieldDefinition()->getFieldStorageDefinition();
    $field_type = $field_storage->getType();
    switch ($field_type) {
      case 'text_with_summary';
        $mapping = $this->getMappingStorage()->loadByEntity($items->getEntity());
        $field_name = $field_storage->getName();
        $cardinality = $field_storage->getCardinality();
        $schema_property = $mapping->getSchemaPropertyMapping($field_name);
        // For text and articleBody properties set the description
        // to the summary.
        if (in_array($schema_property, ['text', 'articleBody'])
          && $cardinality === 1
          && $items->summary
          && $items->format) {
          $summary = (string) check_markup($items->summary, $items->format);
          return $summary ? ['description' => $summary] : [];
        }
        else {
          return [];
        }
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyValue(FieldItemInterface $item): mixed {
    $field_storage = $item->getFieldDefinition()->getFieldStorageDefinition();
    $field_type = $field_storage->getType();

    // Get value from Drupal core field types.
    switch ($field_type) {
      case 'language':
        return ($item->value !== LanguageInterface::LANGCODE_NOT_SPECIFIED) ? $item->value : NULL;

      case 'link':
        /** @var \Drupal\link\LinkItemInterface $item */
        return ($item->uri) ? $item->getUrl()->setAbsolute()->toString() : NULL;

      case 'text_long':
      case 'text_with_summary':
        return $item->value
          ? (string) check_markup($item->value, $item->format)
          : '';

      case 'image':
      case 'file':
        return $this->getImageDerivativeUrl($item) ?: $this->getFileUrl($item);

      case 'daterange':
        $mapping = $this->getMappingStorage()->loadByEntity($item->getEntity());
        $field_name = $item->getFieldDefinition()->getName();
        $schema_property = $mapping->getSchemaPropertyMapping($field_name);
        if ($schema_property === 'eventSchedule') {
          return [
            '@type' => 'Schedule',
            'startDate' => $item->value,
            'endDate' => $item->end_value,
          ];
        }
        else {
          return $item->value;
        }

      case 'boolean':
        return (bool) $item->value;

      case 'decimal':
      case 'float':
      case 'integer':
        // @todo Determine if other field types should fully render each item.
        $field_type_info = $this->fieldTypePluginManager->getDefinition($field_type);
        $display_options = ['type' => $field_type_info['default_formatter']];
        $build = $item->view($display_options);
        return (string) DeprecationHelper::backwardsCompatibleCall(
          currentVersion: \Drupal::VERSION,
          deprecatedVersion: '10.3',
          currentCallable: fn() => $this->renderer->renderInIsolation($build),
          deprecatedCallable: fn() => $this->renderer->renderPlain($build),
        );
    }

    // Main property data type.
    $main_property_name = $this->getMainPropertyName($item);
    $value = $item->$main_property_name ?? NULL;
    if (!is_array($value)) {
      $main_property_data_type = $this->getMainPropertyDateType($item);
      switch ($main_property_data_type) {
        case 'timestamp':
          return ($value)
            ? $this->dateFormatter->format($value, 'custom', 'Y-m-d H:i:s P')
            : $value;
      }
    }

    // Return the label for unmapped entity references.
    if (isset($item->entity) && $item->entity instanceof EntityInterface) {
      return $item->entity->label();
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaPropertyValueDefaultSchemaType(string $schema_type, string $schema_property, mixed $value): array|string|int|bool|NULL {
    // If the value is an array return it with the  @type  default values.
    if (is_array($value)) {
      $range_include = $value['@type'] ?? NULL;
      return $value + $this->getSchemaTypeDefaultValues($schema_type, $schema_property, $range_include);
    }

    $schema_properties_range_includes = $this->configFactory
      ->get('schemadotorg.settings')
      ->get("schema_properties.range_includes");
    $parts = [
      'schema_type' => $schema_type,
      'schema_property' => $schema_property,
    ];
    $range_includes = $this->schemaTypeManager->getSetting($schema_properties_range_includes, $parts);
    $schema_property_schema_type = ($range_includes)
      ? reset($range_includes)
      : $this->schemaTypeManager->getPropertyDefaultType($schema_property);
    if (!$schema_property_schema_type) {
      return $value;
    }

    $main_property = $this->getSchemaTypeMainProperty($schema_property_schema_type);
    if (!$main_property) {
      return $value;
    }

    return [
      '@type' => $schema_property_schema_type,
      $main_property => $value,
    ] + $this->getSchemaTypeDefaultValues($schema_type, $schema_property, $schema_property_schema_type);
  }

  /**
   * Gets the default values for a range includes Schema.org type.
   *
   * @param string $schema_type
   *   The schema type.
   * @param string $schema_property
   *   The schema property.
   * @param string|null $range_include
   *   The range include value.
   *
   * @return array
   *   The default values for a range includes Schema.org type.
   */
  protected function getSchemaTypeDefaultValues(string $schema_type, string $schema_property, ?string $range_include): array {
    if (!$range_include) {
      return [];
    }

    $schema_type_default_values = $this->configFactory
      ->get('schemadotorg_jsonld.settings')
      ->get('schema_type_default_values');

    $parts = [
      'schema_type' => $schema_type,
      'schema_property' => $schema_property,
      'range_include' => $range_include,
    ];

    $patterns = [
      ['schema_type', 'schema_property', 'range_include'],
      ['schema_property', 'range_include'],
      ['range_include'],
    ];

    return $this->schemaTypeManager->getSetting(
      settings: $schema_type_default_values,
      parts: $parts,
      patterns: $patterns
    ) ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function hasSchemaUrl(SchemaDotOrgMappingInterface $mapping): bool {
    return !$this->schemaTypeManager->getSetting(
      $this->configFactory->get('schemadotorg_jsonld.settings')->get('exclude_url'),
      $mapping
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getSchemaTypeEntityReferenceDisplay(EntityInterface $source_entity, string $schema_property, EntityInterface $target_entity): string {
    $settings = $this->configFactory
      ->get('schemadotorg_jsonld.settings')
      ->get('schema_type_entity_references_display');

    $source_mapping = $this->getMappingStorage()->loadByEntity($source_entity);
    $source_parts = [
      'entity_type_id' => $source_mapping->getTargetEntityTypeId(),
      'bundle' => $source_mapping->getTargetBundle(),
      'schema_type' => $source_mapping->getSchemaType(),
      'schema_property' => $schema_property,
      'field_name' => $source_mapping->getSchemaPropertyFieldName($schema_property),
    ];
    $source_pattern = [
      ['entity_type_id', 'bundle', 'schema_property'],
      ['entity_type_id', 'schema_type', 'schema_property'],
      ['bundle', 'schema_property'],
      ['schema_type', 'schema_property'],
      ['entity_type_id', 'bundle', 'field_name'],
      ['entity_type_id', 'schema_type', 'field_name'],
      ['bundle', 'field_name'],
      ['schema_type', 'field_name'],
    ];
    $setting = $this->schemaTypeManager->getSetting($settings, $source_parts, [], $source_pattern);
    if ($setting) {
      return $setting;
    }

    $target_mapping = $this->getMappingStorage()->loadByEntity($target_entity);
    if ($target_mapping) {
      $target_parts = [
        'entity_type_id' => $target_mapping->getTargetEntityTypeId(),
        'bundle' => $target_mapping->getTargetBundle(),
        'schema_type' => $target_mapping->getSchemaType(),
      ];
    }
    else {
      $target_parts = [
        'entity_type_id' => $target_entity->getEntityTypeId(),
        'bundle' => $target_entity->bundle(),
      ];
    }
    $target_pattern = [
      ['entity_type_id', 'bundle'],
      ['entity_type_id', 'schema_type'],
      ['bundle'],
      ['schema_type'],
    ];
    $setting = $this->schemaTypeManager->getSetting($settings, $target_parts, [], $target_pattern);
    if ($setting) {
      return $setting;
    }

    return static::ENTITY_REFERENCE_DISPLAY_LABEL;
  }

  /**
   * Get Schema.org type's main property.
   *
   * @param string $schema_type
   *   The Schema.org type.
   *
   * @return string|null
   *   A Schema.org type's main property. (Defaults to 'name')
   */
  protected function getSchemaTypeMainProperty(string $schema_type): ?string {
    $main_properties = $this->configFactory
      ->get('schemadotorg_jsonld.settings')
      ->get('schema_type_main_properties');

    $breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs($schema_type);
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb = array_reverse($breadcrumb);
      foreach ($breadcrumb as $breadcrumb_type) {
        // Using array key exists to account main property being set to NULL,
        // which means the Schema.org type does NOT have a main property.
        if (array_key_exists($breadcrumb_type, $main_properties)) {
          return $main_properties[$breadcrumb_type];
        }
      }
    }

    return 'name';
  }

  /**
   * Gets Schema.org JSON-LD configuration settings.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Schema.org JSON-LD configuration settings.
   */
  protected function getConfig(): ImmutableConfig {
    return $this->configFactory->get('schemadotorg_jsonld.settings');
  }

  /**
   * Gets the property names for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string[]
   *   The property names for a field item.
   */
  protected function getPropertyNames(FieldItemInterface $item): array {
    return $item->getFieldDefinition()->getFieldStorageDefinition()->getPropertyNames();
  }

  /**
   * Gets the main property name for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string|null
   *   The main property name for a field item.
   */
  protected function getMainPropertyName(FieldItemInterface $item): ?string {
    return $item->getFieldDefinition()->getFieldStorageDefinition()->getMainPropertyName();
  }

  /**
   * Gets the main property date type for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string
   *   The main property date type for a field item.
   */
  protected function getMainPropertyDateType(FieldItemInterface $item): ?string {
    $field_storage_definition = $item->getFieldDefinition()->getFieldStorageDefinition();
    $main_property_name = $field_storage_definition->getMainPropertyName();
    $main_property_definition = $field_storage_definition->getPropertyDefinition($main_property_name);
    return $main_property_definition ? $main_property_definition->getDataType() : NULL;
  }

  /**
   * Gets the mapped Schema.org property for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string
   *   The mapped Schema.org property for a field item.
   */
  protected function getSchemaProperty(FieldItemInterface $item): string {
    $entity = $item->getEntity();
    $field_name = $item->getFieldDefinition()->getName();

    $mapping = $this->getMappingStorage()->loadByEntity($entity);
    return $mapping->getSchemaPropertyMapping($field_name);
  }

  /**
   * Gets the file URI for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string
   *   The file URI for a field item.
   */
  protected function getFileUri(FieldItemInterface $item): string {
    return $item->entity->getFileUri();
  }

  /**
   * Gets the file URL for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string
   *   The file URL for a field item.
   */
  protected function getFileUrl(FieldItemInterface $item): string {
    $uri = $this->getFileUri($item);
    return $this->fileUrlGenerator->generateAbsoluteString($uri);
  }

  /**
   * Gets the selected image style for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return \Drupal\image\ImageStyleInterface|null
   *   The selected image style for a field item.
   */
  protected function getImageStyle(FieldItemInterface $item): ImageStyleInterface|NULL {
    $schema_property = $this->getSchemaProperty($item);
    $style = $this->getConfig()->get('schema_property_image_styles.' . $schema_property);
    if (!$style) {
      return NULL;
    }

    $image_style_storage = $this->entityTypeManager->getStorage('image_style');
    return $image_style_storage->load($style);
  }

  /**
   * Gets the image derivative URL for a field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return string|null
   *   The image derivative URL for a field item.
   */
  protected function getImageDerivativeUrl(FieldItemInterface $item): ?string {
    $field_type = $item->getFieldDefinition()->getFieldStorageDefinition()->getType();
    if ($field_type !== 'image') {
      return NULL;
    }
    $image_style = $this->getImageStyle($item);
    if (!$image_style) {
      return NULL;
    }
    $file_uri = $item->entity->getFileUri();
    return $image_style->buildUrl($file_uri);
  }

}
