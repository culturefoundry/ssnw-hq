<?php

namespace Drupal\content_model_documentation\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SortArray;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Search fields form.
 */
class SearchFieldsForm extends FormBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Our custom service.
   *
   * @var \Drupal\content_model_documentation\BetterFieldsReportInterface
   */
  protected $betterFieldsReport;

  /**
   * List of field definitions.
   *
   * @var array
   */
  protected $fieldDefinitions;

  /**
   * List of filters types, keys and values.
   *
   * @var array
   */
  protected $filters;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->requestStack = $container->get('request_stack');
    $instance->moduleHandler = $container->get('module_handler');
    $instance->betterFieldsReport = $container->get('content_model_documentation.fields_report');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_fields_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $wrapper_id = Html::getUniqueId('search-fields-form-container');
    $form['#prefix'] = '<div id="' . $wrapper_id . '">';
    $form['#suffix'] = '</div>';

    $query = ['destination' => Url::fromRoute('<current>')->toString()];

    $input = array_filter($form_state->getUserInput() ?? []);
    unset(
      $input['form_build_id'],
      $input['form_token'],
      $input['form_id'],
      $input['_triggering_element_name'],
      $input['_triggering_element_value'],
      $input['_drupal_ajax'],
      $input['ajax_page_state'],
    );

    if (!isset($this->fieldDefinitions)) {
      $this->fieldDefinitions = $this->betterFieldsReport->getFieldDefinitions();
    }

    $results = [];
    foreach ($this->fieldDefinitions as $entity_type_id => $fields) {
      $table = [
        '#theme' => 'table',
        '#header' => [
          $this->t('ID'),
          $this->t('Name'),
          $this->t('Type'),
          $this->t('Actions'),
        ],
        '#rows' => [],
        '#empty' => $this->t('No fields found'),
      ];

      foreach ($fields as $name => $definition) {
        // Process filters for later use.
        $values = $definition->toArray();

        // Apply filters.
        $skip = FALSE;
        foreach ($input as $key => $value) {
          if (empty($value)) {
            continue;
          }

          if (!isset($values[$key])) {
            $skip = TRUE;
            continue;
          }

          $skip = (string) $values[$key] !== (string) $value;
        }

        if ($skip) {
          continue;
        }

        // Prepopulate filter options lists.
        $this->getFilterInfoFromDefinition($values);

        // Build action links.
        $links['open'] = [
          'title' => $this->t('Details'),
          'url' => Url::fromRoute('content_model_documentation.fields.details', [
            'entity_type' => $entity_type_id,
            'field' => $name,
          ], [
            'query' => $query,
          ]),
          'attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'dialog',
            'data-dialog-renderer' => 'off_canvas',
            'data-dialog-options' => Json::encode([
              'width' => 600,
            ]),
          ],
        ];

        $actions = [
          '#type' => 'dropbutton',
          '#links' => $links,
        ];

        $table['#rows'][] = [
          $definition->getName(),
          $definition->getLabel(),
          $definition->getType(),
          ['data' => $actions],
        ];
      }

      $empty = empty($table['#rows'] ?? []);

      $result = [
        '#type' => 'details',
        '#title' => $entity_type_id,
        '#open' => !$empty,
        'fields' => $table,
      ];

      if (!$empty) {
        $results[$entity_type_id] = $result;
      }
    }

    // Tables.
    $form['results'] = [
      '#type' => 'container',
      '#weight' => 9,
      'items' => $results,
    ];

    // Filters.
    $form['filters'] = [
      '#type' => 'details',
      '#title' => $this->t('Filters'),
      '#open' => TRUE,
    ];

    if (isset($this->filters['options'])) {
      foreach (array_keys($this->filters['options']) as $key) {
        $form['filters'][$key] = $this->createFilter($key);
      }
      uasort($form['filters'], [$this, 'sortByType']);
    }

    $form['filters']['actions'] = ['#type' => 'actions'];

    $form['filters']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#ajax' => [
        'wrapper' => $wrapper_id,
        'callback' => [$this, 'ajaxRefresh'],
      ],
    ];

    $form['#attached']['library'][] = 'core/drupal.dialog.off_canvas';
    $form['#attached']['library'][] = 'content_model_documentation/search_fields_form';

    return $form;
  }

  /**
   * Callback for party checkbox.
   */
  public function ajaxRefresh(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Prepare information from field definition for later use.
   *
   * @param array $values
   *   A given list of value from a field definition.
   */
  public function getFilterInfoFromDefinition(array $values) {
    foreach (array_keys($values) as $key) {
      // Keep record of all values to build options.
      $option = $values[$key];
      if (!isset($this->filters['options'][$key])) {
        $this->filters['options'][$key] = [];
      }
      if (FALSE === array_search($option, $this->filters['options'][$key])) {
        $this->filters['options'][$key][] = $option;
      }

      // Determine type of the settings values.
      if (!isset($this->filters['types'][$key])) {
        $type = gettype($option);
        if ($option instanceof TranslatableMarkup) {
          $type = 'string';
        }
        $this->filters['types'][$key] = $type;
      }
    }
  }

  /**
   * Generate a renderable form element for a given field definition key.
   *
   * @return array
   *   The form element as a render array.
   */
  public function createFilter(string $key) {
    $type = $this->filters['types'][$key] ?? 'string';
    $options = $this->filters['options'][$key] ?? [];

    switch ($type) {
      case 'boolean':
        $type = 'checkbox';
        break;

      case 'integer':
      case 'string':
        $type = 'select';
        break;
    }

    switch ($key) {
      case 'description':
        $type = 'textfield';
        break;
    }

    $element = [
      '#type' => $type,
      '#title' => $key,
    ];

    if ($type == 'select') {
      foreach ($options as &$option) {
        if ($option instanceof TranslatableMarkup) {
          $option = (string) $option;
        }
      }

      $options = array_combine($options, $options);
      $options = array_unique($options);
      $options = array_filter($options);
      asort($options);

      $element += [
        '#empty_option' => $this->t('- Select -'),
        '#options' => $options,
        // '#multiple' => TRUE,
      ];
    }

    return $element;
  }

  /**
   * Sorts a structured array by '#type' property.
   *
   * Callback for uasort().
   *
   * @param array $a
   *   First item for comparison. The compared items should be associative
   *   arrays that optionally include a '#title' key.
   * @param array $b
   *   Second item for comparison.
   *
   * @return int
   *   The comparison result for uasort().
   */
  public static function sortByType($a, $b) {
    return SortArray::sortByKeyString($a, $b, '#type');
  }

}
