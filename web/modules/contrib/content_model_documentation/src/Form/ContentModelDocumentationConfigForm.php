<?php

namespace Drupal\content_model_documentation\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the module's config/settings admin page.
 */
class ContentModelDocumentationConfigForm extends ConfigFormBase {

  /**
   * Module handler interface.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private ModuleHandlerInterface $moduleHandler;

  /**
   * ContentModelDocumentationConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler for determining which modules are installed.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $moduleHandler) {
    parent::__construct($config_factory);
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'content_model_documentation.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'cm_document_config_form';
  }

  /**
   * Generates an array of entity types and their enabled state.
   *
   * @return array
   *   An array whose elements in the form of 'entity name' => enabled status.
   */
  protected function getEntityTypes() {
    $documentable_entities = [];
    $documentable_entities['block'] = $this->moduleHandler->moduleExists('block_content');
    $documentable_entities['field'] = $this->moduleHandler->moduleExists('field');
    $documentable_entities['media'] = $this->moduleHandler->moduleExists('media');
    $documentable_entities['menu'] = $this->moduleHandler->moduleExists('menu_link_content');
    $documentable_entities['modules'] = TRUE;
    $documentable_entities['node'] = $this->moduleHandler->moduleExists('node');
    $documentable_entities['paragraph'] = $this->moduleHandler->moduleExists('paragraphs');
    $documentable_entities['taxonomy'] = $this->moduleHandler->moduleExists('taxonomy');
    return $documentable_entities;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('content_model_documentation.settings');
    $documentable_entities = $this->getEntityTypes();
    foreach ($documentable_entities as $entity_name => $enabled) {
      $form[$entity_name] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Make @entity_name entity documentable.', ['@entity_name' => $entity_name]),
        '#description' => $this->t('This will allow @entity_name to be referenced by Document entities.', ['@entity_name' => $entity_name]),
        '#default_value' => $config->get($entity_name),
        '#access' => TRUE,
      ];
      if (!$enabled) {
        $form[$entity_name]['#disabled'] = TRUE;
        $form[$entity_name]['#description'] = $this->t('The module for @entity_name is not enabled so can not be documented.', ['@entity_name' => $entity_name]);
      }
    }

    $export_location = $config->get('export_location');
    if (empty($export_location)) {
      $description = $this->t('Enter the machine name of the local module where you would like Content Model documents to be exported.');
    }
    elseif ($this->moduleHandler->moduleExists($export_location)) {
      $description = $this->t('The module "@module_name" will contain any exported files at @module_name/cm_documents', ['@module_name' => $export_location]);
    }
    else {
      $description = $this->t('The module "@module_name" could not be found.  Make sure it is enabled.', ['@module_name' => $export_location]);
    }

    $form['export_location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine name of local module to house export/import files'),
      '#description' => $description,
      '#default_value' => $config->get('export_location'),
      '#access' => TRUE,

    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $config = $this->config('content_model_documentation.settings');
    $documentable_entities = $this->getEntityTypes();
    foreach ($documentable_entities as $entity_name => $enabled) {
      if ($enabled) {
        $config->set($entity_name, $form_state->getValue($entity_name));
      }
      else {
        // This entity is not enabled, so override the original form value.
        $config->set($entity_name, NULL);
      }

      $config->set('export_location', $form_state->getValue('export_location'));
      $config->save();
    }
  }

}
