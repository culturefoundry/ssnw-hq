<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Traits;

/**
 * Provides convenience methods for Schema.org JSON-LDassertions.
 */
trait SchemaDotOrgJsonLdTestTrait {

  /**
   * Format ISO 8601 date time value for JSON-LD.
   */
  protected function formatDateTime(int $time): string {
    return $this->dateFormatter->format($time, 'custom', 'Y-m-d H:i:s P');
  }

}
