<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Schema.org general settings for types.
 */
class SchemaDotOrgSettingsGeneralForm extends SchemaDotOrgSettingsFormBase {

  /**
   * The Schema.org data installer.
   *
   * @var \Drupal\schemadotorg\SchemaDotOrgInstallerInterface
   */
  protected $installer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->installer = $container->get('schemadotorg.installer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_general_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['schemadotorg.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['requirements'] = [
      '#type' => 'details',
      '#title' => $this->t('Requirements settings'),
    ];
    $form['requirements']['recommended_modules'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Check for recommended modules'),
      '#description' => $this->t('If checked, recommended modules that help integrate and support Schema.org mappings, entities, and fields will be checked for and a warning will be displayed if they are missing.'),
    ];
    $form['schema_data'] = [
      '#type' => 'details',
      '#title' => $this->t('Data settings'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $form['schema_data']['file'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Schema.org data file/URL'),
      '#description' => $this->t('Enter a root-relative, Schema.org Blueprints module relative, or absolute URL.')
      . ' ' . $this->t('The file/URL must include the <code>[TABLE]</code> token and the <code>[VERSION]</code> token is optional.')
      . ' <strong>' . $this->t('This value should only be changed if you want to import a custom/external schema.') . '</strong>',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    // Validate the Schema.org data file/URL.
    $file = $form_state->getValue(['schema_data', 'file']);
    if (!$this->installer->validateFileName($file)) {
      $form_state->setError($form['schema_data']['file'], $this->t('Schema.org data file/URL is not valid.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $original_file = $this->config('schemadotorg.settings')->get('schema_data.file');
    $file = $form_state->getValue(['schema_data', 'file']);

    // Track if we need to reinstall the Schema.org types and properties.
    $reinstall = ($original_file !== $file);

    // Save the file.
    $this->config('schemadotorg.settings')
      ->set('schema_data.file', $file)
      ->set('requirements', $form_state->getValue('requirements'))
      ->save();

    // Reinstall the tables.
    if ($reinstall) {
      $this->installer->importTables();
      $this->messenger()->addStatus($this->t('The  Schema.org types and properties tables have been updated.'));
    }

    parent::submitForm($form, $form_state);
  }

}
