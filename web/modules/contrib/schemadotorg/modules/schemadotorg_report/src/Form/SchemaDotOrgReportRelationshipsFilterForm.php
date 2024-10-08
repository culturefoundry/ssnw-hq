<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Form;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg_report\Traits\SchemaDotOrgReportRelationshipsTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Schema.org reports relationships filter form.
 */
class SchemaDotOrgReportRelationshipsFilterForm extends FormBase {
  use SchemaDotOrgReportRelationshipsTrait;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * The Schema.org type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_report_relationships_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityFieldManager = $container->get('entity_field.manager');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $display = 'targets'): array {
    /** @var \Drupal\node\Entity\NodeType[] $node_types */
    $node_types = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple();

    $categories = [];
    $bundles = [];
    foreach ($node_types as $bundle => $node_type) {
      $mapping = $this->loadMapping('node', $bundle);
      if ($mapping) {
        $bundles[$bundle] = $node_type->label();
        $category = $this->getMappingCategory($mapping);
        $categories[$category['name']] = $category['label'];
      }
    }
    ksort($categories);

    $relationships = $this->getRelationships();

    $query = (!$this->getRequest()->query->get('category') && !$this->getRequest()->query->get('bundle') && !$this->getRequest()->query->get('property'))
      ? $this->getRequest()->query->all()
      : [];

    $form['filter'] = [
      '#type' => 'details',
      '#title' => $this->t('Filter Schema.org relationships'),
      '#open' => (boolean) $query,
    ];

    if ($this->getRequest()->query->get('category')) {
      $categories_default_value = (array) $this->getRequest()->query->get('category');
    }
    else {
      $categories_default_value = ($query)
        ? $query['categories'] ?? []
        : array_keys($categories);
    }
    $form['filter']['categories'] = [
      '#type' => 'select',
      '#title' => $this->t('Categories'),
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#options' => $categories,
      '#default_value' => $categories_default_value,
      '#attributes' => [
        'data-placeholder' => $this->t('Select categories'),
      ],
    ];
    if ($display === 'diagram') {
      $subgraphs_default_value = (isset($query['subgraphs']))
        ? (bool) $query['subgraphs']
        : TRUE;
      $form['filter']['subgraphs'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Display categories in sub graphs.'),
        '#return_value' => TRUE,
        '#default_value' => $subgraphs_default_value,
      ];
    }

    if ($this->getRequest()->query->get('bundle')) {
      $bundles_default_value = (array) $this->getRequest()->query->get('bundle');
    }
    else {
      $bundles_default_value = ($query)
        ? $query['bundles'] ?? []
        : array_keys($bundles);
    }
    $form['filter']['bundles'] = [
      '#type' => 'select',
      '#title' => $this->t('Content types'),
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#options' => $bundles,
      '#default_value' => $bundles_default_value,
      '#attributes' => [
        'data-placeholder' => $this->t('Select content types'),
      ],
    ];

    $form['filter']['divider'] = ['#markup' => '<hr/>'];

    $relationship_types = $this->getRelationshipTypes();

    // Limit diagram relationship types to only hierarchy and relationships.
    if ($display === 'diagram') {
      $relationships = array_intersect_key(
        $relationships,
        array_flip(['hierarchical', 'reference'])
      );
    }

    foreach ($relationships as $relationship_type => $relationship_options) {
      $relationship_type_label = $relationship_types[$relationship_type];
      $relationship_options = array_combine($relationship_options, $relationship_options);
      ksort($relationship_options);

      if ($this->getRequest()->query->get('property')) {
        $relationship_default_value = (array) $this->getRequest()->query->get('property');
      }
      elseif ($query) {
        $relationship_default_value = $query[$relationship_type] ?? [];
      }
      else {
        $relationship_default_value = $relationship_options;
        // If there is no query (a.k.a, filters), exclude some Schema.org
        // properties from the relationship default values for the diagram.
        if ($display === 'diagram') {
          $relationship_default_value = array_diff(
            $relationship_default_value,
            $this->getDiagramExcludedSchemaProperties()
          );
        }
      }

      $form['filter'][$relationship_type] = [
        '#type' => 'select',
        '#title' => $relationship_type_label['plural'],
        '#multiple' => TRUE,
        '#options' => $relationship_options,
        '#default_value' => $relationship_default_value,
        '#attributes' => [
          'data-placeholder' => $this->t('Select @label', ['@label' => $relationship_type_label['plural']]),
        ],
      ];
    }
    $form['filter']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Filter'),
    ];
    if (!empty($this->getRequest()->query->all())) {
      $form['filter']['reset'] = [
        '#type' => 'submit',
        '#submit' => ['::resetForm'],
        '#value' => $this->t('Reset'),
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $query = $form_state->cleanValues()->getValues();
    foreach ($query as $key => $value) {
      if (is_array($value)) {
        $query[$key] = array_values($value);
      }
    }

    $form_state->setRedirect(
      $this->getRouteMatch()->getRouteName(),
      $this->getRouteMatch()->getRawParameters()->all(),
      ['query' => $query]
    );
  }

  /**
   * Resets the filter selection.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function resetForm(array &$form, FormStateInterface $form_state): void {
    $form_state->setRedirect(
      $this->getRouteMatch()->getRouteName(),
      $this->getRouteMatch()->getRawParameters()->all()
    );
  }

}
