<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Schema.org types settings for types.
 */
class SchemaDotOrgSettingsTypesForm extends SchemaDotOrgSettingsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_types_settings_form';
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
    $form['schema_types'] = [
      '#type' => 'details',
      '#title' => $this->t('Type settings'),
    ];
    $form['schema_types']['default_types'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org entity type'),
      '#rows' => 20,
      '#description' => $this->t('Enter default Schema.org entity type definition used when adding a Schema.org entity type.'),
      '#description_link' => 'types',
      '#example' => '
SchemaType:
  name: entity_name
  label: Entity Title
  description: A description of the entity type.
',
    ];
    $form['schema_types']['default_properties'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org type properties'),
      '#rows' => 10,
      '#description' => $this->t('Enter default properties for Schema.org types.')
      . '<br/><br/>'
      . $this->t('Please note: Default properties are automatically inherited from their parent Schema.org type and <a href="https://schema.org/Intangible">Intangible</a> are automatically assigned all defined properties, except for properties defined via <a href="https://schema.org/Thing">Thing</a>.')
      . ' '
      . $this->t('Prepend a minus to a property to explicitly remove the property from the specific Schema.org type.'),
      '#description_link' => 'types',
      '#example' => "
SchemaType:
  - '-removedPropertyName01'
  - '-removedPropertyName02'
  - '-removedPropertyName03'
  - propertyName01
  - propertyName02
  - propertyName03
",
    ];
    $form['schema_types']['default_property_values'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org type property values'),
      '#description' => $this->t('Enter default Schema.org type property value.'),
      '#description_link' => 'types',
      '#example' => '
SchemaType:
  propertyName01: DefaultValue01
  propertyName02: DefaultValue02
',
    ];
    $form['schema_types']['default_field_types'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org type field types'),
      '#description' => $this->t('Enter the field types applied to a Schema.org type when a property is added to an entity type.')
      . ' '
      . $this->t('Field types are applied in the order that they are entered.'),
      '#description_link' => 'types',
      '#example' => '
SchemaType:
  - field_type_01
  - field_type_02
  - field_type_03
',
    ];
    $form['schema_types']['main_properties'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type main properties'),
      '#description' => $this->t('Enter the main property for a Schema.org type. Defaults to <em>name</em> for unspecified Schema.org types. Set the main property to <em>null</em> when there is no applicable main property for the Schema.org type.'),
      '#description_link' => 'types',
      '#example' => 'SchemaType: propertyName',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('schemadotorg.settings')
      ->set('schema_types', $form_state->getValue('schema_types'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
