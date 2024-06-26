<?php

namespace Drupal\content_model_documentation\Report;

/**
 * Required methods for extensions of ReportBase that report with diagrams.
 */
interface ReportDiagramInterface extends ReportInterface {

  /**
   * Getter for the list of diagrams to be rendered.
   *
   * @return array
   *   Array of diagrams containing a caption, a key, and diagram.
   *
   *   data  [
   *   'Diagram name' => [
   *      'diagram' => 'mermaid code',
   *      'caption' => 'A required caption for the diagram.'
   *      'key' => 'Html for key or mermaid markup if the key is also mermaid.'
   *      'errors => [ Error stings to output] (optional),
   *    ],
   *   ]
   */
  public function getDiagramList(): array;

}
