<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_preview;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Schema.org JSON-LD preview builder interface.
 */
interface SchemaDotOrgJsonLdPreviewBuilderInterface {

  /**
   * Format Schema.org prevent via JSON-LD.
   */
  const JSONLD = 'jsonld';

  /**
   * Format Schema.org prevent via data (table).
   */
  const DATA = 'data';

  /**
   * Build JSON-LD preview for a route.
   *
   * @param string $format
   *   The format of the JSON-LD preview.
   * @param \Drupal\Core\Routing\RouteMatchInterface|null $route_match
   *   A route match.
   * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
   *   (optional) Object to collect JSON-LD's bubbleable metadata.
   *
   * @return array|null
   *   The JSON-LD preview  for a route or NULL if the route does not return JSON-LD.
   */
  public function build(string $format = self::JSONLD, ?RouteMatchInterface $route_match = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): ?array;

}
