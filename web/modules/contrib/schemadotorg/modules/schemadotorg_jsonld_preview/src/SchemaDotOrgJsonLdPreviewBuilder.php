<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_preview;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\AccessAwareRouterInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;

/**
 * Schema.org JSON-LD preview builder.
 */
class SchemaDotOrgJsonLdPreviewBuilder implements SchemaDotOrgJsonLdPreviewBuilderInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgJsonLdPreviewManager object.
   *
   * @param \Drupal\Core\Routing\AccessAwareRouterInterface $routeProvider
   *   The route provider.
   * @param \Drupal\Core\Routing\AdminContext $routerAdminContext
   *   The router admin context.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names service.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager
   *   The Schema.org JSON-LD manager service.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface $schemaJsonLdBuilder
   *   The Schema.org JSON-LD builder service.
   */
  public function __construct(
    protected AccessAwareRouterInterface $routeProvider,
    protected AdminContext $routerAdminContext,
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgJsonLdManagerInterface $schemaJsonLdManager,
    protected SchemaDotOrgJsonLdBuilderInterface $schemaJsonLdBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(string $format = self::JSONLD, ?RouteMatchInterface $route_match = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): ?array {
    $bubbleable_metadata = $bubbleable_metadata ?? new BubbleableMetadata();

    // Build the entity's Schema.org data.
    $data = $this->schemaJsonLdBuilder->build($route_match, $bubbleable_metadata);
    if (!$data) {
      return NULL;
    }

    // Display the JSON-LD using a details element.
    $build = [
      '#type' => 'container',
    ];

    // Apply bubbleable metadata to the render array so that the block is
    // properly cached.
    $bubbleable_metadata->applyTo($build);

    // Attach library after the bubbleable metadata is applied to
    // the render array.
    $build['#attached']['library'][] = 'schemadotorg_jsonld_preview/schemadotorg_jsonld_preview';

    return ($format === static::DATA)
      ? $this->buildData($build, $data)
      : $this->buildJsonLd($build, $data);
  }

  /**
   * Build Schema.org JSON-LD preview.
   *
   * @param array $build
   *   The renderable array.
   * @param array $data
   *   The JSON-LD data.
   *
   * @return array
   *   A renderable array containing the JSON-LD preview.
   */
  protected function buildJsonLd(array $build, array $data): array {
    // Set the container's attributes.
    $build['#attributes'] = [
      'class' => [
        'schemadotorg-jsonld-preview',
        'js-schemadotorg-jsonld-preview',
      ],
    ];

    // Make it easy for someone to copy the JSON.
    $t_args = [
      ':schema_href' => 'https://validator.schema.org/',
      ':google_href' => 'https://search.google.com/test/rich-results',
    ];
    $description = $this->t('Please copy-n-paste the below JSON-LD into the <a href=":schema_href">Schema Markup Validator</a> or  <a href=":google_href">Google\'s Rich Results Test</a>.', $t_args);
    $build['copy'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['schemadotorg-jsonld-preview-copy']],
      'description' => [
        '#type' => 'container',
        '#markup' => $description,
      ],
      'button' => [
        '#type' => 'button',
        '#button_type' => 'small',
        '#attributes' => ['class' => ['schemadotorg-jsonld-preview-copy-button', 'button--extrasmall']],
        '#value' => $this->t('Copy JSON-LD'),
      ],
      'message' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => ['class' => ['schemadotorg-jsonld-preview-copy-message']],
        '#plain_text' => $this->t('JSON-LD copied to clipboard…'),
      ],
    ];

    // JSON.
    // Make the JSON pretty and enhance it.
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $build['json'] = [
      'input' => [
        '#type' => 'hidden',
        '#value' => $json,
      ],
      'code' => [
        '#type' => 'html_tag',
        '#tag' => 'pre',
        '#plain_text' => $json,
        '#attributes' => ['data-schemadotorg-codemirror-mode' => 'application/ld+json'],
        '#attached' => ['library' => ['schemadotorg/codemirror.javascript']],
      ],
    ];

    // JSON-LD endpoint.
    // @see schemadotorg_jsonld_endpoint.module
    $entity = $this->schemaJsonLdManager->getRouteMatchEntity();
    if ($entity && $this->moduleHandler->moduleExists('schemadotorg_jsonld_endpoint')) {
      $entity_type_id = $entity->getEntityTypeId();
      $route_name = 'schemadotorg_jsonld_endpoint.' . $entity_type_id;
      $route_parameters = ['entity' => $entity->uuid()];
      $route_options = ['absolute' => TRUE];

      // Make sure the JSON-LD route exists.
      // @see \Drupal\schemadotorg_jsonld_endpoint\Routing\SchemaDotOrgJsonLdEndpointRoutes::routes
      if ($this->routeProvider->getRouteCollection()->get($route_name)) {
        $jsonld_url = Url::fromRoute($route_name, $route_parameters, $route_options);

        // Allow other modules to link to additional endpoints.
        // @see schemadotorg_taxonomy_entity_view_alter()
        $build['endpoints'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['schemadotorg-jsonld-preview-endpoints']],
        ];
        $build['endpoints'][$entity_type_id] = [
          '#type' => 'item',
          '#title' => $this->t('JSON-LD endpoint'),
          '#wrapper_attributes' => ['class' => ['container-inline']],
          'link' => [
            '#type' => 'link',
            '#url' => $jsonld_url,
            '#title' => $jsonld_url->toString(),
          ],
        ];
      }
    }
    return $build;
  }

  /**
   * Build Schema.org data (table) preview.
   *
   * @param array $build
   *   The renderable array.
   * @param array $data
   *   The JSON-LD data.
   *
   * @return array
   *   A renderable array containing the JSON-LD preview.
   */
  protected function buildData(array $build, array $data): array {
    $build['data'] = [];
    if (array_is_list($data)) {
      foreach ($data as $item) {
        $build['data'][$item['@type']] = $this->buildDataSchemaType($item);
      }
    }
    else {
      $build['data'] = $this->buildDataSchemaType($data)
        + ['#open' => TRUE];
    }
    return $build;
  }

  /**
   * Build Schema.org type data inside a details widget.
   *
   * @param array $data
   *   Schema.org type data.
   *
   * @return array
   *   A renderable array containing Schema.org type data
   *   inside a details widget.
   */
  protected function buildDataSchemaType(array $data): array {
    $schema_type = $data['@type'];

    $build = [
      '#type' => 'details',
      '#title' => $schema_type,
      '#attributes' => [
        'data-schemadotorg-details-key' => 'schemadotorg-data-preview-' . $this->schemaNames->camelCaseToSnakeCase($schema_type),
      ],
      'table' => [
        '#theme' => 'table',
        '#rows' => $this->buildRows($data),
        '#attributes' => [
          'class' => ['schemadotorg-data-preview'],
        ],
      ],
    ];

    return $build;
  }

  /**
   * Build rows.
   *
   * @param array $data
   *   The rows' data.
   * @param int $indent
   *   THe rows' index.
   * @param string|int|null $parent
   *   The rows' parent key.
   *
   * @return array
   *   Rows.
   */
  protected function buildRows(array $data, int $indent = 1, string|int|null $parent = NULL): array {
    $is_list = array_is_list($data);

    $rows = [];
    foreach ($data as $key => $value) {
      if (!is_array($value)) {
        // If not an array, build the simple row.
        $rows[] = $this->buildRow($key, $value, $indent);
      }
      elseif (!array_is_list($value)) {
        // If not a list, build a heading and then build all the child rows.
        $rows[] = $this->buildRow(($is_list) ? $parent : $key, NULL, $indent);
        $rows = array_merge($rows, $this->buildRows($value, $indent + 1, $key));
      }
      elseif ($this->isMultiDimensionalArray($value)) {
        // If multidimensional, build all the child rows.
        $rows = array_merge($rows, $this->buildRows($value, $indent, $key));
      }
      else {
        // If a simple key/value pair array, build the simple rows.
        foreach ($value as $item) {
          $rows[] = $this->buildRow($key, $item, $indent);
        }
      }
    }
    return $rows;
  }

  /**
   * Build a row.
   *
   * @param string $key
   *   The row's keu.
   * @param mixed|null $value
   *   THe row's value.
   * @param int $indent
   *   The row's indent.
   *
   * @return array|array[]
   *   The row.
   */
  protected function buildRow(string $key, mixed $value, int $indent = 0): array {
    if (is_null($value)) {
      return [
        [
          'data' => [
            '#markup' => $key,
            '#prefix' => '<strong>',
            '#suffix' => '</strong>',
          ],
          'colspan' => 2,
          'style' => 'padding-left:' . ($indent * 30) . 'px',
        ],
      ];
    }
    else {
      if (is_bool($value)) {
        $value = $value ? 'True' : 'False';
      }
      elseif (is_string($value)) {
        // Add a <wbr/> after each slash within all URLs.
        $value = preg_replace_callback(
          '/(https?:\/\/[^\s]+)/i',
          fn($matches) => str_replace('/', '/<wbr/>', $matches[0]),
          htmlentities($value)
        );
      }

      return [
        [
          'data' => [
            '#markup' => $key,
            '#prefix' => '<strong>',
            '#suffix' => '</strong>',
          ],
          'style' => 'padding-left:' . ($indent * 30) . 'px',
        ],
        ['data' => ['#markup' => $value]],
      ];
    }
  }

  /**
   * Determine if an array is multidimensional.
   *
   * @param array $array
   *   An array.
   *
   * @return bool
   *   TRUE if an array is multidimensional.
   */
  protected function isMultiDimensionalArray(array $array): bool {
    return (count($array) !== count($array, COUNT_RECURSIVE));
  }

}
