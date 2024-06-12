<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org taxonomy default vocabulary manager interface.
 */
interface SchemaDotOrgTaxonomyDefaultVocabularyManagerInterface {

  /**
   * Add default vocabulary to content types when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

}
