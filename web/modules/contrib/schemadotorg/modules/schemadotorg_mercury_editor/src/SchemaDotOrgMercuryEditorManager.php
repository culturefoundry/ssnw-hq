<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mercury_editor;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManager;
use Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManagerInterface;

/**
 * The Schema.org Mercury Editor manager.
 */
class SchemaDotOrgMercuryEditorManager implements SchemaDotOrgMercuryEditorManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a new instance of the class.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManagerInterface $schemaLayoutParagraphsManager
   *   The Schema.org Layout Paragraphs manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
    protected SchemaDotOrgLayoutParagraphsManagerInterface $schemaLayoutParagraphsManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingFormAlter(array &$form, FormStateInterface &$form_state): void {
    if (!$this->moduleHandler->moduleExists('schemadotorg_ui')) {
      return;
    }

    /** @var \Drupal\schemadotorg\Form\SchemaDotOrgMappingForm $form_object */
    $form_object = $form_state->getFormObject();
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $form_object->getEntity();

    // Exit if no Schema.org type has been selected.
    if (!$mapping->getSchemaType()) {
      return;
    }

    // Append (via the Mercury Editor) to layout mapping settings.
    if (isset($form['mapping']['mainEntity'])) {
      $form['mapping']['mainEntity']['#title'] .= ' (' . $this->t('via the Mercury Editor') . ')';
      if (isset($form['mapping']['mainEntity']['name'])
        && $form['mapping']['mainEntity']['name']['#type'] === 'checkbox') {
        $form['mapping']['mainEntity']['name']['#title'] .= ' (' . $this->t('via the Mercury Editor') . ')';
        $form['mapping']['mainEntity']['name']['#description'] = $this->t("If checked, a 'Layout' field is added to the content type which allows content authors to build layouts using the Mercury Editor.");
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function mappingPresave(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    // Ensure that the mapping support layout paragraphs.
    if (!$this->schemaLayoutParagraphsManager->isLayoutParagraphsEnabled($mapping->getTargetEntityTypeId(), $mapping->getSchemaType())) {
      return;
    }

    // Check if the layout paragraphs property name is defined, and then enable
    // mercury editor.
    $property_name = SchemaDotOrgLayoutParagraphsManager::PROPERTY_NAME;
    if ($mapping->getTargetEntityTypeId() === 'node'
      && $mapping->hasSchemaPropertyMapping($property_name)) {
      $target_bundle = $mapping->getTargetBundle();
      $this->configFactory->getEditable('mercury_editor.settings')
        ->set("bundles.node.$target_bundle", $target_bundle)
        ->save();
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
    // Check that the field is an entity_reference_revisions type that is
    // targeting layout paragraphs.
    if ($field_storage_values['type'] !== 'entity_reference_revisions'
      || $field_storage_values['settings']['target_type'] !== 'paragraph'
      || $schema_property !== 'mainEntity') {
      return;
    }

    // Make sure the entity type and Schema.org type supports layout paragraphs.
    $entity_type_id = $field_storage_values['entity_type'];
    if ($entity_type_id !== 'node') {
      return;
    }

    // Unset experimental layout builder.
    // @see \Drupal\schemadotorg_layout_paragraphs\SchemaDotOrgLayoutParagraphsManager::alterPropertyField
    $formatter_id = 'layout_paragraphs';
    $formatter_settings['label'] = 'hidden';
    unset($formatter_settings['empty_message']);
  }

}
