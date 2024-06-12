<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgBuildTrait;
use Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a confirmation form before clearing out the examples.
 */
class SchemaDotOrgStarterkitConfirmForm extends ConfirmFormBase {
  use SchemaDotOrgBuildTrait;

  /**
   * The module list service.
   */
  protected ModuleExtensionList $moduleList;

  /**
   * The module handler to invoke the alter hook.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

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
   * The Schema.org starter kit manager service.
   */
  protected SchemaDotOrgStarterkitManagerInterface $schemaStarterkitManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->moduleList = $container->get('extension.list.module');
    $instance->moduleHandler = $container->get('module_handler');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    $instance->schemaStarterkitManager = $container->get('schemadotorg_starterkit.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_starterkit_confirm_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    $t_args = [
      '@action' => $this->getAction(),
      '%name' => $this->getLabel(),
    ];
    return $this->t("Are you sure you want to @action the %name starter kit?", $t_args);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    $t_args = [
      '@action' => $this->getAction(),
      '%name' => $this->getLabel(),
    ];
    return $this->t('Please confirm that you want @action the %name starter kit.', $t_args);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('schemadotorg_starterkit.overview');
  }

  /**
   * The starter kit name.
   */
  protected string $name;

  /**
   * The starter kit operation to be performed.
   */
  protected string $operation;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?string $name = NULL, ?string $operation = NULL): array {
    if (!$this->schemaStarterkitManager->isStarterkit($name)) {
      throw new NotFoundHttpException();
    }

    $this->name = $name;
    $this->operation = $operation;

    $settings = $this->schemaStarterkitManager->getStarterkitSettings($this->name);

    // Check dependencies.
    $module_data = $this->moduleList->getList();
    $missing_dependencies = [];
    foreach ($settings['dependencies'] as $dependency) {
      if (!isset($module_data[$dependency])) {
        $missing_dependencies[] = $dependency;
      }
    };
    if ($missing_dependencies) {
      $starterkit = $this->schemaStarterkitManager->getStarterkit($this->name);
      $t_args = [
        '%name' => $starterkit['name'],
        '%starterkits' => implode(', ', $missing_dependencies),
      ];
      $message = $this->t('Unable to install %name due to missing starter kits %starterkits.', $t_args);
      $this->messenger()->addWarning($message);
      $form['#title'] = $this->getQuestion();
      return $form;
    }

    $form = parent::buildForm($form, $form_state);

    $form['description'] = [
      'description' => $form['description'] + ['#prefix' => '<p>', '#suffix' => '</p>'],
      'types' => $this->buildSchemaTypes(),
    ];

    switch ($this->operation) {
      case 'install':
        // Add note after the actions element which has a weight of 100.
        $form['note'] = [
          '#weight' => 101,
          '#markup' => $this->t('Please note that the installation and setting up of multiple entity types and fields may take a minute or two to complete.'),
          '#prefix' => '<div><em>',
          '#suffix' => '</em></div>',
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
    $operation = $this->operation;
    $name = $this->name;

    $operations = [];
    $operations['install'] = $this->t('installed');
    $operations['update'] = $this->t('updated');
    $operations['generate'] = $this->t('generated');
    $operations['kill'] = $this->t('killed');

    try {
      $this->schemaStarterkitManager->$operation($name);

      // Display a custom message.
      $t_args = [
        '@action' => $operations[$this->operation],
        '%name' => $this->getLabel(),
      ];
      $this->messenger()->addStatus($this->t('The %name starter kit has been @action.', $t_args));
    }
    catch (\Exception $exception) {
      // Display a custom message.
      $t_args = [
        '@action' => $operations[$this->operation],
        '%name' => $this->getLabel(),
      ];
      $this->messenger()->addStatus($this->t('The %name starter kit has failed to be @action.', $t_args));
      $this->messenger->addError($exception->getMessage());
    }

    // Redirect to the starter kit manage page.
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * Get the current starter kit's label.
   *
   * @return string
   *   The current starter kit's label.
   */
  protected function getLabel(): string {
    $starterkit = $this->schemaStarterkitManager->getStarterkit($this->name);
    if (!$starterkit) {
      throw new NotFoundHttpException();
    }
    return $starterkit['name'];
  }

  /**
   * Get the current starter kit's action.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The current starter kit's action.
   */
  protected function getAction(): TranslatableMarkup {
    $is_installed = $this->moduleHandler->moduleExists($this->name);
    $operations = [];
    if (!$is_installed) {
      if ($this->currentUser()->hasPermission('administer modules')) {
        $operations['install'] = $this->t('install');
      }
    }
    else {
      $operations['update'] = $this->t('update');
      if ($this->moduleHandler->moduleExists('devel_generate')) {
        $operations['generate'] = $this->t('generate');
        $operations['kill'] = $this->t('kill');
      }
    }
    if (!isset($operations[$this->operation])) {
      throw new NotFoundHttpException();
    }
    return $operations[$this->operation];
  }

  /**
   * Get the current starter kit's name.
   *
   * @return string
   *   the current starter kit's name.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the current starter kit's operation.
   *
   * @return string
   *   the current starter kit's operation.
   */
  public function getOperation(): string {
    return $this->operation;
  }

  /**
   * Get starter kit's and dependencies Schema.org types.
   *
   * @param string $name
   *   Starter kit name.
   *
   * @return array
   *   Starter kit Schema.org types.
   */
  public function getSchemaTypes(string $name): array {
    $settings = $this->schemaStarterkitManager->getStarterkitSettings($name);
    $types = $settings['types'];
    if (isset($settings['dependencies'])) {
      foreach ($settings['dependencies'] as $dependency) {
        $types = NestedArray::mergeDeep($settings['types'], $this->getSchemaTypes($dependency));
      }
    }
    return $types;
  }

  /**
   * Build Schema.org types details.
   *
   * @return array
   *   A renderable array containing Schema.org types details.
   */
  protected function buildSchemaTypes(): array {
    $build = [];
    $types = $this->getSchemaTypes($this->name);
    foreach ($types as $type => $mapping_defaults) {
      [$entity_type_id, , $schema_type] = $this->getMappingStorage()->parseType($type);
      // Reload the mapping default without any alterations.
      if (!in_array($this->operation, ['install', 'update'])) {
        $mapping_defaults = $this->schemaMappingManager->getMappingDefaults($entity_type_id, $mapping_defaults['entity']['id'], $schema_type);
      }

      $details = $this->buildSchemaType($type, $mapping_defaults);
      switch ($this->operation) {
        case 'install':
        case 'update':
          $mapping = $this->getMappingStorage()->loadByType($type);
          $details['#title'] .= ' - ' . ($mapping ? $this->t('Exists') : '<em>' . $this->t('Missing') . '</em>');
          $details['#summary_attributes']['class'] = [($mapping) ? 'color-success' : 'color-warning'];
          break;
      }
      $build[$type] = $details;
    }
    return $build;
  }

}
