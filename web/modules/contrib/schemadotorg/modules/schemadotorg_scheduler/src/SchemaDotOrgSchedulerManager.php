<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_scheduler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * The Schema.org scheduler manager.
 */
class SchemaDotOrgSchedulerManager implements SchemaDotOrgSchedulerManagerInterface {

  /**
   * Constructs a SchemaDotOrgSchedulerManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    if ($mapping->isSyncing()) {
      return;
    }

    // Make sure we are adding scheduling to nodes.
    $entity_type_id = $mapping->getTargetEntityTypeId();
    if ($entity_type_id !== 'node') {
      $this->setFormDisplay($mapping);
      return;
    }

    // Check if the Schema.org mapping support scheduling.
    $scheduled_types = $this->configFactory
      ->get('schemadotorg_scheduler.settings')
      ->get('scheduled_types');
    $scheduled = $this->schemaTypeManager->getSetting($scheduled_types, $mapping);
    if (!$scheduled) {
      $this->setFormDisplay($mapping);
      return;
    }

    // Get third party settings for scheduler.
    $third_party_settings = [
      'publish_enable' => in_array('publish', $scheduled),
      'publish_past_date' => 'error',
      'publish_past_date_created' => FALSE,
      'publish_required' => FALSE,
      'publish_revision' => FALSE,
      'publish_touch' => FALSE,
      'unpublish_enable' => in_array('unpublish', $scheduled),
      'unpublish_required' => FALSE,
      'unpublish_revision' => FALSE,
      'expand_fieldset' => 'when_required',
      'fields_display_mode' => 'vertical_tab',
      'show_message_after_update' => TRUE,
    ];

    // Set scheduler third party settings for the node type.
    $node_type = $mapping->getTargetEntityBundleEntity();
    foreach ($third_party_settings as $key => $value) {
      $node_type->setThirdPartySetting('scheduler', $key, $value);
    }
    $node_type->save();
    $this->setFormDisplay($mapping);
  }

  /**
   * Hide scheduler publish and unpublish components from form display.
   *
   * Issue #3317999: It is impossible to add media for node via media library
   * if Scheduler content moderation integration module is enabled.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   *
   * @see https://www.drupal.org/project/scheduler_content_moderation_integration/issues/3317999
   */
  protected function setFormDisplay(SchemaDotOrgMappingInterface $mapping): void {
    if (!$this->moduleHandler->moduleExists('scheduler_content_moderation_integration')) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    // Set scheduler third party settings for the node type.
    $entity_type = $mapping->getTargetEntityBundleEntity();
    if (!$entity_type) {
      return;
    }

    $publish_enable = $entity_type->getThirdPartySetting('scheduler', 'publish_enable');
    $unpublish_enable = $entity_type->getThirdPartySetting('scheduler', 'unpublish_enable');
    $scheduler_moderation_installed = $this->moduleHandler->moduleExists('scheduler_moderation');
    $form_modes = array_merge(['default'], array_keys($this->entityDisplayRepository->getFormModes($entity_type_id)));
    foreach ($form_modes as $form_mode) {
      $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, $form_mode);
      $form_display->setComponent('scheduler_settings', ['weight' => 50]);

      if (!$publish_enable) {
        if ($form_display->getComponent('publish_on')) {
          $form_display->removeComponent('publish_on');
        }
        if ($form_display->getComponent('publish_state')) {
          $form_display->removeComponent('publish_state');
        }
      }
      else {
        $form_display->setComponent('publish_on', ['type' => 'datetime_timestamp_no_default', 'weight' => 52]);
        if ($scheduler_moderation_installed) {
          $form_display->setComponent('publish_state', ['type' => 'scheduler_moderation', 'weight' => 55]);
        }
      }

      if (!$unpublish_enable) {
        if ($form_display->getComponent('unpublish_on')) {
          $form_display->removeComponent('unpublish_on');
        }
        if ($form_display->getComponent('unpublish_state')) {
          $form_display->removeComponent('unpublish_state');
        }
      }
      else {
        $form_display->setComponent('unpublish_on', ['type' => 'datetime_timestamp_no_default', 'weight' => 52]);
        if ($scheduler_moderation_installed) {
          $form_display->setComponent('unpublish_state', ['type' => 'scheduler_moderation', 'weight' => 55]);
        }
      }
      $form_display->save();
    }
  }

}
