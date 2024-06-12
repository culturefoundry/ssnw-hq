<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_export\Controller;

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Component\Utility\SortArray;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\field\FieldConfigInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Returns responses for Schema.org mapping export.
 */
class SchemaDotOrgExportMappingController extends ControllerBase {

  /**
   * The Schema.org mapping.
   */
  protected ?SchemaDotOrgMappingInterface $mapping;

  /**
   * The field definitions.
   *
   * @var \Drupal\Core\Field\FieldDefinitionInterface[]
   */
  protected array $fieldDefinitions;

  /**
   * The form components.
   */
  protected array $components;

  /**
   * The controller constructor.
   */
  public function __construct(
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected EntityFieldManagerInterface $entityFieldManager,
    protected RendererInterface $renderer,
    protected SchemaDotOrgNamesInterface $schemaNames,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('entity_display.repository'),
      $container->get('entity_field.manager'),
      $container->get('renderer'),
      $container->get('schemadotorg.names'),
    );
  }

  /**
   * Returns response for Schema.org mapping export request.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   A streamed HTTP response containing a Schema.org mapping CSV export.
   */
  public function overview(): StreamedResponse {
    $response = new StreamedResponse(function (): void {
      $additional_type_installed = $this->moduleHandler()
        ->moduleExists('schemadotorg_additional_type');

      $handle = fopen('php://output', 'r+');

      // Header.
      $header = [];
      $header[] = 'entity_type';
      $header[] = 'bundle';
      $header[] = 'schema_type';
      if ($additional_type_installed) {
        $header[] = 'schema_additional_type';
      }
      $header[] = 'schema_properties';
      fputcsv($handle, $header);

      // Rows.
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface[] $mappings */
      $mappings = $this->entityTypeManager()
        ->getStorage('schemadotorg_mapping')
        ->loadMultiple();
      foreach ($mappings as $mapping) {
        $row = [];
        $row[] = $mapping->getTargetEntityTypeId();
        $row[] = $mapping->getTargetBundle();
        $row[] = $mapping->getSchemaType();
        if ($additional_type_installed) {
          $row[] = ($mapping->getSchemaPropertyFieldName('additionalType')) ? $this->t('Yes') : $this->t('No');
        }
        $row[] = implode('; ', $mapping->getSchemaProperties());
        fputcsv($handle, $row);
      }
      fclose($handle);
    });

    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-Disposition', 'attachment; filename="schemadotorg_mapping.csv"');
    return $response;
  }

  /**
   * Returns response for Schema.org mapping HTML export request.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   A HTTP response containing a Schema.org mapping HTML export.
   */
  public function details(SchemaDotOrgMappingInterface $schemadotorg_mapping): Response {
    $this->mapping = $schemadotorg_mapping;

    $entity_type_id = $this->mapping->getTargetEntityTypeId();
    $bundle = $this->mapping->getTargetBundle();
    $this->fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $bundle);

    $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle);
    $this->components = $form_display->getComponents();

    // Header.
    $header = [
      'name' => $this->t('Field name / Field type'),
      'label' => $this->t('Field label'),
      'description' => $this->t('Field description'),
      'settings' => $this->t('Field settings</br>(prefix / suffix / options)'),
      'schemadotorg' => $this->t('Schema.org property'),
    ];
    foreach ($header as &$column) {
      $column = [
        'data' => $column,
        'valign' => 'top',
        'align' => 'left',
        'style' => 'background-color: #ccc',
      ];
    }

    // Rows.
    $field_groups = $form_display->getThirdPartySettings('field_group');
    if ($field_groups) {
      uasort($field_groups, [SortArray::class, 'sortByWeightElement']);
      $rows = $this->buildFieldGroups($field_groups);

      $other_children = [];
      foreach (array_keys($this->components) as $field_name) {
        $field_definition = $this->fieldDefinitions[$field_name] ?? NULL;
        if ($field_definition instanceof FieldConfigInterface) {
          $other_children[] = $field_name;
        }
      }
      if ($other_children) {
        $rows += $this->buildFieldGroups([
          'other' => [
            'label' => $this->t('Other'),
            'children' => $other_children,
          ],
        ]);
      }
    }
    else {
      $rows = $this->buildComponents($this->components);
    }
    foreach ($rows as &$row) {
      $row = [
        'data' => $row,
        'valign' => 'top',
        'align' => 'left',
      ];
    }

    // Build.
    $build = [];
    $build['message'] = [
      '#markup' => $this->t('Cut and paste the HTML below into a Google Doc or MS Word.'),
      '#prefix' => '<p align="center"><em>',
      '#suffix' => '</em></p><hr/>',
    ];
    $build['entity_type'] = $this->buildEntityType();
    $build['table'] = [
      '#type' => 'table',
      '#attributes' => [
        'border' => 1,
        'cellspacing' => 0,
        'cellpadding' => 5,
        'width' => '950',
      ],
      '#header' => $header,
      '#rows' => $rows,
    ];

    // Render and strip unwanted attributes.
    $output = (string) DeprecationHelper::backwardsCompatibleCall(
      currentVersion: \Drupal::VERSION,
      deprecatedVersion: '10.3',
      currentCallable: fn() => $this->renderer->renderInIsolation($build),
      deprecatedCallable: fn() => $this->renderer->renderPlain($build),
    );
    $output = preg_replace('/ (class|data-striping)="[^"]+"/', '', $output);

    // Wrap output in a simple <html> template.
    $output = '<!DOCTYPE html>
