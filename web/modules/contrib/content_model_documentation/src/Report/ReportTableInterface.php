<?php

namespace Drupal\content_model_documentation\Report;

/**
 * Required methods for extensions of ReportBase that report with tables.
 *
 * May optionally provide getPreReport() or getPostReport() to return a render
 * array to go before or after the table.
 */
interface ReportTableInterface extends ReportInterface {

  /**
   * Get the header for the report.
   *
   * @return array
   *   A flat array of header titles/cells for each column.
   */
  public function getHeaderRow(): array;

  /**
   * Get the footer for the report.
   *
   * @return array
   *   A flat array of header titles/cells for each column. Return [] if none.
   */
  public function getFooterRow(): array;

  /**
   * Get the table caption for the report.
   *
   * @return string
   *   A string to be used as the summary for a table.
   */
  public function getCaption(): string;

  /**
   * Get an array of arrays that makeup the table body.
   *
   * @return array
   *   Each element in the array represents a row in the table.
   */
  public function getTableBodyRows(): array;

  /**
   * Get an array of arrays that makeup the CSV rows.
   *
   * This is usually just a pass through of getTableBody(), however it allows
   * for additional processing if the CSV is differnt from the table.
   * Example: a table has full links, but the CSV would just have a URLs.
   *
   * @return array
   *   Each element in the array represents a row in the CSV. Return empty
   *   string if there is no CSV.
   */
  public function getCsvBodyRows(): array;

}
