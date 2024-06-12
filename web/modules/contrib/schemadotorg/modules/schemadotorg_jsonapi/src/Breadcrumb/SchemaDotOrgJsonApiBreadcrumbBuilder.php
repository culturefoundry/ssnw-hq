<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi\Breadcrumb;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\schemadotorg\Breadcrumb\SchemaDotOrgBreadcrumbBuilder;

/**
 * Provides a breadcrumb builder for Schema.org JSON:API.
 */
class SchemaDotOrgJsonApiBreadcrumbBuilder extends SchemaDotOrgBreadcrumbBuilder {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return ($route_match->getRouteName() === 'schemadotorg_jsonapi.settings');
  }

}
