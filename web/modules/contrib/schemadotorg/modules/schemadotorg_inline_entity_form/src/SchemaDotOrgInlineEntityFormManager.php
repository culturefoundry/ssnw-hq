<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_inline_entity_form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * The Schema.org Inline Entity Form manager.
 */
class SchemaDotOrgInlineEntityFormManager implements SchemaDotOrgInlineEntityFormManagerInterface {

  /**
   * Constructs a SchemaDotOrgInlineEntityFormManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function entityInsert(EntityInterface $entity): void {
    // Make sure we are insert node type that is mapped to a Schema.org type.
    // @see \Drupal\schemadotorg\SchemaDotOrgEntityTypeBuilder::addEntityBundle
    if (empty($entity->schemaDotOrgType)) {
      return;
    }

    // Make sure we get the entity type bundle of.
    if (!$entity instanceof ConfigEntityInterface
      || !$entity->getEntityType() instanceof EntityTypeInterface
      || empty($entity->getEntityType()->getBundleOf())) {
      return;
    }

    if ($entity->isSyncing()) {
      return;
    }

    $entity_type_id = $entity->getEntityType()->getBundleOf();
    $config = $this->configFactory->get('schemadotorg_inline_entity_form.settings');

    // Check Schema.org type is subtype of the inline entity form displays
    // enabled by default.
    $default_type_form_displays = $config->get('default_type_form_displays');
    $parts = [
      'entity_type_id' => $entity_type_id,
      'schema_type' => $entity->schemaDotOrgType,
      'bundle' => $entity->id(),
    ];
    if (!$this->schemaTypeManager->getSetting($default_type_form_displays, $parts)) {
      return;
    }

    $entity_form_mode_storage = $this->entityTypeManager->getStorage('entity_form_mode');
    // Create the inline entity form mode if it does not exist.
    if (!$entity_form_mode_storage->load('node.inline_entity_form')) {
      $entity_form_mode_storage->create([
        'id' => $entity_type_id . '.inline_entity_form',
        'label' => 'Inline entity form',
        'targetEntityType' => $entity_type_id,
      ])->save();
    }

    // Create the inline entity form display.
    $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $entity->id(), 'inline_entity_form');

    // Hide most default base field components.
    // @see \Drupal\node\Entity\Node::baseFieldDefinitions
    $components = $form_display->getComponents();
    $component_keys = array_keys($components);
    $default_components = $config->get('default_form_display_components');
    $remove_keys = array_diff_key(
      array_combine($component_keys, $component_keys),
      array_combine($default_components, $default_components)
    );
    foreach ($remove_keys as $remove_key) {
      $form_display->removeComponent($remove_key);
    }

    $form_display->save();
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
    // Make sure this an entity reference field.
    if ($field_storage_values['type'] !== 'entity_reference') {
      return;
    }

    $entity_type_id = $field_values['entity_type'];

    // Check the for supported Schema.org property.
    $default_schema_properties = $this->configFactory
      ->get('schemadotorg_inline_entity_form.settings')
      ->get('default_schema_properties');
    $parts = [
      'entity_type_id' => $entity_type_id,
      'bundle' => $field_values['bundle'],
      'schema_type' => $schema_type,
      'schema_property' => $schema_property,
    ];
    if (!$this->schemaTypeManager->getSetting($default_schema_properties, $parts)) {
      return;
    }

    if (empty($widget_id)) {
      $widget_id = 'inline_entity_form_complex';
      $widget_settings = [
        'allow_existing' => TRUE,
        'allow_duplicate' => TRUE,
        'collapsible' => TRUE,
        'revision' => TRUE,
      ];

      /** @var \Drupal\Core\Entity\EntityFormModeInterface|null $entity_form_mode */
      $entity_form_mode = $this->entityTypeManager
        ->getStorage('entity_form_mode')
        ->load($entity_type_id . '.inline_entity_form');
      // If the 'inline entity form' form mode exists, use it.
      if ($entity_form_mode) {
        $widget_settings['form_mode'] = 'inline_entity_form';
      }

      // If the 'content browser' module is installed, use it.
      if ($this->moduleHandler->moduleExists('content_browser')) {
        $widget_settings['third_party_settings'] = [
          'entity_browser_entity_form' => [
            'entity_browser_id' => 'browse_content',
          ],
        ];
      }
    }
  }

}
