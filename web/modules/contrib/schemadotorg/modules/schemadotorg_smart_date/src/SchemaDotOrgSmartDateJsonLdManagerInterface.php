<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_smart_date;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Schema.org Smart Date JSON-LD manager interface.
 */
interface SchemaDotOrgSmartDateJsonLdManagerInterface {

  /**
   * Alter the Schema.org JSON-LD date to include additional Smart Date data.
   *
   * @param array &$data
   *   The JSON-LD date.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The Smart Date field items.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @see datetime_range_schemadotorg_jsonld_schema_type_field_alter()
   */
  public function alterProperties(array &$data, FieldItemListInterface $items, BubbleableMetadata $bubbleable_metadata): void;

}
