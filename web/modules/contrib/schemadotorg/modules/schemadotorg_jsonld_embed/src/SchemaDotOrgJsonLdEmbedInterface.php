<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_embed;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Schema.org JSON-LD embed manager interface.
 */
interface SchemaDotOrgJsonLdEmbedInterface {

  /**
   * Build embedded media and content entity JSON-LD data.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array
   *   The embedded media and content entity JSON-LD data.
   */
  public function build(ContentEntityInterface $entity, BubbleableMetadata $bubbleable_metadata): array;

}
