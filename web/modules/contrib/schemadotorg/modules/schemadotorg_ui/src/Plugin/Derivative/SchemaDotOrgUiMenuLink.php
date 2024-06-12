<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides menu link definitions for all entity bundles.
 */
final class SchemaDotOrgUiMenuLink extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;


  /**
   * The module handler.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    $instance = new static();
    $instance->moduleHandler = $container->get('module_handler');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface $mapping_type_storage */
    $mapping_type_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping_type');
    $entity_types = $mapping_type_storage->getEntityTypes();

    $this->derivatives = [];
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
      $bundle_of = $entity_type->getBundleOf();

      // Skip media unless the schemadotorg_media.module is installed.
      if ($bundle_of === 'media'
        && !$this->moduleHandler->moduleExists('schemadotorg_media')) {
        continue;
      }

      if ($bundle_of && in_array($bundle_of, $entity_types)) {
        $entity_type_label = ($entity_type_id === 'paragraphs_type')
          ? $this->t('paragraph type')
          : $entity_type->getSingularLabel();
        $this->derivatives["schemadotorg.{$entity_type_id}.type_add"] = [
          'route_name' => "schemadotorg.{$entity_type_id}.type_add",
          'parent' => "entity.{$entity_type_id}.collection",
          'title' => $this->t('Add Schema.org @type', ['@type' => $entity_type_label]),
          'weight' => -10,
        ] + $base_plugin_definition;
      }
    }

    return $this->derivatives;
  }

}
