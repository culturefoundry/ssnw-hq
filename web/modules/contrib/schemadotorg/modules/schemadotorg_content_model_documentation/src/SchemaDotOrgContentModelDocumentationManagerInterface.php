<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_content_model_documentation;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface;

/**
 * Schema.org Content Model Documentation manager interface.
 */
interface SchemaDotOrgContentModelDocumentationManagerInterface {

  /**
   * A mapping of Schema.org mapping types to documentable entities.
   */
  public const DOCUMENTABLE_ENTITIES = [
    'taxonomy_term' => 'taxonomy',
    'block_content' => 'block',
    'node' => 'node',
    'media' => 'media',
    'paragraph' => 'paragraph',
  ];

  /**
   * Make Schema.org mapping type documentable.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface $mapping_type
   *   The Schema.org mapping type.
   */
  public function mappingTypeInsert(SchemaDotOrgMappingTypeInterface $mapping_type): void;

  /**
   * Add documentation markup field to Schema.org mapping's entity type.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Determine if links should be opened in modal dialogs.
   *
   * @return bool
   *   TRUE if links should be opened in modal dialogs.
   */
  public function openLinksInModal(): bool;

  /**
   * Determine if the markup field can be used for documentation.
   *
   * @return bool
   *   TRUE if the markup field can be used for documentation.
   */
  public function useMarkupField(): bool;

  /**
   * Get Schema.org documentation field name.
   *
   * @return string
   *   The Schema.org documentation field name.
   */
  public function getFieldName(): string;

  /**
   * Get documentation link text.
   *
   * @return string
   *   Documentation link text.
   */
  public function getLinkText(): string;

  /**
   * Get the default template for documentation note.
   *
   * @return string
   *   The default template for documentation note.
   */
  public function getDefaultNotes(): string;

  /**
   * Get the default filer format for documentation note.
   *
   * @return string
   *   The default filter format for documentation note.
   */
  public function getDefaultFormat(): string;

}
