<?php

namespace Drupal\content_model_documentation\Report;

use Drupal\content_model_documentation\CMDocumentConnectorTrait;
use Drupal\content_model_documentation\DocumentableModules;
use Drupal\content_model_documentation\FieldsReportManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * A report that shows all node content types and their counts.
 */
class EnabledModules extends ReportBase implements ReportInterface, ReportTableInterface {

  use CMDocumentConnectorTrait;
  use StringTranslationTrait;

  /**
   * The DocumentableModules service.
   *
   * @var Drupal\content_model_documentation\DocumentableModules
   */
  protected $documentableModules;

  /**
   * The footer row.
   *
   * @var array
   */
  protected $footer = [];

  /**
   * Constructor for report base.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The symfony request stack.
   * @param \Drupal\content_model_documentation\FieldsReportManagerInterface $field_report_manager
   *   The field report manager.
   * @param \Drupal\content_model_documentation\DocumentableModules $documentable_modules
   *   The documentable modules service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    Connection $database,
    EntityTypeManagerInterface $entity_type_manager,
    LanguageManagerInterface $language_manager,
    RendererInterface $renderer,
    RequestStack $request_stack,
    FieldsReportManagerInterface $field_report_manager,
    DocumentableModules $documentable_modules
    ) {
    parent::__construct($config_factory, $database, $entity_type_manager, $language_manager, $renderer, $request_stack, $field_report_manager);
    $this->documentableModules = $documentable_modules;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Unfortunately the list gets pretty long because the child classes
    // would have to fully override parent. The create() functions can't be
    // chained like the __constructor() functions can.
    return new static(
      // These are the ReportBase arguments.
      $container->get('config.factory'),
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('renderer'),
      $container->get('request_stack'),
      $container->get('content_model_documentation.fields_report'),
      // These are the arguments custom to this report.
      $container->get('content_model_documentation.documentable.modules'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function getReportTitle(): string {
    return 'Enabled modules';
  }

  /**
   * {@inheritdoc}
   */
  public function getReportType(): string {
    return 'table';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getCaption(): string {
    return $this->t('This is list of all the enabled modules on the site, with links to help and documentation.');
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaderRow(): array {
    $header = [
      $this->t('Module name'),
      $this->t('Machine name'),
      $this->t('Description'),
      $this->t('Project'),
      $this->t('Help'),
    ];
    if ($this->config->get('modules')) {
      array_push($header, $this->t('Documentation'));
    }
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function getFooterRow(): array {
    return $this->footer;
  }

  /**
   * {@inheritdoc}
   */
  public function getTableBodyRows(): array {
    $rows = $this->documentableModules->getEnabledReporting();
    $enabled_module_count = count($rows);
    $custom_module_count = $this->countCustomModules($rows);
    $core_module_count = $this->countCoreModules($rows);
    $contrib_module_count = $this->countContribModules($rows);

    $this->footer = [
      [
        'total' => $this->t('@count enabled modules.', ['@count' => $enabled_module_count]),
        'core' => $this->t('@count Core modules.', ['@count' => $core_module_count]),
        'contrib' => $this->t('@count Contributed modules.', ['@count' => $contrib_module_count]),
        'custom' => $this->t('@count Custom modules.', ['@count' => $custom_module_count]),
      ],
    ];

    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  public function getCsvBodyRows(): array {
    // Will need to convert links to urls.
    $site_address = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    $rows = $this->documentableModules->getEnabledReporting(TRUE, $site_address);
    return $rows;
  }

  /**
   * Counts the number of custom / local modules.
   *
   * @param array $modules
   *   The rows from the module report.
   *
   * @return int
   *   The number of items counted
   */
  protected function countCustomModules(array $modules) : int {
    $count = 0;
    $custom_text = (string) $this->t("Custom, see your repository.");
    foreach ($modules as $module) {
      if ($module['project'] == $custom_text) {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Counts the number of Core modules.
   *
   * @param array $modules
   *   The rows from the module report.
   *
   * @return int
   *   The number of items counted
   */
  protected function countCoreModules(array $modules) : int {
    $count = 0;
    foreach ($modules as $module) {
      if ($module['project'] === "core") {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Counts the number of custom / local modules.
   *
   * @param array $modules
   *   The rows from the module report.
   *
   * @return int
   *   The number of items counted
   */
  protected function countContribModules(array $modules) : int {
    $count = 0;
    $custom_text = (string) $this->t("Custom, see your repository.");
    foreach ($modules as $module) {
      if ($module['project'] != $custom_text  && $module['project'] !== "core") {
        $count++;
      }
    }
    return $count;
  }

}
