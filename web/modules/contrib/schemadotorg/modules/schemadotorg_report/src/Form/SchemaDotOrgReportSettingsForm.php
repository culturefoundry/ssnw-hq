<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_report\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\Form\SchemaDotOrgSettingsFormBase;

/**
 * Configure Schema.org report settings.
 */
class SchemaDotOrgReportSettingsForm extends SchemaDotOrgSettingsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_report_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['schemadotorg_report.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['schemadotorg_report'] = [
      '#type' => 'details',
      '#title' => $this->t('References settings'),
    ];
    $form['schemadotorg_report']['about'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org about links'),
      '#description' => $this->t('Enter links to general information about Schema.org.'),
      '#example' => "
- title: 'Schema.org Documentation'
  uri: 'https://schema.org/docs/documents.html'
",
    ];
    $form['schemadotorg_report']['types'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type specific links'),
      '#description' => $this->t('Enter links to specific information about Schema.org types.'),
      '#example' => "
SchemaType:
  - title: 'Schema.org Documentation'
    uri: 'https://schema.org/docs/documents.html'
",
    ];
    $form['schemadotorg_report']['issues'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type issue/discussion links'),
      '#description' => $this->t('Enter links to specific issues/discussions about Schema.org types.'),
      '#example' => "
SchemaType:
  - title: 'Schema.org Documentation'
    uri: 'https://schema.org/docs/documents.html'

",
    ];
    return parent::buildForm($form, $form_state);
  }

}
