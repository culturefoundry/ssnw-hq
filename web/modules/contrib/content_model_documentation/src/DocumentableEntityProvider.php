<?php

namespace Drupal\content_model_documentation;

use Drupal\content_model_documentation\Entity\CMDocument;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;

/**
 * Service providing information on the Documentable entities.
 */
class DocumentableEntityProvider extends ServiceProviderBase {

  use CMDocumentConnectorTrait;

  /**
   * Module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Documentable Module service.
   *
   * @var \Drupal\content_model_documentation\DocumentableModules
   */
  protected $documentableModules;

  /**
   * Drupal's entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * An array of fieldmap.
   *
   * @var array
   */
  protected $fieldMap;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory service.
   * @param \Drupal\content_model_documentation\DocumentableModules $documentableModules
   *   The documentable modules service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The time service.
   */
  public function __construct(ConfigFactoryInterface $configFactory, DocumentableModules $documentableModules, EntityFieldManagerInterface $entityFieldManager) {
    $this->config = $configFactory->get('content_model_documentation.settings');
    $this->entityFieldManager = $entityFieldManager;
    $this->fieldMap = $this->entityFieldManager->getFieldMap();
    $this->documentableModules = $documentableModules;
  }

  /**
   * Get all documentable entities (not including cm_documents).
   *
   * @return array
   *   Array of all entities identified as documentable.
   */
  public static function getUnDocumentedEntities(): array {
    $documentableEntityProvider = \Drupal::service('content_model_documentation.documentable.entity.provider');
    $documentable_entities = $documentableEntityProvider->getAvailableEntitiesToDocument();

    return $documentable_entities;
  }

  /**
   * Non-static run function to gather all the entities that are documentable.
   *
   * @return array
   *   An array of documentable entities.
   */
  public function getAvailableEntitiesToDocument() {
    $documentable_non_entities = CMDocument::getOtherDocumentableTypes();

    $documentable_entities = $this->getModules();
    $documentable_entities = array_merge($documentable_entities, $this->getAllDocumentableEntities());
    $documentable_entities = $this->removeBaseEntities($documentable_entities);
    asort($documentable_entities, SORT_NATURAL);
    $documentable_entities = array_merge($documentable_non_entities, $documentable_entities);

    return $documentable_entities;
  }

  /**
   * Gets all entities that are allowed by content_model_documentation config.
   *
   * @return array
   *   An array of key value pairs for the entities that are documentable.
   */
  protected function getAllDocumentableEntities(): array {
    $documentable_entities = [];
    $documentable_types = $this->getDocumentableEntityTypes();
    foreach ($documentable_types as $documentable_type) {
      $documentable_entities = $this->addEntityBundles($documentable_type, $documentable_entities);
      $documentable_entities = $this->addEntityFields($documentable_type, $documentable_entities);
    }
    // Let's sort them so they make more sense in the list.
    natcasesort($documentable_entities);
    return $documentable_entities;
  }

  /**
   * Gets the list of modules for documenting.
   *
   * @return array
   *   An array of unique strings representing modules and submodules.
   */
  protected function getModules(): array {
    if ($this->config->get('modules')) {
      $modules = $this->documentableModules->getDocumentableModulesSelectList();

    }
    return $modules ?? [];
  }

  /**
   * Removes base entities that make no sense to document and other cruft.
   *
   * @param array $documentable_entities
   *   An array of key value pairs to have base entities removed from.
   *
   * @return array
   *   An array of key value pairs for the entities that are documentable.
   */
  protected function removeBaseEntities(array $documentable_entities): array {
    $remove_these = [];
    // @todo Flesh this out. So far there are none identified to remove.
    return array_diff_key($documentable_entities, $remove_these);
  }

  /**
   * Removes options that have already been documented except self.
   *
   * @param string $currently_selected
   *   The value of 'documented_entity' property currently selected.
   * @param array $options
   *   The options for the 'documented_entity' select list.
   *
   * @return array
   *   An array of key value pairs for the entities that are documentable.
   */
  public static function removeDocumentedEntities($currently_selected, array $options): array {
    $connection = Database::getConnection();
    $query = $connection->select('cm_document', 'cm_document')
      ->fields('cm_document', ['documented_entity']);
    $existing_documents = $query->execute();
    $existing_documented_entities = [];
    $allow_multiples = CMDocument::getOtherDocumentableTypes();
    foreach ($existing_documents as $existing_document) {
      $documented = $existing_document->documented_entity;
      if (empty($existing_documented_entities[$documented]) && $currently_selected !== $documented && empty($allow_multiples[$documented])) {
        // It is not already in the array, so add it.
        $existing_documented_entities[$documented] = NULL;
      }
    }
    $available_options = array_diff_key($options, $existing_documented_entities);

    return $available_options;
  }

  /**
   * Get documentable bundle entities and add them to $documentable_entities.
   *
   * @param string $entity_type
   *   The name of the entity type.
   * @param array $documentable_entities
   *   The incoming array of documentable entities.
   *
   * @return array
   *   Array of documentable entities.
   */
  protected function addEntityBundles($entity_type, array $documentable_entities): array {
    if (!empty($this->fieldMap[$entity_type]) && is_array($this->fieldMap[$entity_type])) {
      // We use uuid because everything has one.
      foreach ($this->fieldMap[$entity_type]['uuid']['bundles'] as $bundle) {
        $documentable_entities["{$entity_type}.{$bundle}"] = "{$entity_type}.{$bundle}";
      }
    }

    return $documentable_entities;
  }

  /**
   * Get documentable field entities and add them to $documentable_entities.
   *
   * @param string $entity_type
   *   The name of the entity type.
   * @param array $documentable_entities
   *   The incoming array of documentable entities.
   *
   * @return array
   *   Array of documentable entities.
   */
  protected function addEntityFields($entity_type, array $documentable_entities): array {
    $should_document_fields = $this->config->get('field');
    if ($should_document_fields && (!empty($this->fieldMap[$entity_type]))) {
      foreach ($this->fieldMap[$entity_type] as $element_name => $element) {
        if (!$this->isField($element_name)) {
          // It is not a field element, so bail out.
          continue;
        }
        foreach ($element['bundles'] as $bundle_name) {
          $documentable_entities["{$entity_type}.{$bundle_name}.{$element_name}"] = "{$entity_type}.{$bundle_name}.{$element_name}";
          // Add base field if it has not already been added.
          $base_field_name = "base.field.{$element_name}";
          if (!isset($documentable_entities[$base_field_name])) {
            $documentable_entities[$base_field_name] = $base_field_name;
          }
        }
      }

    }
    return $documentable_entities;
  }

}
