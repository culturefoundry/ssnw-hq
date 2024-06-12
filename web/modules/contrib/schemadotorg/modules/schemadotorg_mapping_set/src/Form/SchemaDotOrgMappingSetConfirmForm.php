<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mapping_set\Form;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\schemadotorg_mapping_set\Controller\SchemadotorgMappingSetController;
use Drupal\schemadotorg_mapping_set\SchemaDotOrgMappingSetManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a confirmation form before clearing out the examples.
 */
class SchemaDotOrgMappingSetConfirmForm extends ConfirmFormBase {

  /**
   * The service container.
   */
  protected ContainerInterface $container;

  /**
   * The module handler to invoke the alter hook.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The Schema.org mapping set manager service.
   */
  protected SchemaDotOrgMappingSetManagerInterface $schemaMappingSetManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->container = $container;
    $instance->moduleHandler = $container->get('module_handler');
    $instance->schemaMappingSetManager = $container->get('schemadotorg_mapping_set.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_mapping_set_confirm_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    $t_args = [
      '@action' => $this->getAction(),
      '%name' => $this->getLabel(),
    ];
    return $this->t("Are you sure you want to @action the %name mapping set?", $t_args);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    $t_args = [
      '@action' => $this->getAction(),
      '%name' => $this->getLabel(),
    ];
    return $this->t('Please confirm that you want @action the %name mapping set with the below types.', $t_args);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('schemadotorg_mapping_set.overview');
  }

  /**
   * The mapping set name.
   */
  protected string $name;

  /**
   * The mapping set operation to be performed.
   */
  protected string $operation;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?string $name = NULL, ?string $operation = NULL): array {
    $this->name = $name;
    $this->operation = $operation;

    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\schemadotorg_mapping_set\Controller\SchemadotorgMappingSetController $controller */
    $controller = SchemadotorgMappingSetController::create($this->container);
    $form['description'] = [
      'description' => $form['description'] + ['#prefix' => '<p>', '#suffix' => '</p>'],
      'types' => $controller->buildDetails($this->name, $operation),
    ];

    switch ($this->operation) {
      case 'setup':
        // Add note after the actions element which has a weight of 100.
        $form['note'] = [
          '#weight' => 101,
          '#markup' => $this->t('Please note that setting up multiple entity types and fields may take a minute or two to complete.'),
          '#prefix' => '<div><em>',
          '#suffix' => '</em></div>',
        ];
        break;

      case 'teardown':
        // Display warning about Schema.org types used by other mapping sets.
        if ($this->getRequest()->isMethod('get')) {
          $types = $this->schemaMappingSetManager->getTypes($name);
          $used_types = [];
          foreach ($types as $type) {
            [$entity_type_id, $schema_type] = explode(':', $type);
            $mapping_sets = $this->schemaMappingSetManager->getMappingSets($entity_type_id, $schema_type, TRUE);
            if (count($mapping_sets) > 1) {
              $used_types[] = $type;
            }
          }
          if ($used_types) {
            $t_args = ['%types' => implode('; ', $used_types)];
            $message = $this->t('The below %types types are used by other mapping sets and will not be deleted.', $t_args);
            $this->messenger()->addWarning($message);
          }
        }

        // Check confirmation checkbox.
        $t_args = [
          '@action' => $this->getAction(),
          '%name' => $this->getLabel(),
        ];
        $form['confirm'] = [
          '#type' => 'checkbox',
          '#title' => $this->t("Yes, I want to @action the %name mapping set and all associated content.", $t_args),
          '#description' => '<strong>' . $this->t('This action cannot be undone.') . '</strong>',
          '#required' => TRUE,
        ];
        break;

    }

    if ($form_state->isMethodType('get')
      && in_array($this->operation, ['generate', 'kill'])) {
      $this->messenger()->addWarning($this->t('All existing content will be deleted.'));
    }

    $form['#attributes']['class'][] = 'js-schemadotorg-submit-once';
    $form['#attached'] = ['library' => ['schemadotorg/schemadotorg.form']];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Execute the operation.
    $operation = $this->operation;
    $name = $this->name;
    $this->schemaMappingSetManager->$operation($name);

    // Display a custom message.
    $operations = [];
    $operations['setup'] = $this->t('setup');
    $operations['generate'] = $this->t('generated');
    $operations['kill'] = $this->t('killed');
    $operations['teardown'] = $this->t('torn down');
    $t_args = [
      '@action' => $operations[$this->operation],
      '%name' => $this->getLabel(),
    ];
    $this->messenger()->addStatus($this->t('The %name mapping set has been @action.', $t_args));

    // Redirect to the mapping set manage page.
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * Get the current mapping set's label.
   *
   * @return string
   *   The current mapping set's label.
   */
  protected function getLabel(): string {
    $mapping_sets = $this->config('schemadotorg_mapping_set.settings')->get('sets');
    if (!isset($mapping_sets[$this->name])) {
      throw new NotFoundHttpException();
    }
    return $mapping_sets[$this->name]['label'];
  }

  /**
   * Get the current mapping set's action.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The current mapping set's action.
   */
  protected function getAction(): TranslatableMarkup {
    $is_setup = $this->schemaMappingSetManager->isSetup($this->name);
    $operations = [];
    if (!$is_setup) {
      $operations['setup'] = $this->t('setup');
    }
    else {
      if ($this->moduleHandler->moduleExists('devel_generate')) {
        $operations['generate'] = $this->t('generate');
        $operations['kill'] = $this->t('kill');
      }
      $operations['teardown'] = $this->t('teardown');
    }
    if (!isset($operations[$this->operation])) {
      throw new NotFoundHttpException();
    }
    return $operations[$this->operation];
  }

  /**
   * Get the current mapping set's name.
   *
   * @return string
   *   the current mapping set's name.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the current mapping set's operation.
   *
   * @return string
   *   the current mapping set's operation.
   */
  public function getOperation(): string {
    return $this->operation;
  }

}
