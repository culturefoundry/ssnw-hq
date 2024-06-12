<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a breadcrumb builder for Schema.org.
 */
class SchemaDotOrgBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    $route_name = $route_match->getRouteName() ?? '';
    return ((bool) preg_match('/^(entity\.schemadotorg_mapping|schemadotorg\.settings)/', $route_name));
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

    $route_name = $route_match->getRouteName();
    switch ($route_name) {
      case 'entity.schemadotorg_mapping.add_form':
      case 'entity.schemadotorg_mapping.edit_form':
      case 'entity.schemadotorg_mapping.delete_form':
        $breadcrumb->addLink(Link::createFromRoute($this->t('Mappings'), 'entity.schemadotorg_mapping.collection'));
        break;

      case 'entity.schemadotorg_mapping_type.add_form':
      case 'entity.schemadotorg_mapping_type.edit_form':
      case 'entity.schemadotorg_mapping_type.delete_form':
        $breadcrumb->addLink(Link::createFromRoute($this->t('Mapping types'), 'entity.schemadotorg_mapping_type.collection'));
        break;
    }

    // This breadcrumb builder is based on a route parameter, and hence it
    // depends on the 'route' cache context.
    $breadcrumb->addCacheContexts(['route']);

    return $breadcrumb;
  }

}
