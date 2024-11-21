<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_recipe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgBuildTrait;
use Drupal\schemadotorg_recipe\SchemaDotOrgRecipeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Schema.org Blueprints Recipe routes.
 */
class SchemadotorgRecipeController extends ControllerBase {
  use SchemaDotOrgBuildTrait;

  /**
   * The app root.
   */
  protected string $root;

  /**
   * The module list service.
   */
  protected ModuleExtensionList $moduleExtensionList;

  /**
   * The Schema.org names manager.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org schema type builder.
   */
  protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder;

  /**
   * The Schema.org mapping manager service.
   */
  protected SchemaDotOrgMappingManagerInterface $schemaMappingManager;

  /**
   * The Schema.org recipe manager service.
   */
  protected SchemaDotOrgRecipeManagerInterface $schemaRecipeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->root = $container->getParameter('app.root');
    $instance->moduleExtensionList = $container->get('extension.list.module');
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    $instance->schemaRecipeManager = $container->get('schemadotorg_recipe.manager');
    return $instance;
  }

  /**
   * Builds the response for the recipes overview page.
   */
  public function overview(): array {
    // Header.
    $header = [
      'title' => ['data' => $this->t('Title / Description'), 'width' => '30%'],
      'applicable' => ['data' => $this->t('Applicable'), 'width' => '5%'],
      'applied' => ['data' => $this->t('Applied'), 'width' => '5%'],
      'types' => ['data' => $this->t('Types'), 'width' => '25%'],
      'dependencies' => ['data' => $this->t('Dependencies'), 'width' => '30%'],
      'operations' => ['data' => $this->t('Operations'), 'width' => '10%'],
    ];

    $module_data = $this->moduleExtensionList->getList();

    // Rows.
    $rows = [];
    $recipes = $this->schemaRecipeManager->getRecipes();
    foreach ($recipes as $recipe_name => $recipe_settings) {
      $types = [];
      if (!empty($recipe_settings['schemadotorg']['types'])) {
        foreach ($recipe_settings['schemadotorg']['types'] as $type => $type_settings) {
          $mapping = $this->getMappingStorage()->loadByType($type);
          if ($mapping) {
            if ($mapping->getTargetEntityBundleEntity()) {
              $types[$type] = $mapping->getTargetEntityBundleEntity()
                ->toLink($type, 'edit-form')->toString();
            }
            elseif ($mapping->getTargetEntityTypeId() === 'user') {
              $types[$type] = Link::createFromRoute($type, 'entity.user.admin_form')->toString();
            }
            else {
              $types[$type] = $type;
            }
          }
          else {
            $types[$type] = $type;
          }
        }
      }

      // Dependencies.
      $dependencies = [];
      foreach ($recipe_settings['install'] as $dependency) {
        if (isset($module_data[$dependency])) {
          $dependency_name = $module_data[$dependency]->info['name'];
          $dependency_name = str_replace('Schema.org Blueprints Recipe: ', '', $dependency_name);
          $dependencies[] = $dependency_name;
        }
        else {
          $dependencies[] = ['#markup' => $dependency . ' <em>(' . $this->t('Missing') . ')</em>'];
        }
      };

      $title = $recipe_settings['name'];
      $title = str_replace('Schema.org Blueprints Recipe: ', '', $title);
      $title = str_replace('Schema.org Blueprints ', '', $title);

      $view_url = Url::fromRoute('schemadotorg_recipe.details', ['name' => $recipe_name]);

      $row = [];

      $row['title'] = [
        'data' => [
          'link' => [
            '#type' => 'link',
            '#title' => $title,
            '#url' => $view_url,
          ],
          'description' => [
            '#prefix' => '<br/>',
            '#markup' => $recipe_settings['description'] ?? '',
          ],
        ],
      ];

      $row['applicable'] = ($recipe_settings['schemadotorg']['applicable'])
        ? $this->t('Yes')
        : $this->t('No');
      $row['applied'] = ($recipe_settings['schemadotorg']['applied'])
        ? $this->t('Yes')
        : $this->t('No');

      $row['types'] = [
        'data' => [
          '#theme' => 'item_list',
          '#items' => $types,
        ],
      ];

      $row['dependencies'] = [
        'data' => [
          '#theme' => 'item_list',
          '#items' => $dependencies,
        ],
      ];

      $operations = $this->getOperations($recipe_name);
      $row['operations'] = ($operations) ? [
        'data' => [
          '#type' => 'operations',
          '#links' => $operations,
        ],
        'style' => 'white-space: nowrap',
      ] : [];
      $rows[] = ($recipe_settings['schemadotorg']['applied'])
        ? ['data' => $row, 'class' => ['color-success']]
        : $row;
    }

    return [
      'table' => [
        '#type' => 'table',
        '#sticky' => TRUE,
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No recipes found.'),
      ],
    ];
  }

  /**
   * Builds the response for the recipe detail page.
   */
  public function details(string $name): array {
    if (!$this->schemaRecipeManager->isRecipe($name)) {
      throw new NotFoundHttpException();
    }

    $recipe = $this->schemaRecipeManager->getRecipe($name);

    $build = [];
    $build['#title'] = $recipe['name'];
    $build['description'] = [
      '#markup' => $recipe['description'],
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];
    $build['summary'] = $this->buildSummary($name);
    $build['details'] = $this->buildDetails($name);
    $build['dependencies'] = $this->buildDependencies($name);
    $build['recipe'] = $this->buildRecipe($name);
    return $build;
  }

  /**
   * Build a recipe's summary.
   *
   * @param string $name
   *   The recipe's name.
   *
   * @return array
   *   A renderable array containing a recipe's summary.
   */
  public function buildDependencies(string $name): array {
    $recipe = $this->schemaRecipeManager->getRecipe($name);
    $module_list_info = $this->moduleExtensionList->getAllAvailableInfo();

    $rows = [];
    foreach ($recipe['install'] as $dependency) {
      $module_info = $module_list_info[$dependency]
        ?? ['name' => $dependency, 'description' => ''];
      $rows[] = [
        'name' => [
          'data' => [
            'name' => [
              '#markup' => $module_info['name'],
              '#prefix' => '<strong>',
              '#suffix' => '</strong><br/>',
            ],
            'description' => ['#markup' => $module_info['description']],
          ],
        ],
        'installed' => $this->moduleHandler()->moduleExists($dependency)
          ? $this->t('Yes')
          : $this->t('No'),
      ];
    }

    $header = [
      'name' => ['data' => $this->t('Dependency name / Description'), 'width' => '80%'],
      'installed' => ['data' => $this->t('Installed'), 'width' => '20%'],
    ];

    return $rows ? [
      '#type' => 'details',
      '#title' => $this->t('Dependencies'),
      'modules' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ],
    ] : [];
  }

  /**
   * Build a recipe's summary.
   *
   * @param string $name
   *   The recipe's name.
   *
   * @return array
   *   A renderable array containing a recipe's summary.
   */
  public function buildSummary(string $name): array {
    $rows = [];
    $recipe = $this->schemaRecipeManager->getRecipeSettings($name);
    foreach ($recipe['schemadotorg']['types'] as $type => $mapping_defaults) {
      [$entity_type_id, $bundle, $schema_type] = $this->getMappingStorage()->parseType($type);
      $mapping = $this->getMappingStorage()->loadByType($type);

      $row = [];
      $row['schema_type'] = $schema_type;
      if (!empty($mapping_defaults['additional_mappings'])) {
        $row['schema_type'] .= ' (' . implode(', ', array_keys($mapping_defaults['additional_mappings'])) . ')';
      }
      $row['entity_type'] = [
        'data' => [
          'label' => [
            '#markup' => $mapping_defaults['entity']['label'],
            '#prefix' => '<strong>',
            '#suffix' => '</strong> (' . $entity_type_id . ':' . $bundle . ')<br/>',
          ],
          'comment' => [
            '#markup' => $mapping_defaults['entity']['description'],
          ],
        ],
      ];
      $row['status'] = [
        'data' => ($mapping)
          ? $this->t('Exists') :
          [
            '#markup' => $this->t('Missing'),
            '#prefix' => '<em>',
            '#suffix' => '</em>',
          ],
      ];

      $rows[] = [
        'data' => $row,
        'class' => [
          ($mapping) ? 'color-success' : 'color-warning',
        ],
      ];
    }

    // Append operations as the last row in the table.
    if ($rows) {
      $rows[] = [
        ['colspan' => 2],
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => $this->getOperations($name, ['query' => $this->getRedirectDestination()->getAsArray()]),
          ],
          'style' => 'white-space: nowrap',
        ],
      ];
    }

    $header = [
      'schema_type' => ['data' => $this->t('Schema.org type(s)'), 'width' => '15%'],
      'entity_type' => ['data' => $this->t('Entity label (type) / description'), 'width' => '70%'],
      'status' => ['data' => $this->t('Status'), 'width' => '15%'],
    ];

    return $rows ? [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ] : [];
  }

  /**
   * Build a recipe's details.
   *
   * @param string $name
   *   The recipe's name.
   *
   * @return array
   *   A renderable array containing a recipe's details.
   */
  public function buildDetails(string $name): array {
    $build = [];
    $recipe = $this->schemaRecipeManager->getRecipeSettings($name);
    foreach ($recipe['schemadotorg']['types'] as $type => $defaults) {
      $mapping = $this->getMappingStorage()->loadByType($type);

      $details = $this->buildSchemaType($type, $defaults);
      $details['#title'] .= ' - ' . ($mapping ? $this->t('Exists') : '<em>' . $this->t('Missing') . '</em>');
      $details['#summary_attributes']['class'] = [($mapping) ? 'color-success' : 'color-warning'];
      $build[$type] = $details;
    }
    return $build;
  }

  /**
   * Build a recipe's YAML.
   *
   * @param string $name
   *   The recipe's name.
   *
   * @return array
   *   A renderable array containing a recipe's YAML.
   */
  public function buildRecipe(string $name): array {
    $recipe = $this->schemaRecipeManager->getRecipe($name);
    $path = $recipe['schemadotorg']['path'];
    $yaml = file_get_contents($path);
    $t_args = [
      '@path' => str_replace($this->root, '', $path),
    ];
    return [
      '#type' => 'details',
      '#title' => $this->t('Configuration'),
      '#description' => $this->t('Below is the configuration in @path.', $t_args),
      'code' => [
        '#type' => 'html_tag',
        '#tag' => 'pre',
        '#plain_text' => $yaml,
        '#attributes' => ['data-schemadotorg-codemirror-mode' => 'text/x-yaml'],
        '#attached' => ['library' => ['schemadotorg/codemirror.yaml']],
      ],
    ];
  }

  /**
   * Get a recipe's operations based on its status.
   *
   * @param string $name
   *   The name of the recipe.
   * @param array $options
   *   An array of route options.
   *
   * @return array
   *   A recipe's operations based on its status.
   */
  protected function getOperations(string $name, array $options = []): array {
    $recipe = $this->schemaRecipeManager->getRecipe($name);

    $operations = [];
    if (!$recipe['schemadotorg']['applied']) {
      if ($this->currentUser()->hasPermission('administer modules')) {
        $operations['apply'] = $this->t('Apply recipe');
      }
    }
    else {
      if ($this->moduleHandler()->moduleExists('devel_generate')) {
        if (!empty($recipe['schemadotorg']['types'])) {
          $operations['generate'] = $this->t('Generate content');
          $operations['kill'] = $this->t('Kill content');
        }
      }
      $operations['reapply'] = $this->t('Reapply recipe');
    }
    foreach ($operations as $operation => $title) {
      $operations[$operation] = [
        'title' => $title,
        'url' => Url::fromRoute(
          'schemadotorg_recipe.confirm_form',
          ['name' => $name, 'operation' => $operation],
          $options
        ),
      ];
    }
    return $operations;
  }

}
