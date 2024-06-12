<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Schema.org properties settings for properties.
 */
class SchemaDotOrgSettingsPropertiesForm extends SchemaDotOrgSettingsFormBase {

  /**
   * The field type manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypeManager;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_properties_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->fieldTypeManager = $container->get('plugin.manager.field.field_type');
    return $instance;
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
    $definitions = $this->fieldTypeManager->getDefinitions();
    $field_types = [];
    $field_types[] = '# Available field types';
    $field_types[] = '# ---------------------';
    foreach ($definitions as $field_type => $definition) {
      $field_types[] = '# ' . $field_type . ' = ' . $definition['label'];
    }
    $form['schema_properties'] = [
      '#type' => 'details',
      '#title' => $this->t('Property settings'),
    ];
    $form['schema_properties']['default_fields'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org property fields'),
      '#rows' => 20,
      '#description' => $this->t('Enter default Schema.org property field definition used when adding a Schema.org property to an entity type.'),
      '#description_link' => 'properties',
      '#example' => implode(PHP_EOL, $field_types) . '
SchemaType--propertyName:
  type: string
  label: Property name
  unlimited: true
  required: true
propertyName:
  type: string
',
    ];
    $form['schema_properties']['default_field_formatter_settings'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org property field formatter settings'),
      '#rows' => 20,
      '#description' => $this->t('Enter default Schema.org property field formatter settings used when adding a Schema.org property to an entity type.'),
      '#description_link' => 'properties',
      '#example' => '
SchemaType--propertyName:
  label: hidden
propertyName:
  label: hidden
',
    ];
    $form['schema_properties']['default_field_types'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org property field types'),
      '#description' => $this->t('Enter the field types applied to a Schema.org property when the property is added to an entity type.'),
      '#description_link' => 'properties',
      '#example' => '
schemaProperty:
  - field_type_01
  - field_type_02
  - field_type_03
SchemaType--propertyName:
  - field_type_01
  - field_type_02
  - field_type_03
',
    ];
    $form['schema_properties']['default_field_weights'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Default Schema.org property field weights'),
      '#description' => $this->t('Enter Schema.org property default field order/weight to help order fields as they are added to entity types.'),
      '#example' => '
- field_name_01
- field_name_02
- field_name_03
',
    ];
    $form['schema_properties']['range_includes'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type/property custom range includes'),
      '#description' => $this->t('Enter custom range includes for Schema.org types/properties.'),
      '#description_link' => 'types',
      '#example' => '
TypeName--propertyName:
  - Type01
  - Type02
propertyName:
  - Type01
  - Type02
',
    ];
    $form['schema_properties']['ignored_properties'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Ignored Schema.org properties'),
      '#description' => $this->t('Enter Schema.org properties that should ignored and not displayed on the Schema.org mapping form and simplifies the user experience.'),
      '#description_link' => 'properties',
      '#example' => '
- propertyName01
- propertyName02
- propertyName03
',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('schemadotorg.settings')
      ->set('schema_properties', $form_state->getValue('schema_properties'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
