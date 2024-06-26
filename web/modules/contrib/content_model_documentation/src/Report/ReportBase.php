<?php

namespace Drupal\content_model_documentation\Report;

use Drupal\content_model_documentation\CMDocumentConnectorTrait;
use Drupal\content_model_documentation\FieldsReportManagerInterface;
use Drupal\content_model_documentation\MermaidTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Required methods for extensions of ReportBase.
 */
abstract class ReportBase implements ContainerInjectionInterface {

  use CMDocumentConnectorTrait;
  use MermaidTrait;
  use StringTranslationTrait;

  /**
   * Module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;


  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The database connection.
   *
   * @var \Drupal\content_model_documentation\FieldsReportManagerInterface
   */
  protected $reportManager;

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
   *   The field report manager.  (Has some helpful field related methods.)
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    Connection $database,
    EntityTypeManagerInterface $entity_type_manager,
    LanguageManagerInterface $language_manager,
    RendererInterface $renderer,
    RequestStack $request_stack,
    FieldsReportManagerInterface $field_report_manager
    ) {
    $this->config = $config_factory->get('content_model_documentation.settings');
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
    $this->renderer = $renderer;
    $this->requestStack = $request_stack;
    // There are a bunch of helpful functions in here.
    $this->reportManager = $field_report_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Unfortunately the list gets pretty long because the child classes
    // would have to fully override parent. The create() functions can't be
    // chained like the __constructor() functions can.
    return new static(
      $container->get('config.factory'),
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('renderer'),
      $container->get('request_stack'),
      $container->get('content_model_documentation.fields_report'),
    );
  }

  /**
   * Gets the id / class name of the report.
   *
   * @return string
   *   The class name of the report.
   */
  public function getReportId() {
    return get_class($this);
  }

  /**
   * This is the function that gets called by the ReportController.
   *
   * @param string $type
   *   The type of report (table, htmlblob, csv).
   * @param string $report_name
   *   The name of the report coming from the url path element.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   Render array for most things, Response for csv download.
   */
  public function buildPage($type, $report_name) {
    switch ($type) {
      case 'table':
        $render = [];
        $pre_report = method_exists($this, 'getPreReport') ? $this->getPreReport() : [];
        if ($pre_report) {
          $render[] = $pre_report;
          $render[] = $this->getHrRenderArray();
        }
        $render[] = $this->getTable();
        $post_report = method_exists($this, 'getPostReport') ? $this->getPostReport() : [];
        if ($post_report) {
          $render[] = $this->getHrRenderArray();
          $render[] = $post_report;
        }

        break;

      case 'csv':
        $render = $this->getCsv($report_name);
        break;

      case 'htmlblob':
        $render = $this->getHtml();
        break;

      case 'diagram':
        $render = $this->buildDiagramPage();
        break;

      default:
        break;
    }

    return $render;
  }

  /**
   * Get the markup for table report.
   *
   * @throws \Exception
   *   Thrown if get table is called by a class without ReportTableInterface.
   *
   * @return array
   *   A render array of html markup for the report.
   */
  protected function getTable(): array {
    if (!$this instanceof ReportTableInterface) {
      throw new \Exception("In order to call pageBuild('table') the class must implement the ReportTableInterface.");
    }
    $this->validateHeaderBodyParity();
    $this->validateCaption();
    $build = [];
    if (!empty($this->getDescription())) {
      $build['description'] = [
        '#type' => '#markup',
        '#prefix' => '<div id="report-description"',
        '#markup' => $this->getDescription(),
        '#suffix' => '</div>',
      ];
    }
    if (!empty($this->getCsvBodyRows())) {
      // A CSV is available so create link, but is needs to be relative.
      // Drupal is kind of funny about this because the page you are on is not
      // a directory, so location relative (./csv) does not append to the page
      // it appends to the directory.  So it needs a full url.
      $current_page = $url = $this->requestStack->getCurrentRequest()->getRequestUri();
      $url = Url::fromUserInput("{$current_page}/csv");
      $build['csv_link'] = [
        '#type' => 'link',
        '#title' => $this->t('Download CSV'),
        '#url' => $url,
        '#attributes' => [
          'class' => 'feed-icon',
        ],
        '#prefix' => '<div class="csv-feed views-data-export-feed">',
        '#suffix' => '</div>',
      ];

    }
    $build['table'] = [
      '#type' => 'table',
      '#header' => $this->getHeaderRow(),
      '#rows' => $this->getTableBodyRows(),
      '#footer' => $this->getFooterRow(),
      '#empty' => $this->t('No table content found.'),
      '#caption' => $this->getCaption(),
      '#attributes' => [
        'class' => ['sortable'],
      ],
      '#attached' => ['library' => ['content_model_documentation/sortable-init']],
    ];
    return [
      '#type' => '#markup',
      '#sorted' => TRUE,
      '#markup' => $this->renderer->render($build),
    ];
  }

  /**
   * Get the response for a CSV report.
   *
   * @param string $report_name
   *   The machine name of the report.
   *
   * @throws \Exception
   *   Thrown if get table is called by a class without ReportTableInterface.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   A response object for direct download.
   */
  protected function getCsv(string $report_name) {
    if (!$this instanceof ReportTableInterface) {
      throw new \Exception("In order to call pageBuild('csv') the class must implement the ReportTableInterface.");
    }
    $this->validateHeaderBodyParity();
    $response = new Response();
    $output = "\xEF\xBB\xBF";
    $output .= 'Nr, URL, Created, Feedback, Message, Inspected' . "\n";
    // Output up to 10MB is kept in memory, if it becomes bigger it will write
    // to a temporary file.
    $csv = fopen('php://temp/maxmemory:' . (10 * 1024 * 1024), 'r+');
    fputcsv($csv, $this->getHeaderRow());
    $rows = $this->getCsvBodyRows();
    foreach ($rows as $row) {
      fputcsv($csv, $row);
    }
    rewind($csv);
    $output = stream_get_contents($csv);
    $response->setContent($output);
    $response->headers->set("Content-Type", "text/csv; charset=UTF-8");
    $response->headers->set("Content-Disposition", "attachment; filename={$report_name}.csv");

    return $response;
  }

  /**
   * Get the markup for a blobby html report.
   *
   * @throws \Exception
   *   Thrown if get table is called by a class without ReportTableInterface.
   *
   * @return array
   *   A render array of html markup for the report.
   */
  protected function getHtml(): array {
    if (!$this instanceof ReportTableInterface) {
      throw new \Exception("In order to call pageBuild('table') the class must implement the ReportTableInterface.");
    }
    $this->validateHeaderBodyParity();
    $this->validateCaption();
    $build = [];
    if (!empty($this->getDescription())) {
      $build['description'] = [
        '#type' => '#markup',
        '#prefix' => '<div id="report-description"',
        '#markup' => $this->getDescription(),
        '#suffix' => '</div>',
      ];
    }
    $build['table'] = [
      '#type' => 'table',
      '#header' => $this->getHeaderRow(),
      '#rows' => $this->getTableBodyRows(),
      '#empty' => $this->t('No table content found.'),
      '#caption' => $this->getCaption(),
      '#attributes' => [
        'class' => ['sortable'],
      ],
      '#attached' => ['library' => 'content_model_documentation/sortable-init'],
    ];
    return [
      '#type' => '#markup',
      '#sorted' => TRUE,
      '#markup' => $this->renderer->render($build),
    ];
  }

  /**
   * Build a set of diagrams.
   *
   * @param string $description
   *   Optional description to override interface. Used when calling directly.
   *
   * @throws \Exception
   *   Thrown if called by a class without ReportDiagramInterface.
   *
   * @return array
   *   A render array of html markup for the diagram based report.
   */
  protected function buildDiagramPage(string $description = '') : array {
    if (!$this instanceof ReportDiagramInterface) {
      throw new \Exception("In order to call pageBuild('diagram') the class must implement the ReportDiagramInterface.");
    }
    $build = [];
    $description = !empty($description) ? $description : $this->getDescription();
    if (!empty($description)) {
      $build['description'] = [
        '#type' => '#markup',
        '#prefix' => '<div id="report-diagram-description"',
        '#markup' => $description,
        '#suffix' => '</div>',
      ];
    }
    $build = $this->getDiagrams($build);
    return $build;
  }

  /**
   * Get the markup for a series of diagrams.
   *
   * @param array $build
   *   A render array to add onto.
   *
   * @throws \Exception
   *   Thrown if get table is called by a class without ReportTableInterface.
   *
   * @return array
   *   A render array of html markup for the report.
   */
  protected function getDiagrams($build) {

    $diagrams = $this->getDiagramList();
    foreach ($diagrams as $name => $diagram_item) {
      // Each diagram_item must contain a diagram, caption, and key.
      $this->validateCaption($diagram_item['caption']);
      $build[] = [
        '#theme' => 'mermaid_diagram',
        '#mermaid' => $diagram_item['diagram'],
        '#title' => $name,
        '#caption' => $diagram_item['caption'],
        '#attached' => ['library' => 'mermaid_diagram_field/diagram'],
        '#key' => $diagram_item['key'],
        '#show_code' => TRUE,
      ];
      if (!empty($diagram_item['errors'])) {
        $build[] = $this->getRenderErrors($diagram_item['errors']);
      }

    }
    return $build;
  }

  /**
   * Validate that header and row counts match.
   *
   * @throws \Exception
   *   When there is a mismatch.
   */
  protected function validateHeaderBodyParity(): void {
    $count_of_header_columns = count($this->getHeaderRow());
    $rows = $this->getTableBodyRows();
    foreach ($rows as $row_num => $row) {
      if ($count_of_header_columns !== count($row)) {
        // Report the offending row like a human counts, not like a machine.
        $human_row = ++$row_num;
        throw new \Exception("There is a mismatch between the number of column headers and data in row $human_row.");
      }
    }
  }

  /**
   * Validates a caption exists.
   *
   * @param string|null $caption
   *   A caption to use instead of calling for one.
   *
   * @throws \Exception
   *   If the getCaption method is not defined or returns empty.
   */
  protected function validateCaption($caption = NULL): void {
    if (empty($caption) && (!method_exists($this, 'getCaption') || empty(trim($this->getCaption())))) {
      throw new \Exception("A caption is required. getCaption() must be defined, and can not be empty.");
    }
  }

  /**
   * Adds a documentation column to the rows.
   *
   * @param string $type
   *   The the type of entity to lookup documentation for.
   * @param string $field
   *   The field or submodule of the entity to lookup documentation for.
   * @param array $rows
   *   The rows of a table.
   * @param bool $csv
   *   Boolean indicating it is for a CSV and needs a complete URL.
   *
   * @return array
   *   The original array with a column of documentation appended to the right.
   */
  protected function addDocumentationColumn(string $type, string $field, array $rows, bool $csv = FALSE): array {
    foreach ($rows as $bundle => $row) {
      if ($csv) {
        // Since this is a CSV we include the full url.
        $site_address = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        $link = $this->getVerifiedCmDocumentPath($type, $bundle, $field, $site_address);
        $row['documentation'] = $link;
      }
      else {
        if ($type === 'module' && ($row['project'] instanceof Link)) {
          $project_name = $row['project']->getText();
          $row['documentation'] = $this->getCmDocumentLink($type, $project_name, $field);
        }
        elseif (empty($row['project'])) {
          $row['documentation'] = $this->getCmDocumentLink($type, $bundle, $field);
        }
        else {
          $row['documentation'] = '';
        }
      }
      $rows[$bundle] = $row;
    };
    return $rows;
  }

  /**
   * Get Hr render array.
   *
   * @return array
   *   A markup render array containing a horizontal rule.
   */
  protected function getHrRenderArray(): array {
    return [
      '#type' => '#markup',
      '#markup' => '<hr />',
    ];
  }

}
