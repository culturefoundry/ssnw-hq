<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Controller;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\schemadotorg_additional_mappings\SchemaDotOrgAdditionalMappingsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Schema.org report about and item routes.
 */
class SchemaDotOrgReportItemController extends SchemaDotOrgReportControllerBase {
  use SchemaDotOrgMappingStorageTrait;
  use SchemaDotOrgMappingStorageTrait;
  /**
   * The Schema.org mapping manager service.
   */
  protected SchemaDotOrgMappingManagerInterface $schemaMappingManager;

  /**
   * The Schema.org additional mappings manager service.
   */
  protected ?SchemaDotOrgAdditionalMappingsManagerInterface $additionalMappingsManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    $instance->additionalMappingsManager = $container->has('schemadotorg_additional_mappings.manager')
      ? $container->get('schemadotorg_additional_mappings.manager')
      : NULL;

    return $instance;
  }

  /**
   * Builds the Schema.org type or property item.
   *
   * @param string $id
   *   The Schema.org type or property ID.
   *
   * @return array
   *   A renderable array containing a Schema.org type or property item.
   */
  public function index(string $id = ''): array {
    if ($id === '') {
      return $this->about();
    }
    elseif ($this->schemaTypeManager->isType($id)) {
      return $this->item('types', $id);
    }
    elseif ($this->schemaTypeManager->isProperty($id)) {
      return $this->item('properties', $id);
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Route title callback.
   *
   * @param string $id
   *   The Schema.org type or property ID.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The title.
   */
  public function title(string $id): TranslatableMarkup {
    if (empty($id)) {
      return $this->t('Schema.org: About');
    }

    if ($this->schemaTypeManager->isDataType($id)) {
      $type = $this->t('Data type');
    }
    elseif ($this->schemaTypeManager->isEnumerationType($id)) {
      $type = $this->t('Enumeration type');
    }
    elseif ($this->schemaTypeManager->isEnumerationValue($id)) {
      $type = $this->t('Enumeration value');
    }
    elseif ($this->schemaTypeManager->isType($id)) {
      $type = $this->t('Type');
    }
    else {
      $type = $this->t('Property');
    }

    $t_args = ['@id' => $id, '@type' => $type];
    return $this->t('Schema.org: @id (@type)', $t_args);
  }

  /**
   * Build Schema.org about page.
   *
   * @return array
   *   A renderable array containing Schema.org about page.
   */
  protected function about(): array {
    $build = parent::buildHeader();

    // Introduction.
    $introduction = '<p>' . $this->t('<a href="https://Schema.org/">Schema.org</a> is a collaborative, community activity with a mission to create, maintain, and promote schemas for structured data on the Internet, on web pages, in email messages, and beyond.') . '</p>'
      . '<p>' . $this->t('Schema.org vocabulary can be used with many different encodings, including RDFa, Microdata and JSON-LD. These vocabularies cover entities, relationships between entities and actions, and can easily be extended through a well-documented extension model. Over 10 million sites use Schema.org to markup their web pages and email messages. Many applications from Google, Microsoft, Pinterest, Yandex and others already use these vocabularies to power rich, extensible experiences.') . '</p>'
      . '<p>' . $this->t('Founded by Google, Microsoft, Yahoo and Yandex, Schema.org vocabularies are developed by an open community process, using the public-schemaorg@w3.org mailing list and through GitHub.') . '</p>'
      . '<p>' . $this->t('A shared vocabulary makes it easier for webmasters and developers to decide on a schema and get the maximum benefit for their efforts. It is in this spirit that the founders, together with the larger community have come together - to provide a shared collection of schemas.') . '</p>';
    $build['introduction'] = ['#markup' => $introduction];

    // Divider.
    $build['divider'] = ['#markup' => '<hr/>'];

    // Description top.
    $t_args = [
      ':types_href' => Url::fromRoute('schemadotorg_report.types')->toString(),
      ':properties_href' => Url::fromRoute('schemadotorg_report.properties')->toString(),
      ':things_href' => Url::fromRoute('schemadotorg_report.types.things')->toString(),
    ];
    $description_top = '<p>'
      . $this->t('The schemas are a set of <a href=":types_href">types</a>, each associated with a set of <a href=":properties_href">properties</a>.', $t_args)
      . ' ' . $this->t('The types are arranged in a <a href=":things_href">hierarchy</a>.', $t_args)
      . '</p>';
    $build['description_top'] = ['#markup' => $description_top];

    // Types.
    $build['types'] = $this->getFilterForm('types');

    // Description bottom.
    $description_bottom = '<p>' . $this->t('Or you can jump directly to a commonly used type:') . '</p>';
    $description_bottom .= '<ul class="item-list">';
    $description_bottom .= '<li>' . $this->t('Creative works: <a title="CreativeWork" href="/CreativeWork">CreativeWork</a>, <a title="Book" href="/Book">Book</a>, <a title="Movie" href="/Movie">Movie</a>, <a title="MusicRecording" href="/MusicRecording">MusicRecording</a>, <a title="Recipe" href="/Recipe">Recipe</a>, <a title="TVSeries" href="/TVSeries">TVSeries</a> ...') . '</li>';
    $description_bottom .= '<li>' . $this->t('Embedded non-text objects: <a title="AudioObject" href="/AudioObject">AudioObject</a>, <a title="ImageObject" href="/ImageObject">ImageObject</a>, <a title="VideoObject" href="/VideoObject">VideoObject</a>') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Event" href="/Event">Event</a>') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a href="meddocs.html">Health and medical types</a>: notes on the health and medical types under <a title="MedicalEntity" href="/MedicalEntity">MedicalEntity</a>.') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Organization" href="/Organization">Organization</a>') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Person" href="/Person">Person</a>') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Place" href="/Place">Place</a>, <a title="LocalBusiness" href="/LocalBusiness">LocalBusiness</a>, <a title="Restaurant" href="/Restaurant">Restaurant</a> ...') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Product" href="/Product">Product</a>, <a title="Offer" href="/Offer">Offer</a>, <a title="AggregateOffer" href="/AggregateOffer">AggregateOffer</a>') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Review" href="/Review">Review</a>, <a title="AggregateRating" href="/AggregateRating">AggregateRating</a>') . '</li>';
    $description_bottom .= '<li>' . $this->t('<a title="Action" href="/Action">Action</a>') . '</li>';
    $description_bottom .= '</ul>';
    $path = Url::fromRoute('schemadotorg_report')->toString();
    $build['description_bottom'] = ['#markup' => str_replace('href="/', 'href="' . $path . '/', $description_bottom)];

    // About.
    $about = $this->config('schemadotorg_report.settings')->get('about');
    if ($about) {
      $build['about'] = [
        'title' => [
          '#markup' => '<p>' . $this->t('Learn more about Schema.org') . '</p>',
        ],
        'links' => [
          '#theme' => 'item_list',
          '#items' => $this->buildReportLinks($about),
        ],
      ];
    }

    return $build;
  }

  /**
   * Build Schema.org type or property item.
   *
   * @param string $table
   *   Types or properties table name.
   * @param string $id
   *   Type or property id (a.k.a. label).
   *
   * @return array
   *   A renderable array containing Schema.org type or property item.
   */
  protected function item(string $table, string $id): array {
    // Fields.
    $fields = ($table === 'types')
      ? $this->getTypeFields()
      : $this->getPropertyFields();

    // Item.
    $item = $this->schemaTypeManager->getItem($table, $id);

    // Item.
    $build = parent::buildHeader($table);

    // Hide label and display the Schema.org type or property id.
    if ($this->isAjax()) {
      $build['title'] = [
        '#markup' => $id,
        '#prefix' => '<h1>',
        '#suffix' => '</h1>',
      ];
      unset($fields['label']);
    }

    // Pending warning.
    if ($item['is_part_of'] === 'https://pending.schema.org') {
      $t_args = [':href' => 'https://schema.org/docs/pending.home.html'];
      $build['pending'] = [
        '#markup' => $this->t('This term is proposed for full integration into Schema.org, <a href=":href">pending</a> implementation feedback and adoption from applications and websites.', $t_args),
        '#prefix' => '<p><em>',
        '#suffix' => '</em></p>',
      ];
    }

    foreach ($fields as $name => $label) {
      $value = $item[$name] ?? NULL;
      if (empty($value)) {
        continue;
      }

      $build[$name] = [
        '#type' => 'item',
        '#title' => $label,
      ];
      switch ($name) {
        case 'id':
          $build[$name]['link'] = [
            '#type' => 'link',
            '#title' => $value,
            '#url' => Url::fromUri($value),
          ];
          $links = [
            'types' => $this->t('References'),
            'issues' => $this->t('Issues/Discussions'),
          ];
          foreach ($links as $link_type => $link_title) {
            $type_items = $this->config('schemadotorg_report.settings')->get("$link_type.$id");
            if ($type_items) {
              $type_links = $this->buildReportLinks($type_items);
              foreach ($type_links as &$type_link) {
                $type_link['#prefix'] = '<div>';
                $type_link['#suffix'] .= '</div>';
              }
              $build[$link_type] = [
                '#type' => 'item',
                '#title' => $link_title,
                'items' => $type_links,
              ];
            }
          }
          break;

        case 'label':
          $build[$name]['#plain_text'] = $value;
          break;

        case 'comment':
          $options = ['base_path' => Url::fromRoute('schemadotorg_report')->toString() . '/'];
          $build[$name]['#markup'] = $this->schemaTypeBuilder->formatComment($value, $options);
          break;

        case 'properties':
          $properties = $this->schemaTypeManager->parseIds($value);
          $build[$name] = [
            '#type' => 'details',
            '#title' => $label,
            '#open' => TRUE,
            'items' => $this->buildTypeProperties($properties),
          ];

          // Get default properties from type breadcrumb.
          $schema_types_default_properties = $this->config('schemadotorg.settings')
            ->get('schema_types.default_properties');
          $breadcrumbs = $this->schemaTypeManager->getTypeBreadcrumbs($id);
          $default_properties = [];
          $default_properties_breadcrumb = [];
          foreach ($breadcrumbs as $breadcrumb) {
            foreach ($breadcrumb as $breadcrumb_type) {
              if (isset($schema_types_default_properties[$breadcrumb_type])) {
                $default_properties += array_combine($schema_types_default_properties[$breadcrumb_type], $schema_types_default_properties[$breadcrumb_type]);
                $default_properties_breadcrumb[$breadcrumb_type] = $this->schemaTypeBuilder->buildItemsLinks($schema_types_default_properties[$breadcrumb_type]) + [
                  '#prefix' => $breadcrumb_type . ' | ',
                  '#suffix' => '<br/>',
                ];
              }
            }
          }
          if ($default_properties_breadcrumb) {
            $build[$name]['default_properties'] = [];
            $build[$name]['default_properties']['default'] = [
              '#type' => 'item',
              '#title' => $this->t('Default properties'),
              'links' => $default_properties_breadcrumb,
            ];

            // Display all entity types that have differing default properties.
            // This generally only applies to nodes.
            $entity_type_definitions = $this->getMappingTypeStorage()->getEntityTypeBundleDefinitions();
            foreach ($entity_type_definitions as $entity_type_id => $entity_type_definition) {
              $bundle_entity_type_id = $entity_type_definition->id();
              $mapping_type = $this->loadMappingType($entity_type_id);
              $bundle_entity_type_label = ($entity_type_id === 'paragraph')
                ? 'paragraph type'
                : $this->entityTypeManager()->getDefinition($bundle_entity_type_id)->getSingularLabel();
              $mapping_default_properties = $mapping_type->getDefaultSchemaTypeProperties($id);

              if ($mapping_default_properties != $default_properties) {
                $t_args = ['@type' => $bundle_entity_type_label];
                $build[$name]['default_properties'][$entity_type_id] = [
                  '#type' => 'item',
                  '#title' => $this->t('Default @type properties', $t_args),
                  'links' => $this->schemaTypeBuilder->buildItemsLinks($mapping_default_properties),
                ];
              }
            }
          }

          // Get additional mappings properties.
          if ($this->moduleHandler()->moduleExists('schemadotorg_additional_mappings')) {
            $additional_mappings = $this->additionalMappingsManager
              ->getDefaultAdditionalMappings('node', NULL, $id);
            if ($additional_mappings) {
              $additional_mappings_links = [];
              foreach ($additional_mappings as $schema_type => $additional_mapping) {
                $additional_mappings_links[$schema_type] = $this->schemaTypeBuilder->buildItemsLinks($additional_mapping['schema_properties'])
                  + [
                    '#suffix' => '<br/>',
                    '#prefix' => $additional_mapping['schema_type'] . ' | ',
                  ];
              }
              $build[$name]['schemadotorg_additional_mappings'] = [
                '#type' => 'item',
                '#title' => $this->t('Default additional mappings properties'),
                'links' => $additional_mappings_links,
              ];
            }
          }

          // Get all properties, excluding superseded properties.
          $all_properties = $this->database->select('schemadotorg_properties', 'properties')
            ->fields('properties', ['label'])
            ->condition('label', $properties, 'IN')
            ->condition('superseded_by', '')
            ->orderBy('label')
            ->execute()
            ->fetchCol();
          $all_properties = array_combine($all_properties, $all_properties);
          $build[$name]['all_properties'] = [
            '#type' => 'item',
            '#title' => $this->t('All properties'),
            'links' => $this->schemaTypeBuilder->buildItemsLinks($all_properties) + [
              '#prefix' => $item['label'] . ' | ',
            ],
          ];

          // Get ignored properties.
          $ignored_properties = $this->config('schemadotorg.settings')
            ->get('schema_properties.ignored_properties');
          $ignored_properties = $ignored_properties ? array_combine($ignored_properties, $ignored_properties) : [];
          $ignored_properties = array_intersect_key($ignored_properties, $all_properties);
          if ($ignored_properties) {
            $build[$name]['ignored_properties'] = [
              '#type' => 'item',
              '#title' => $this->t('Ignored properties'),
              'links' => $this->schemaTypeBuilder->buildItemsLinks($ignored_properties),
            ];
          }

          // Get all range includes.
          $range_includes_ids = $this->database->select('schemadotorg_properties', 'properties')
            ->fields('properties', ['range_includes'])
            ->condition('label', $properties, 'IN')
            ->orderBy('label')
            ->execute()
            ->fetchCol();
          $all_range_includes = [];
          foreach ($range_includes_ids as $range_include_ids) {
            $ids = $this->schemaTypeManager->parseIds($range_include_ids);
            $all_range_includes += array_combine($ids, $ids);
          }
          ksort($all_range_includes);
          $build[$name]['all_range_includes'] = [
            '#type' => 'item',
            '#title' => $this->t('All range includes'),
            'links' => $this->schemaTypeBuilder->buildItemsLinks($all_range_includes),
          ];
          break;

        default:
          $build[$name]['links'] = $this->schemaTypeBuilder->buildItemsLinks($value);
      }
    }

    // Get mapping defaults.
    if ($table === 'types') {
      $default_entity_type_id = $this->getDefaultEntityTypeId($id);
      $mapping_defaults = $this->schemaMappingManager->getMappingDefaults(
        entity_type_id: $default_entity_type_id,
        schema_type: $id,
      );
      $build['mapping_defaults'] = [
        '#type' => 'details',
        '#title' => $this->t('Mapping defaults'),
        '#description' => $this->t('The below mapping defaults are used when the Schema.org type is created.'),
        'code' => [
          '#type' => 'html_tag',
          '#tag' => 'pre',
          '#plain_text' => Yaml::encode($mapping_defaults),
          '#attributes' => ['data-schemadotorg-codemirror-mode' => 'text/x-yaml'],
          '#attached' => ['library' => ['schemadotorg/codemirror.yaml']],
        ],
      ];
    }

    // Custom fields.
    if ($table === 'types') {
      // Add type.
      $add_type = $this->buildAddType($id);
      if ($add_type) {
        $build['add_type'] = $add_type;
      }

      // Parents.
      $build['parents'] = [
        '#weight' => '-10',
        '#suffix' => '<hr/>',
        'breadcrumbs' => $this->buildTypeBreadcrumbs($id),
      ];

      // Subtype.
      if ($item['sub_types']) {
        $subtypes = $this->schemaTypeManager->parseIds($item['sub_types']);
        $tree = $this->schemaTypeManager->getTypeTree($subtypes);
        $build['sub_types_hierarchy'] = [
          '#type' => 'details',
          '#title' => $this->t('More specific types'),
          'items' => $this->schemaTypeBuilder->buildTypeTree($tree),
        ];
      }

      // Enumerations.
      $build['enumerations'] = $this->buildTypeEnumerations($id);

      // Appears in.
      $build['appears_in'] = $this->buildTypeAppearsIn($id);
    }

    return $build;
  }

  /**
   * Build Schema.org type properties table.
   *
   * @param array $properties
   *   An array of Schema.org properties.
   *
   * @return array
   *   A renderable array containing a Schema.org type properties table.
   */
  protected function buildTypeProperties(array $properties): array {
    $header = [
      'label' => [
        'data' => $this->t('Label'),
      ],
      'comment' => [
        'data' => $this->t('Comment'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'range_includes' => [
        'data' => $this->t('Range includes'),
      ],
      'superseded_by' => [
        'data' => $this->t('Superseded by'),
      ],
    ];

    // Query.
    $result = $this->database->select('schemadotorg_properties', 'properties')
      ->fields('properties', array_keys($header))
      ->condition('label', $properties, 'IN')
      ->orderBy('label')
      ->execute();

    // Rows.
    $rows = [];
    while ($record = $result->fetchAssoc()) {
      $row = [];
      foreach ($record as $name => $value) {
        $row[$name] = $this->buildTableCell($name, $value);
      }
      $rows[] = $row;
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => ['class' => ['schemadotorg-report-table']],
    ];
  }

  /**
   * Build Schema.org type enumerations.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   A renderable array containing schema.org type enumerations.
   */
  protected function buildTypeEnumerations(string $type): array {
    $enumerations = $this->schemaTypeManager->getEnumerations($type);
    if (!$enumerations) {
      return [];
    }

    array_walk($enumerations, function (&$enumeration): void {
      $enumeration = Link::fromTextAndUrl($enumeration, $this->schemaTypeBuilder->getItemUrl($enumeration))->toRenderable();
    });

    return [
      '#type' => 'fieldset',
      '#title' => $this->t('Enumeration members'),
      'items' => [
        '#theme' => 'item_list',
        '#items' => $enumerations,
      ],
    ];
  }

  /**
   * Build Schema.org type appears inc.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array
   *   A renderable array containing Schema.org type appears in.
   */
  protected function buildTypeAppearsIn(string $type): array {
    $header = [
      'label' => [
        'data' => $this->t('Label'),
      ],
      'domain_includes' => [
        'data' => $this->t('Domain includes'),
      ],
      'comment' => [
        'data' => $this->t('Comment'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];

    // Query.
    $result = $this->database->select('schemadotorg_properties', 'properties')
      ->fields('properties', ['label', 'domain_includes', 'comment'])
      ->condition('range_includes', '%' . $type . '%', 'LIKE')
      ->orderBy('label')
      ->execute();

    // Rows.
    $rows = [];
    while ($record = $result->fetchAssoc()) {
      $row = [];
      foreach ($record as $name => $value) {
        $row[$name] = $this->buildTableCell($name, $value);
      }
      $rows[] = $row;
    }
    if (!$rows) {
      return [];
    }

    return [
      '#type' => 'details',
      '#title' => $this->t('Appears in (via range includes)'),
      '#description' => $this->t('Instances of @type may appear as a value for the following properties', ['@type' => $type]),
      '#open' => TRUE,
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => ['class' => ['schemadotorg-report-table']],
      ],
    ];
  }

  /**
   * Build add Schema.org type operation dropdown.
   *
   * @param string $type
   *   The Schema.org type.
   *
   * @return array|null
   *   A renderable array containing the add Schema.org type operation dropdown.
   *
   * @see \Drupal\schemadotorg_ui\Routing\SchemaDotOrgRouteSubscriber
   */
  protected function buildAddType(string $type): ?array {
    if (!$this->moduleHandler()->moduleExists('schemadotorg_ui')) {
      return NULL;
    }

    if (!$this->schemaTypeManager->isThing($type)) {
      return NULL;
    }

    // Get operations.
    $operations = [];
    /** @var \Drupal\Core\Config\Entity\ConfigEntityType[] $entity_type_definitions */
    $entity_type_definitions = $this->getMappingTypeStorage()->getEntityTypeBundleDefinitions();
    foreach ($entity_type_definitions as $entity_type_id => $entity_type_definition) {
      $bundle_entity_type_id = $entity_type_definition->id();
      $bundle_entity_type_label = ($entity_type_id === 'paragraph')
        ? 'paragraph type'
        : $this->entityTypeManager()->getDefinition($bundle_entity_type_id)->getSingularLabel();
      $t_args = ['@type' => $bundle_entity_type_label];
      $operations[$entity_type_id] = [
        'title' => $this->t('Add Schema.org @type', $t_args),
        'url' => Url::fromRoute("schemadotorg.{$bundle_entity_type_id}.type_add", ['type' => $type]),
      ];
    }

    // Make sure there are operations.
    if (!$operations) {
      return NULL;
    }

    // Add the default operation.
    $default_entity_type = $this->getDefaultEntityTypeId($type);
    if (isset($operations[$default_entity_type])) {
      $default_operation = $operations[$default_entity_type];
      $default_operation['title'] = $this->t('Add Schema.org type');
      $operations = ['default' => $default_operation] + $operations;
    }

    return [
      '#weight' => '-10',
      '#type' => 'operations',
      '#links' => $operations,
      '#prefix' => '<div class="schemadotorg-report-add-type">',
      '#suffix' => '</div>',
    ];
  }

  /**
   * Get the default entity type id for a Schema.org type.
   *
   * @param string $type
   *   A Schema.org type.
   *
   * @return string
   *   The default entity type id for the Schema.org type.
   */
  protected function getDefaultEntityTypeId(string $type): string {
    if ($this->schemaTypeManager->isSubTypeOf($type, 'Intangible')
      && $this->loadMappingType('paragraph')) {
      return 'paragraph';
    }
    elseif ($this->schemaTypeManager->isSubTypeOf($type, 'MediaObject')
      && $this->loadMappingType('media')) {
      return 'media';
    }
    else {
      return 'node';
    }
  }

  /**
   * Gets Schema.org type fields.
   *
   * @return array
   *   Schema.org type fields.
   */
  protected function getTypeFields(): array {
    return [
      'id' => $this->t('ID'),
      'label' => $this->t('Label'),
      'comment' => $this->t('Comment'),
      'sub_type_of' => $this->t('Sub type of'),
      'is_part_of' => $this->t('Is part of'),
      'enumerationtype' => $this->t('Enumeration type'),
      'equivalent_class' => $this->t('Equivalent class'),
      'sub_types' => $this->t('Sub types'),
      'supersedes' => $this->t('supersedes'),
      'superseded_by' => $this->t('Superseded by'),
      'properties' => $this->t('Properties'),
    ];
  }

  /**
   * Gets Schema.org property fields.
   *
   * @return array
   *   Schema.org Property fields.
   */
  protected function getPropertyFields(): array {
    return [
      'id' => $this->t('ID'),
      'label' => $this->t('Label'),
      'comment' => $this->t('Comment'),
      'inverse_of' => $this->t('Inverse of'),
      'domain_includes' => $this->t('Domain includes'),
      'range_includes' => $this->t('Range includes'),
      'sub_property_of' => $this->t('Sub property of'),
      'equivalent_property' => $this->t('Equivalent property'),
      'subproperties' => $this->t('Sub properties'),
      'supersedes' => $this->t('Supersedes'),
      'superseded_by' => $this->t('Superseded by'),
      'is_part_of' => $this->t('Is part of'),
    ];
  }

}
