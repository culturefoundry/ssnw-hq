<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

/**
 * Paragraph plugin implementation of the Schema.org Entity Selection plugin.
 *
 * The paragraph reference selection plugin must support the auto create
 * interface for adding new paragraphs.
 *
 * @see \Drupal\Core\Entity\EntityReferenceSelection\SelectionWithAutocreateInterface
 * @see \Drupal\paragraphs\Plugin\EntityReferenceSelection\ParagraphSelection
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg:paragraph",
 *   label = @Translation("Schema.org Paragraphs Selection"),
 *   entity_types = {"paragraph"},
 *   group = "schemadotorg",
 *   weight = 1,
 * )
 */
class SchemaDotOrgParagraphReferenceSelection extends SchemaDotOrgEntityReferenceSelection {

  /**
   * {@inheritdoc}
   */
  public static function getTargetBundles(array $configuration): array {
    $target_bundles = parent::getTargetBundles($configuration);

    // Track if 'from_library' is being used and make sure to include it.
    $from_library = $configuration['target_bundles']['from_library'] ?? FALSE;
    if ($from_library) {
      $target_bundles['from_library'] = 'from_library';
    }

    return $target_bundles;
  }

}
