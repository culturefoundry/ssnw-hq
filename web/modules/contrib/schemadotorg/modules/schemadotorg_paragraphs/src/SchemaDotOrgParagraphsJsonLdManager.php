<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_paragraphs;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;

/**
 * Schema.org paragraphs JSON-LD manager.
 */
class SchemaDotOrgParagraphsJsonLdManager implements SchemaDotOrgParagraphsJsonLdManagerInterface {

  /**
   * Constructs a SchemaDotOrgJsonLdManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface|null $schemaJsonLdBuilder
   *   The schema.org JSON-LD builder.
   */
  public function __construct(
    protected ModuleHandlerInterface $moduleHandler,
    protected ?SchemaDotOrgJsonLdBuilderInterface $schemaJsonLdBuilder = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function jsonldSchemaPropertyAlter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void {
    // Make sure the Paragraphs Library module is enabled.
    if (!$this->moduleHandler->moduleExists('paragraphs_library')) {
      return;
    }

    $field_storage_definition = $item->getFieldDefinition()
      ->getFieldStorageDefinition();
    // Check that the field is an entity_reference_revisions type that is
    // targeting paragraphs.
    if ($field_storage_definition->getType() !== 'entity_reference_revisions'
      || $field_storage_definition->getSetting('target_type') !== 'paragraph') {
      return;
    }

    // Check that the item entity is a paragraph from the
    // Paragraphs library.
    if (empty($item->entity)
      || !$item->entity instanceof ParagraphInterface
      || $item->entity->getType() !== 'from_library') {
      return;
    }

    /** @var \Drupal\paragraphs\ParagraphInterface $from_library_paragraph */
    $from_library_paragraph = $item->entity;
    if (!$from_library_paragraph->hasField('field_reusable_paragraph')
      || empty($from_library_paragraph->field_reusable_paragraph->entity)) {
      return;
    }

    /** @var \Drupal\paragraphs_library\LibraryItemInterface $from_library_item */
    $from_library_item = $from_library_paragraph->field_reusable_paragraph->entity;
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $from_library_item->get('paragraphs')->entity;
    if (!$from_library_item->hasField('paragraphs')
      || empty($from_library_item->paragraphs->entity)) {
      return;
    }

    // Build the paragraphs JSON-LD.
    $value = $this->schemaJsonLdBuilder->buildEntity(
      entity: $paragraph,
      bubbleable_metadata: $bubbleable_metadata,
    );
  }

}
