<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_paragraphs;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Schema.org paragraphs JSON-LD manager interface.
 */
interface SchemaDotOrgParagraphsJsonLdManagerInterface {

  /**
   * Alter the Schema.org property JSON-LD value for an entity's field item.
   *
   * Adds paragraph from paragraphs library to JSON-LD.
   *
   * @param mixed $value
   *   Alter the Schema.org property JSON-LD value.
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The entity's field item.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @see hook_schemadotorg_jsonld_schema_property_alter()
   */
  public function jsonldSchemaPropertyAlter(mixed &$value, FieldItemInterface $item, BubbleableMetadata $bubbleable_metadata): void;

}
