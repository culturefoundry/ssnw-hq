<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_recipe\Form;

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
use Drupal\schemadotorg_recipe\SchemaDotOrgRecipeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Process;

/**
 * Provides a confirmation form before clearing out the examples.
 */
class SchemaDotOrgRecipeConfirmForm extends ConfirmFormBase {
  use SchemaDotOrgBuildTrait;

  /**
   * The module list service.
   */
  protected ModuleExtensionList $moduleExtensionList;

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
   * The Schema.org recipe manager service.
   */
  protected SchemaDotOrgRecipeManagerInterface $schemaRecipeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->moduleExtensionList = $container->get('extension.list.module');
    $instance->moduleHandler = $container->get('module_handler');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    $instance->schemaRecipeManager = $container->get('schemadotorg_recipe.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_recipe_confirm_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    $t_args = [
      '@action' => $this->getAction(),
      '%name' => $this->getLabel(),
    ];
    return $this->t("Are you sure you want to @action the %name recipe?", $t_args);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    $t_args = [
      '@action' => $this->getAction(),
      '%name' => $this->getLabel(),
    ];
    return $this->t('Please confirm that you want @action the %name recipe.', $t_args);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('schemadotorg_recipe.overview');
  }

  /**
   * The recipe name.
   */
  protected string $name;

  /**
   * The recipe operation to be performed.
   */
  protected string $operation;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?string $name = NULL, ?string $operation = NULL): array {
    if (!$this->schemaRecipeManager->isRecipe($name)) {
      throw new NotFoundHttpException();
    }

    $this->name = $name;
    $this->operation = $operation;

    $settings = $this->schemaRecipeManager->getRecipeSettings($this->name);

    // Check dependencies.
    $module_data = $this->moduleExtensionList->getList();
    $missing_dependencies = [];
    foreach ($settings['install'] as $dependency) {
      if (!isset($module_data[$dependency])) {
        $missing_dependencies[] = $dependency;
      }
    };
    if ($missing_dependencies) {
      $recipe = $this->schemaRecipeManager->getRecipe($this->name);
      $t_args = [
        '%name' => $recipe['name'],
        '%recipes' => implode(', ', $missing_dependencies),
      ];
      $message = $this->t('Unable to install %name due to missing recipes %recipes.', $t_args);
      $this->messenger()->addWarning($message);
      $form['#title'] = $this->getQuestion();
      return $form;
    }

    $form = parent::buildForm($form, $form_state);

    $form['description'] = [
      'description' => $form['description'] + ['#weight' => -100, '#prefix' => '<p>', '#suffix' => '</p>'],
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

      case 'update':
        // Add note after the actions element which has a weight of 100.
        $form['description']['note'] = [
          '#weight' => -99,
          '#markup' => $this->t('Updating this recipe will add missing entity types/fields and/or update existing entity types/field.'),
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
    $operations['apply'] = $this->t('applied');
    $operations['reapply'] = $this->t('reapplied');
    $operations['generate'] = $this->t('generated');
    $operations['kill'] = $this->t('killed');

    try {
      $t_args = [
        '@action' => $operations[$this->operation],
        '%name' => $this->getLabel(),
      ];

      $operation_method = ($operation === 'reapply')
        ? 'apply'
        : $operation;
      $result = $this->schemaRecipeManager->$operation_method($name);

      if ($result instanceof Process) {
        if ($result->getExitCode()) {
          $this->messenger()->addError($this->t('The %name recipe has NOT been @action.', $t_args));
          $error = $result->getErrorOutput();
          if ($error) {
            $this->messenger()->addError($error);
          }
        }
        else {
          $this->messenger()->addStatus($this->t('The %name recipe has been @action.', $t_args));
          $output = $result->getOutput();
          if ($output) {
            $this->messenger()->addStatus($output);
          }
        }
      }
      else {
        // Display a custom message.
        $this->messenger()->addStatus($this->t('The %name recipe has been @action.', $t_args));
      }

    }
    catch (\Exception $exception) {
      // Display a custom message.
      $t_args = [
        '@action' => $operations[$this->operation],
        '%name' => $this->getLabel(),
      ];
      $this->messenger()->addStatus($this->t('The %name recipe has failed to be @action.', $t_args));
      $this->messenger->addError($exception->getMessage());
    }

    // Redirect to the recipe manage page.
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * Get the current recipe's label.
   *
   * @return string
   *   The current recipe's label.
   */
  protected function getLabel(): string {
    $recipe = $this->schemaRecipeManager->getRecipe($this->name);
    if (!$recipe) {
      throw new NotFoundHttpException();
    }
    return $recipe['name'];
  }

  /**
   * Get the current recipe's action.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The current recipe's action.
   */
  protected function getAction(): TranslatableMarkup {
    $settings = $this->schemaRecipeManager->getRecipeSettings($this->name);
    $is_applied = $settings['schemadotorg']['applied'];
    $operations = [];
    if (!$is_applied) {
      if ($this->currentUser()->hasPermission('administer modules')) {
        $operations['apply'] = $this->t('apply');
      }
    }
    else {
      $operations['reapply'] = $this->t('reapply');
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
   * Get the current recipe's name.
   *
   * @return string
   *   the current recipe's name.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the current recipe's operation.
   *
   * @return string
   *   the current recipe's operation.
   */
  public function getOperation(): string {
    return $this->operation;
  }

  /**
   * Get recipe's and dependencies Schema.org types.
   *
   * @param string $name
   *   Recipe name.
   *
   * @return array
   *   Recipe Schema.org types.
   */
  public function getSchemaTypes(string $name): array {
    $settings = $this->schemaRecipeManager->getRecipeSettings($name);
    $types = $settings['schemadotorg']['types'];
    // phpcs:disable
    // @todo get types from other recipes.
    //   if (isset($settings['dependencies'])) {
    //      foreach ($settings['dependencies'] as $dependency) {
    //        $types = NestedArray::mergeDeep($settings['types'], $this->getSchemaTypes($dependency));
    //      }
    //    }
    // phpcs:enable
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
      if (!in_array($this->operation, ['apply', 'reapply'])) {
        $mapping_defaults = $this->schemaMappingManager->getMappingDefaults($entity_type_id, $mapping_defaults['entity']['id'], $schema_type);
      }

      $details = $this->buildSchemaType($type, $mapping_defaults);
      switch ($this->operation) {
        case 'apply':
        case 'reapply':
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
