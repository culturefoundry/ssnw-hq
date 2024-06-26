<?php

namespace Drupal\content_model_documentation\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\field_ui\FieldUI;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for our custom routes.
 */
class FieldsController extends ControllerBase {

  /**
   * Our custom service.
   *
   * @var \Drupal\content_model_documentation\FieldsReportInterface
   */
  protected $fieldsReport;

  /**
   * The entity bundle information service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $bundleInfo;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->fieldsReport = $container->get('content_model_documentation.fields_report');
    $instance->bundleInfo = $container->get('entity_type.bundle.info');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Title callback.
   */
  public function fieldDetailsTitle(string $entity_type, string $field) {
    if ($definition = $this->fieldsReport->getFieldDefinition($entity_type, $field)) {
      return $definition->getLabel();
    }

    return $this->t('Field details');
  }

  /**
   * Display details about a given field definition.
   */
  public function fieldDetails(string $entity_type, string $field) {
    $definition = $this->fieldsReport->getFieldDefinition($entity_type, $field);
    if (!$definition) {
      return ['#markup' => $this->t('Field not found')];
    }

    $values = $definition->toArray();

    // Display bundles.
    if (method_exists($definition, 'getBundles')) {
      $active_on_bundles = [];
      foreach (array_values($definition->getBundles()) as $bundle) {
        if ($route_info = FieldUI::getOverviewRouteInfo($entity_type, $bundle)) {
          $bundles = $this->bundleInfo->getBundleInfo($entity_type);
          $link = Link::fromTextAndUrl($bundles[$bundle]['label'], $route_info)->toRenderable();
          $active_on_bundles[] = $this->renderer->render($link);
        }
      }

      $values['bundles'] = implode(', ', $active_on_bundles);
    }

    $rows = [];
    foreach ($values as $key => $value) {
      if ($value === NULL || $key == '_core') {
        continue;
      }
      if (\is_array($value)) {
        $value = Json::encode($value);
      }
      $rows[] = [$key, ['data' => ['#markup' => $value]]];
    }

    $build = [
      '#theme' => 'table',
      '#header' => [$this->t('Key'), $this->t('Value')],
      '#rows' => $rows,
    ];

    return $build;
  }

}
