<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_descriptions;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\field\FieldConfigInterface;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\schemadotorg_ui\Form\SchemaDotOrgUiMappingForm;

/**
 * Schema.org descriptions manager.
 */
class SchemaDotOrgDescriptionsManager implements SchemaDotOrgDescriptionsManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgDescriptionsManager object.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function entityLoad(array $entities, string $entity_type_id): void {
    // Only alter description on the entity type collection page.
    if ($this->routeMatch->getRouteName() !== "entity.$entity_type_id.collection") {
      return;
    }

    /** @var \Drupal\Core\Config\Entity\ConfigEntityType $entity_type_definition */
    $entity_type_definition = $this->entityTypeManager->getDefinition($entity_type_id);
    $target_entity_type_id = $entity_type_definition->getBundleOf();

    /** @var \Drupal\Core\Entity\EntityInterface[] $entities */
    foreach ($entities as $entity) {
      // Only set description for config entity bundles and if it is empty.
      if (!$entity instanceof ConfigEntityBundleBase || !empty($entity->get('description'))) {
        continue;
      }

      // Get mapping for target entity type and bundle.
      $target_bundle = $entity->id();
      $mapping = SchemaDotOrgMapping::load("$target_entity_type_id.$target_bundle");
      if (!$mapping) {
        continue;
      }

      $schema_type = $mapping->getSchemaType();
      $custom_description = $this->getSchemaCustomDescription($schema_type);
      if ($custom_description) {
        $entity->set('description', $custom_description);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function nodeFormAlter(array &$form, FormStateInterface $form_state): void {
    if (!empty($form['title']['widget'][0]['value']['#description'])) {
      return;
    }

    /** @var \Drupal\node\NodeForm $form_object */
    $form_object = $form_state->getFormObject();
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form_object->getEntity();

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = SchemaDotOrgMapping::loadByEntity($node);
    if (!$mapping) {
      return;
    }

    $schema_type = $mapping->getSchemaType();
    $schema_property = $mapping->getSchemaPropertyMapping('title');
    if (!$schema_property) {
      return;
    }

    $custom_description = $this->getSchemaCustomDescription($schema_type, $schema_property);
    $form['title']['widget'][0]['value']['#description'] = $custom_description;
  }

  /**
   * {@inheritdoc}
   */
  public function mappingFormAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!$this->moduleHandler->moduleExists('schemadotorg_ui')) {
      return;
    }

    /** @var \Drupal\schemadotorg\Form\SchemaDotOrgMappingForm $form_object */
    $form_object = $form_state->getFormObject();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $form_object->getEntity();

    // Exit if no Schema.org type has been selected.
    $schema_type = $mapping->getSchemaType();
    if (!$schema_type) {
      return;
    }

    $add_field = SchemaDotOrgUiMappingForm::ADD_FIELD;
    $custom_descriptions = $this->getCustomDescriptions();

    // Apply the Schema.org type's custom description to the comment.
    $custom_description = $custom_descriptions[$schema_type] ?? NULL;
    if ($custom_description && isset($form['schema_type']['comment']['#markup'])) {
      $form['schema_type']['comment']['#markup'] = $custom_description;
    }

    // Unset the entity's default value and append a note to the description.
    if (isset($form['mapping']['entity']['description'])) {
      $form['mapping']['entity']['description']['#default_value'] = '';
      $form['mapping']['entity']['description']['#description'] .= '<br/>'
        . '<strong>' . $this->t("If left blank, the description will be automatically set to the corresponding Schema.org type's comment or custom description.") . '</strong>';
    }

    // Unset the additionalType default value and append a note to the description.
    if (isset($form['mapping']['additional_type'][$add_field]['description'])) {
      $form['mapping']['additional_type'][$add_field]['description']['#default_value'] = '';
      $form['mapping']['additional_type'][$add_field]['description']['#description'] .= '<br/>'
        . '<strong>' . $this->t("If left blank, the description will be automatically set to the corresponding Schema.org type's comment or custom description.") . '</strong>';
    }

    // Unset each property/field's default value and append a note to the description.
    if (isset($form['mapping']['properties'])) {
      foreach ($form['mapping']['properties'] as $schema_property => &$element) {
        // Apply the Schema.org type's custom description to the comment.
        $custom_description = $this->getSchemaCustomDescription($schema_type, $schema_property, FALSE);
        if ($custom_description && isset($element['property']['comment']['#markup'])) {
          $element['property']['comment']['#markup'] = $custom_description;
        }

        if (isset($element['field'][$add_field]['description'])) {
          $element['field'][$add_field]['description']['#default_value'] = '';
          $element['field'][$add_field]['description']['#description'] .= '<br/>'
            . '<strong>' . $this->t("If left blank, the description will be automatically set to the corresponding Schema.org property's comment or custom description.") . '</strong>';
        }
      }
    }

    // Append a note to custom descriptions.
    // @see schemadotorg_layout_paragraphs.module
    $custom_descriptions = $this->getCustomDescriptions();
    foreach (array_keys($custom_descriptions) as $schema_property) {
      if (isset($form['mapping'][$schema_property][$add_field]['description'])) {
        $form['mapping'][$schema_property][$add_field]['description']['#default_value'] = '';
        $form['mapping'][$schema_property][$add_field]['description']['#description'] .= '<br/>'
          . '<strong>' . $this->t("If left blank, the description will be automatically set.") . '</strong>';
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function entityPrepareForm(EntityInterface $entity, string $operation, FormStateInterface $form_state): void {
    if ($entity instanceof ConfigEntityBundleBase) {
      $entity_type_id = $entity->getEntityType()->getBundleOf();
      $bundle = $entity->id();
      $mapping = SchemaDotOrgMapping::load("$entity_type_id.$bundle");
      if ($mapping) {
        $form_state->set('schemadotorg_descriptions', 'type');
      }
    }
    elseif ($entity instanceof FieldConfigInterface) {
      $entity_type_id = $entity->getTargetEntityTypeId();
      $bundle = $entity->getTargetBundle();
      $field_name = $entity->getName();

      // Display description if a custom description for the field is defined.
      $custom_description = $this->getFieldCustomDescription($entity);
      if ($custom_description) {
        $form_state->set('schemadotorg_descriptions', 'field');
      }

      // Display description if the field is mapped to Schema.org property.
      /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
      $mapping = SchemaDotOrgMapping::load("$entity_type_id.$bundle");
      if ($mapping && $mapping->getSchemaPropertyMapping($field_name)) {
        $form_state->set('schemadotorg_descriptions', 'property');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if ($form_state->get('schemadotorg_descriptions')
      && isset($form['description'])) {

      switch ($form_state->get('schemadotorg_descriptions')) {
        case 'type':
          $description = $this->t("If left blank, this description will be automatically set to the corresponding Schema.org type's comment or custom description.");
          break;

        case 'property':
        case 'field':
        default:
          $description = $this->t("If left blank, this field's description will be automatically set to the corresponding Schema.org property's comment or custom description.");
          break;
      }

      $t_args = [
        ':href' => Url::fromRoute('schemadotorg.settings.general', [], ['fragment' => 'edit-schemadotorg-descriptions'])->toString(),
      ];

      $form['description']['#description'] = $form['description']['#description'] ?? '';
      $form['description']['#description'] .= ($form['description']['#description']) ? '<br/>' : '';
      $form['description']['#description'] .= '<strong>' . $description . '</strong> '
        . $this->t('<a href=":href">View custom descriptions</a>.', $t_args);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function bundleEntityAlter(array &$values, string $schema_type, string $entity_type_id): void {
    $entity_values =& $values['entity'];

    $definition = $this->schemaTypeManager->getType($schema_type);
    $description = $this->schemaTypeBuilder->formatComment($definition['drupal_description'], ['base_path' => 'https://schema.org/']);
    if ($entity_values['description'] === $description) {
      $entity_values['description'] = '';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function propertyFieldAlter(
    string $schema_type,
    string $schema_property,
    array &$field_storage_values,
    array &$field_values,
    ?string &$widget_id,
    array &$widget_settings,
    ?string &$formatter_id,
    array &$formatter_settings,
  ): void {
    // Check Schema.org property and subtype for description.
    $default_description = $this->getSchemaDefaultDescription(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_PROPERTIES, $schema_property);
    if ($default_description) {
      $description = $default_description;
    }
    else {
      $description = NULL;
    }

    // Unset the field's description if it has not been altered.
    if ($field_values['description'] === $description) {
      $field_values['description'] = '';
    }
  }

  /**
   * Get custom descriptions.
   *
   * @return array
   *   Custom descriptions.
   */
  protected function getCustomDescriptions(): array {
    return $this->configFactory
      ->get('schemadotorg_descriptions.settings')
      ->get('custom_descriptions');
  }

  /**
   * Get a field's custom description.
   *
   * @param \Drupal\field\FieldConfigInterface $field
   *   A field.
   *
   * @return string|null
   *   A field's custom description.
   */
  protected function getFieldCustomDescription(FieldConfigInterface $field): ?string {
    $custom_descriptions = $this->getCustomDescriptions();

    $entity_type_id = $field->getTargetEntityTypeId();
    $bundle = $field->getTargetBundle();
    $field_name = $field->getName();

    return $custom_descriptions["$entity_type_id--$bundle--$field_name"]
      ?? $custom_descriptions["$entity_type_id--$field_name"]
      ?? $custom_descriptions["$bundle--$field_name"]
      ?? $custom_descriptions[$field_name]
      ?? NULL;
  }

  /**
   * Get a Schema.org type/property's custom description.
   *
   * NOTE: A Schema.org type/property custom descriptions can be set to NULL
   * which indicates the description should not be set.
   *
   * @param string|null $schema_type
   *   A Schema.org type.
   * @param string|null $schema_property
   *   A Schema.org property.
   * @param bool $default
   *   Use the Schema.org type/property's default description.
   *
   * @return string|null
   *   A Schema.org type/property's custom description.
   *
   * @throws \Exception
   *   If a Schema.org type or property is missing.
   */
  protected function getSchemaCustomDescription(?string $schema_type = NULL, ?string $schema_property = NULL, bool $default = TRUE): ?string {
    $custom_descriptions = $this->getCustomDescriptions();

    if ($schema_property) {
      if (array_key_exists("$schema_type--$schema_property", $custom_descriptions)) {
        return $custom_descriptions["$schema_type--$schema_property"];
      }
      elseif (array_key_exists($schema_property, $custom_descriptions)) {
        return $custom_descriptions[$schema_property];
      }
      elseif ($default) {
        return $this->getSchemaDefaultDescription(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_PROPERTIES, $schema_property);
      }
      else {
        return NULL;
      }
    }
    elseif ($schema_type) {
      if (array_key_exists($schema_type, $custom_descriptions)) {
        return $custom_descriptions[$schema_type];
      }
      elseif ($default) {
        return $this->getSchemaDefaultDescription(SchemaDotOrgSchemaTypeManagerInterface::SCHEMA_TYPES, $schema_type);
      }
      else {
        return NULL;
      }
    }
    else {
      throw new \Exception('A Schema.org type or property is missing.');
    }
  }

  /**
   * Get Schema.org type/property default description.
   *
   * @param string $table
   *   The Schema.org table.
   * @param string $id
   *   The Schema.org type or property ID.
   *
   * @return string
   *   Schema.org type/property default description.
   */
  protected function getSchemaDefaultDescription(string $table, string $id): string {
    $options = ['base_path' => 'https://schema.org/'];
    $type_definition = $this->schemaTypeManager->getItem($table, $id);
    return $type_definition
      ? $this->schemaTypeBuilder->formatComment($type_definition['drupal_description'], $options)
      : '';
  }

}
