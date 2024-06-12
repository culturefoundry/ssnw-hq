<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_embed;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;

/**
 * Schema.org JSON-LD embed manager.
 */
class SchemaDotOrgJsonLdEmbedManager implements SchemaDotOrgJsonLdEmbedInterface {

  /**
   * Xpath selector for finding embedded media.
   */
  protected string $xpath = 'descendant-or-self::*[(@data-entity-type) and (@data-entity-uuid)]';

  /**
   * Constructs a SchemaDotOrgJsonLdEmbedManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface|null $schemaJsonLdBuilder
   *   The Schema.org JSON-LD builder service.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected ?SchemaDotOrgJsonLdBuilderInterface $schemaJsonLdBuilder = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(ContentEntityInterface $entity, BubbleableMetadata $bubbleable_metadata): array {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $mapping_storage->loadByEntity($entity);
    if (!$mapping) {
      return [];
    }

    // Make sure the entity's values includes the [data-entity-type] attribute.
    $text = print_r($entity->toArray(), TRUE);
    if (!str_contains($text, 'data-entity-type')) {
      return [];
    }

    $data = [];
    $schema_properties = $mapping->getSchemaProperties();
    foreach ($schema_properties as $field_name => $schema_property) {
      // Make sure the entity has the field and the current user has
      // access to the field.
      if (!$entity->hasField($field_name) || !$entity->get($field_name)->access('view')) {
        continue;
      }

      /** @var \Drupal\Core\Field\FieldItemListInterface $items */
      $items = $entity->get($field_name);
      $field_type = $items->getFieldDefinition()->getType();
      if (in_array($field_type, ['text_long', 'text_with_summary'])) {
        foreach ($items as $item) {
          $data += $this->getEntitiesData($item->value, $bubbleable_metadata);
        }
      }
    }
    return $data;
  }

  /**
   * Get embedded media and content JSON-LD data from a text value.
   *
   * @param string $value
   *   The text/HTML value.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array
   *   Embedded media and content JSON-LD data from a text value.
   */
  protected function getEntitiesData(string $value, BubbleableMetadata $bubbleable_metadata): array {
    $dom = Html::load($value);
    $xpath = new \DOMXPath($dom);
    $types = [];
    foreach ($xpath->query($this->xpath) as $dom_node) {
      /** @var \DOMElement $dom_node */
      $embed_entity_type_id = $dom_node->getAttribute('data-entity-type');
      $embed_uuid = $dom_node->getAttribute('data-entity-uuid');
      $embed_data = $this->getEntityData($embed_entity_type_id, $embed_uuid, $bubbleable_metadata);
      if ($embed_data) {
        $types["schemadotorg_jsonld_embed-$embed_entity_type_id-$embed_uuid"] = $embed_data;
      }
    }
    return $types;
  }

  /**
   * Get embedded media and content JSON-LD data.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $uuid
   *   The entity uuid.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array|null
   *   Embedded media and content JSON-LD data.
   */
  protected function getEntityData(string $entity_type_id, string $uuid, BubbleableMetadata $bubbleable_metadata): ?array {
    $embed_storage = $this->entityTypeManager->getStorage($entity_type_id);
    $embed_entities = $embed_storage->loadByProperties(['uuid' => $uuid]);
    if (!$embed_entities) {
      return NULL;
    }

    /** @var \Drupal\Core\Entity\EntityInterface $embed_entity */
    $embed_entity = reset($embed_entities);
    if (!$embed_entity->access('view')) {
      return NULL;
    }

    return $this->schemaJsonLdBuilder->buildEntity(
      entity: $embed_entity,
      bubbleable_metadata: $bubbleable_metadata,
    );
  }

}
