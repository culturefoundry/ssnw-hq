<?php

namespace Drupal\content_model_documentation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ModuleDocumentController.
 *
 *  Returns responses for Module Documentation routes.
 */
class ReportController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Symfony container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * Constructs a new ModuleDocumentationController.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container interface.
   */
  public function __construct(ContainerInterface $container) {

    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): ReportController {
    return new static(
      $container
    );
  }

  /**
   * Title callback.
   */
  public function getTitle($report_name, $alternate_format) {
    $report_class = $this->getReportClass($report_name);
    // Case race, first to evaluate TRUE wins.
    switch (TRUE) {
      case (empty($report_name)):
      case ($alternate_format === 'csv'):
        // CSV output can not have title.
        $title = '';
        break;

      case (!empty($report_class) && ($alternate_format !== 'csv')):
        $title = $report_class::getReportTitle();
        break;

      default:
        // Anything that made it this far, I don't know what it would be.
        $title = '';
        break;
    }

    return $title ?? '';
  }

  /**
   * Display /admin/reports/system/{report_name}/{alternate_format} page.
   *
   * Display /admin/reports/content-model/{report_name}/{alternate_format} page.
   *
   * @param string|null $report_name
   *   The snake_case_name of the report matches the class name (from the url).
   * @param string|null $alternate_format
   *   A different format of the report, like CSV (from the url).
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   Drupal build array
   */
  public function display($report_name, $alternate_format = NULL) {
    $report_class = $this->getReportClass($report_name);
    if (!empty($report_class)) {
      $report = $report_class::create($this->container);
    }

    // Case race.  First one to eval to TRUE wins.
    switch (TRUE) {
      case empty($report):
        throw new NotFoundHttpException();

      case empty($alternate_format):
        // There is no alternate format requested so build report.
        $type = $report->getReportType();
        return $report->buildPage($type, $report_name);

      case ($alternate_format === 'csv'):
        // Give them the CSV.
        return $report->buildPage('csv', $report_name);

      default:
        throw new NotFoundHttpException();
    }

  }

  /**
   * Take dashed path element and convert to camel class name format.
   *
   * @param mixed $string
   *   The string to convert.
   *
   * @return string
   *   The converted string.
   */
  protected function convertDashesToCamel($string): string {
    $string = str_replace('-', '', ucwords($string, '-'));
    return $string;
  }

  /**
   * Get's the fully name spaced class for a report.
   *
   * @param string $report_name
   *   The short/un-namespaced name of the report class to lookup.
   *
   * @return string
   *   The fully namespaced name of the report class if it exists. '' otherwise.
   */
  protected function getReportClass($report_name): string {
    $camel_report_name = $this->convertDashesToCamel($report_name);
    if (!empty($camel_report_name)) {
      $report_class = "\\Drupal\\content_model_documentation\\Report\\{$camel_report_name}";
      if (class_exists($report_class, TRUE)) {
        $class_name = $report_class;
      }
    }
    return $class_name ?? '';
  }

}
