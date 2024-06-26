<?php

namespace Drupal\content_model_documentation\Report;

/**
 * Required methods for extensions of ReportBase.
 */
interface ReportInterface {

  /**
   * Gets the type of report.
   *
   * @return string
   *   A valid report type [table, csv, htmlblob] supported by pageBuild().
   */
  public function getReportType();

  /**
   * Gets the title of the report.
   *
   * @return string
   *   The title of the report.
   */
  public static function getReportTitle();

  /**
   * Get the description for the report.
   *
   * @return string
   *   An html string to be used as the summary. [<h2> and <p> are allowed.]
   */
  public function getDescription(): string;

}
