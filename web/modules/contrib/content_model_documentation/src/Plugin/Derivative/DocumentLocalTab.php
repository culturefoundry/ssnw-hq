<?php

namespace Drupal\content_model_documentation\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\content_model_documentation\CMDocumentConnectorTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class for Config Task.
 */
class DocumentLocalTab extends DeriverBase implements ContainerDeriverInterface {

  use CMDocumentConnectorTrait;
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Creates LocalTask objects on documentable entities.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $config_factory->get('content_model_documentation.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];
    $documentable_entities = $this->getDocumentableEntityTypes();
    $flip = ['menu_link_content' => 'menu'];
    $documentable_entities = str_replace(array_keys($flip), array_values($flip), $documentable_entities);

    if (empty($documentable_entities)) {
      return $this->derivatives;
    }

    foreach ($documentable_entities as $entity_type_id) {
      if ($entity_type_id === 'field') {
        // Skipping fields, because I don't know how to do them yet.
        // They will need more complex handling I think.
        continue;
      }

      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
      if (!$entity_type) {
        continue;
      }
      $base_entity_type = $entity_type;
      if (method_exists($entity_type, 'getBundleEntityType') && $bundle_type = $entity_type->getBundleEntityType()) {
        // If the entity type uses bundles, use the bundle type instead.
        $base_entity_type = $this->entityTypeManager->getDefinition($bundle_type);
      }
      $base_entity_type_id = $base_entity_type->id();
      $base_route = $this->getBaseRoute($entity_type_id, $base_entity_type_id);
      $bundle_type = $bundle_type ?? $entity_type_id;

      $bundle_of = (method_exists($entity_type, 'getBundleOf'));
      if ($bundle_of) {
        $this->derivatives["$entity_type_id.document_tab"] = [
          'route_name' => "entity.{$bundle_type}.document",
          'title' => $this->t('Documentation'),
          'base_route' => $base_route,
          'weight' => 90,
        ];
      }
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

  /**
   * Gets the base route to use for the entity in question.
   *
   * @param string $entity_type
   *   The machine name of the entity.
   * @param string $base_entity_type_id
   *   The base entity type id.
   * @param bool $fields
   *   A flag to indicate whether what should be returned is for fields.
   *
   * @see https://www.drupal.org/i/3389614
   *
   * @return string
   *   The vase route for the entity.
   */
  protected function getBaseRoute(string $entity_type, string $base_entity_type_id, bool $fields = FALSE): string {
    // Case race, first to be TRUE wins.
    switch (TRUE) {
      case ($entity_type === 'block_content') && (!empty($field)):
        $base_route = "/admin/structure/block/block-content/manage/{$bundle}/fields/block_content.{$bundle}.{$field}/document";
        break;

      case ($entity_type === 'block_content'):
        // Block edit /admin/structure/block/block-content/manage/BLOCKNAME.
        $base_route = "entity.{$base_entity_type_id}.edit_form";
        break;

      case ($entity_type === 'media') && (!empty($field)):
        $base_route = "/admin/structure/media/manage/{$bundle}/fields/media.{$bundle}.{$field}/document";
        break;

      case ($entity_type === 'media'):
        // Media edit /admin/structure/media/manage/MEDIANAME.
        $base_route = "entity.{$base_entity_type_id}.edit_form";
        break;

      case ($entity_type === 'menu_link') && (!empty($field)):
        $base_route = "/admin/structure/menu/manage/{$bundle_hyphenated}/fields/menu_link_content.{$bundle_hyphenated}.{$field}/document";
        break;

      case ($entity_type === 'menu'):
      case ($entity_type === 'menu_link'):
        $base_route = "entity.menu.edit_form";
        break;

      case ($entity_type === 'node') && (!empty($field)):
        // Field instance edit /admin/structure/types/manage/BUNDLENAME/fields/node.event.FIELDNAME.
        $base_route = "/admin/structure/types/manage/{$bundle}/fields/node.{$bundle}.{$field}/document";
        break;

      case ($entity_type === 'node'):
        // Content type edit /admin/structure/types/manage/BUNDLENAME.
        $base_route = "entity.{$base_entity_type_id}.edit_form";
        break;

      case ($entity_type === 'paragraph') && (!empty($field)):
        $base_route = "/admin/structure/paragraphs_type/{$bundle}/fields/{$bundle}.{$field}/document";
        break;

      case ($entity_type === 'paragraph'):
        // Paragraph type edit /admin/structure/paragraphs_type/PARAGRAPHNAME.
        $base_route = "entity.{$base_entity_type_id}.edit_form";
        break;

      case ($entity_type === 'taxonomy_term') && (!empty($field)):
        $base_route = "/admin/structure/taxonomy/manage/{$bundle}/overview/fields/taxonomy_term.{$bundle}.{$field}/document";
        break;

      case ($entity_type === 'taxonomy_term'):
      case ($entity_type === 'taxonomy_vocabulary'):
        // Vocabulary edit /admin/structure/taxonomy/manage/VOCABULARYNAME.
        $base_route = "entity.{$base_entity_type_id}.overview_form";
        break;
    }

    return $base_route ?? '';
  }

}
