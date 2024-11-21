<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\Form\SchemaDotOrgSettingsFormBase;

/**
 * Configure Schema.org JSON-LD settings.
 */
class SchemaDotOrgJsonLdSettingsForm extends SchemaDotOrgSettingsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'schemadotorg_jsonld_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['schemadotorg_jsonld.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['schemadotorg_jsonld'] = [
      '#type' => 'details',
      '#title' => $this->t('JSON-LD settings'),
    ];
    $form['schemadotorg_jsonld']['schema_type_default_values'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type default values'),
      '#description' => $this->t('Enter Schema.org type default values.'),
      '#description_link' => 'types',
      '#example' => '
SchemaType:
  propertyName01: DefaultValue01
  propertyName02: DefaultValue02
',
    ];
    $form['schemadotorg_jsonld']['schema_type_main_properties'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type main properties'),
      '#description' => $this->t('Enter the main properties for a Schema.org types Defaults to <em>name</em> for unspecified Schema.org types. Set the main property to <em>null</em> when there is no applicable main property for the Schema.org type.'),
      '#description_link' => 'types',
      '#example' => 'SchemaType: propertyName',
    ];
    $form['schemadotorg_jsonld']['schema_property_order'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org property order'),
      '#description' => $this->t('Enter the default Schema.org property order.'),
      '#description_link' => 'properties',
      '#example' => '
- propertyName01
- propertyName02
- propertyName03
',
    ];
    $form['schemadotorg_jsonld']['schema_property_image_styles'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org property image styles'),
      '#description' => $this->t('Enter the Schema.org property and the desired image style.'),
      '#description_link' => 'properties',
      '#example' => 'propertyName: image_style',
    ];
    $form['schemadotorg_jsonld']['schema_type_entity_references_display'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Schema.org type entity references display'),
      '#description' => $this->t("Enter how an entity reference's Schema.org type or bundle (for unmapped entities) should be displayed via JSON-LD.")
      . ' '
      . $this->t("Entity references within JSON-LD can include the entity's data, url, label, or none."),
      '#description_link' => 'types',
      '#token_link' => TRUE,
      '#example' => "
Intangible: entity
node--Thing: url
media--image: '[media:field_media_image:1x1:url], [media:field_media_image:4x3:url], [media:field_media_image:16x9:url]'
paragraph--layout: none

# Source patterns (must include the Schema.property or field name)
source_entity_type_id--source_bundle--schemaProperty: none
source_entity_type_id--SourceSchemaType--schemaProperty: none
source_bundle--schemaProperty: none
SourceSchemaType--schemaProperty: none
source_entity_type_id--source_bundle--field_name: none
source_entity_type_id--SourceSchemaType--field_name: none
source_bundle--field_name: none
SourceSchemaType--field_name: none

# Target patterns
target_entity_type_id--target_bundle: none
target_entity_type_id--TargetSchemaType: none
target_bundle: none
TargetSchemaType: none
",
    ];
    $form['schemadotorg_jsonld']['exclude_url'] = [
      '#type' => 'schemadotorg_settings',
      '#title' => $this->t('Exclude @url from types'),
      '#description' => $this->t('Enter entity and Schema.org type that should never include an @url property'),
      '#example' => '
- block_content
- media
- paragraph
- PronounceableText
',
    ];
    return parent::buildForm($form, $form_state);
  }

}
