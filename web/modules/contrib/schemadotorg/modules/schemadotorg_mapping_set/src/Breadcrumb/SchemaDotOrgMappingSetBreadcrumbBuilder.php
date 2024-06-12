<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_mapping_set\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a breadcrumb builder for Schema.org mapping set.
 */
class SchemaDotOrgMappingSetBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    $route_name = $route_match->getRouteName() ?? '';
    return ((bool) preg_match('/^schemadotorg_mapping_set\./', $route_name));
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match): Breadcrumb {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
    $breadcrumb->addLink(Link::createFromRoute($this->t('Administration'), 'system.admin'));
    $breadcrumb->addLink(Link::createFromRoute($this->t('Configuration'), 'system.admin_config'));
    $breadcrumb->addLink(Link::createFromRoute($this->t('Schema.org'), 'schemadotorg'));
    if (in_array($route_match->getRouteName(), ['schemadotorg_mapping_set.confirm_form', 'schemadotorg_mapping_set.details'])) {
      $breadcrumb->addLink(Link::createFromRoute($this->t('Mapping sets'), 'schemadotorg_mapping_set.overview'));
    }

    // This breadcrumb builder is based on a route parameter, and hence it
    // depends on the 'route' cache context.
    $breadcrumb->addCacheContexts(['route']);

    return $breadcrumb;
  }

}