<html lang="en">
<head><title>' . $this->mapping->getTargetEntityBundleEntity()->label() . '</title></head>
<body>' . $output . '</body>
</html>';
    $header = [
      'Content-Length' => strlen($output),
      'Content-Type' => 'text/html',
    ];
    return new Response($output, 200, $header);
  }

  /**
   * Build entity type information.
   *
   * @return array
   *   A renderable array containing node type information.
   */
  protected function buildEntityType(): array {
    $bundle_entity = $this->mapping->getTargetEntityBundleEntity();
    $build = [];
    $build['title'] = [
      '#markup' => $bundle_entity->label(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];
    $build['description'] = [
      '#markup' => $bundle_entity->get('description') ?? '',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $uri = 'https://schema.org/' . $this->mapping->getSchemaType();
    $build['schemadotorg'] = [
      'link' => [
        '#type' => 'link',
        '#title' => $uri,
        '#url' => Url::fromUri($uri),
      ],
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Add information.
    $build['information'] = [
      '#prefix' => '<hr/><p>',
      '#suffix' => '</p>',
    ];
    $values = [
      (string) $this->t('Entity type') => $this->mapping->getTargetEntityTypeId(),
      (string) $this->t('Bundle') => $this->mapping->getTargetBundle(),
      (string) $this->t('Exported') => date('F j, Y, g:i a'),
      (string) $this->t('Exported by') => $this->currentUser()->getDisplayName(),
    ];
    foreach ($values as $label => $value) {
      $build['information'][$label] = [
        '#markup' => $this->t('<strong>@label:</strong> @value', ['@label' => $label, '@value' => $value]),
        '#suffix' => '<br/>',
      ];
    }
    return $build;
  }

  /**
   * Build field group table rows.
   *
   * @param array $field_groups
   *   THe field groups.
   *
   * @return array
   *   Field group table rows.
   */
  public function buildFieldGroups(array $field_groups): array {
    $rows = [];
    foreach ($field_groups as $field_group_name => $field_group) {
      $data = [];
      $data['name'] = $this->formatNameTableCell($field_group_name, $field_group['format_type'] ?? '');
      $data['label'] = $field_group['label'];
      $data['description'] = $field_group['format_settings']['description'] ?? '';
      $data['settings'] = '';
      $data['schemadotorg'] = '';

      $row = [];
      foreach ($data as $key => $cell) {
        $row[$key] = (!is_array($cell))
          ? ['data' => ['#markup' => $cell]]
          : $cell;

        $row[$key]['data']['#prefix'] = '<strong>';
        $row[$key]['data']['#suffix'] = '</strong>';
        $row[$key]['style'] = 'background-color:#eee';
      }
      $rows[$field_group_name] = $row;

      $children_keys = array_combine($field_group['children'], $field_group['children']);
      $children = array_intersect_key($this->components, $children_keys);
      $this->components = array_diff_key($this->components, $children_keys);
      uasort($children, [SortArray::class, 'sortByWeightElement']);
      $rows += $this->buildComponents($children);
    }

    return $rows;
  }

  /**
   * Build components table rows.
   *
   * @param array $components
   *   THe components.
   *
   * @return array
   *   Components table rows.
   */
  protected function buildComponents(array $components): array {
    $schema_properties = ($this->mapping)
      ? $this->mapping->getAllSchemaProperties()
      : [];

    $rows = [];
    foreach ($components as $field_name => $component) {
      $field_definition = $this->fieldDefinitions[$field_name] ?? NULL;
      if (!$field_definition instanceof FieldDefinitionInterface) {
        continue;
      }

      $schema_property = $schema_properties[$field_name] ?? NULL;

      $row = [];
      $row['name'] = $this->formatNameTableCell($field_name, $field_definition->getType());
      $row['label'] = $field_definition->getLabel();
      $row['description'] = ['data' => ['#markup' => $field_definition->getDescription()]];
      $row['settings'] = [
        'data' => [
          '#markup' => $this->formatSettings($field_definition->getSettings())
            . $this->formatFieldOptions($field_definition),
        ],
      ];
      $row['schemadotorg'] = $this->buildSchemaProperty($schema_property);

      $rows[$field_name] = $row;

      switch ($field_definition->getType()) {
        case 'custom';
          $rows += $this->buildCustomField($field_definition);
          break;
      }
    }

    return $rows;
  }

  /**
   * Build custom field table rows.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The custom field definition.
   *
   * @return array
   *   Custom field table rows.
   */
  protected function buildCustomField(FieldDefinitionInterface $field_definition): array {
    $field_name = $field_definition->getName();

    $rows = [];
    $settings = $field_definition->getSettings();
    $field_settings = $settings['field_settings'];
    uasort($field_settings, [SortArray::class, 'sortByWeightElement']);
    foreach ($field_settings as $custom_field_name => $custom_field_setting) {
      $property = ($this->mapping)
        ? $this->schemaNames->snakeCaseToCamelCase($custom_field_name)
        : NULL;

      $row = [];
      $row['name'] = $this->formatNameTableCell($field_name . ':' . $custom_field_name, $custom_field_setting['type']);
      $row['label'] = $custom_field_setting['widget_settings']['label'];
      $row['description'] = $custom_field_setting['widget_settings']['settings']['description'];
      $row['settings'] = [
        'data' => [
          '#markup' => $this->formatSettings($custom_field_setting['widget_settings']['settings']),
        ],
      ];
      $row['schemadotorg'] = $this->buildSchemaProperty($property);
      $rows[$field_name . ':' . $custom_field_name] = $row;
    }

    return $rows;
  }

  /**
   * Build a link to a Schema.org property.
   *
   * @param string|null $property
   *   The Schema.org property.
   *
   * @return array|null
   *   A link to a Schema.org property.
   */
  protected function buildSchemaProperty(?string $property): ?array {
    return ($property) ? [
      'data' => [
        '#type' => 'link',
        '#title' => $property,
        '#url' => Url::fromUri('https://schema.org/' . $property),
      ],
    ] : NULL;
  }

  /**
   * Format a group or field name.
   *
   * @param string $name
   *   A group or field name.
   * @param string $type
   *   A group or field type.
   *
   * @return array
   *   The formatted group or field name and type.
   */
  protected function formatNameTableCell(string $name, string $type): array {
    return [
      'data' => [
        '#markup' => preg_replace('/^(group_|field_|schema_)/', '', $name)
          . ($type ? '<br/>[' . $type . ']' : ''),
      ],
    ];
  }

  /**
   * Format field settings.
   *
   * @param array $settings
   *   Field settings.
   *
   * @return string
   *   Formatted field settings.
   */
  protected function formatSettings(array $settings): string {
    $prefix = $settings['prefix'] ?? '';
    $suffix = $settings['suffix'] ?? '';
    if ($prefix && $suffix) {
      return "$prefix/$suffix<br/>";
    }
    elseif ($prefix || $suffix) {
      return "$prefix$suffix<br/>";
    }
    else {
      return '';
    }
  }

  /**
   * Format field options.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $definition
   *   The field definition.
   *
   * @return string
   *   Formatted field options.
   */
  protected function formatFieldOptions(FieldDefinitionInterface $definition): string {
    if ($definition instanceof FieldConfigInterface) {
      $allowed_values = options_allowed_values($definition->getFieldStorageDefinition());
      return $allowed_values
        ? implode('</br>', OptGroup::flattenOptions($allowed_values))
        : '';
    }
    else {
      return '';
    }
  }

}
