<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Schema.org reports filter form.
 */
class SchemaDotOrgReportFilterForm extends FormBase {

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * Schema.org table.
   */
  protected ?string $table;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_report_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?string $table = NULL, ?string $id = NULL): array {
    $this->table = $table;

    $t_args = [
      '@label' => ($table === SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES)
        ? $this->t('type')
        : $this->t('property'),
    ];
    $form['filter'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['container-inline']],
    ];
    $form['filter']['id'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Find a @label', $t_args),
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('Find a Schema.org @labels', $t_args),
      '#size' => 30,
      '#novalidate' => TRUE,
      '#target_type' => $table,
      '#action' => Url::fromRoute('schemadotorg_report')->toString() . '/',
      '#default_value' => $id,
    ];
    $form['filter']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Find'),
    ];
    if (!empty($id)) {
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
    $id = $form_state->getValue('id');
    if ($id && $this->schemaTypeManager->isId($this->table, $id)) {
      $form_state->setRedirect('schemadotorg_report', ['id' => $id]);
    }
    else {
      $route_name = $this->getRouteMatch()->getRouteName();
      $redirect_route_name = 'schemadotorg_report.' . $this->table;
      if ($route_name && str_contains($route_name, $redirect_route_name)) {
        $redirect_route_name = $route_name;
      }
      $form_state->setRedirect($redirect_route_name, [], ['query' => ['id' => $id]]);
    }
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
    $form_state->setRedirect($this->getRouteMatch()->getRouteName(), $this->getRouteMatch()->getRawParameters()->all());
  }

}
