<?php

namespace Drupal\content_model_documentation\Report;

/**
 * A report that shows all node content types and their counts.
 */
class VocabularyCount extends ReportBase implements ReportInterface, ReportTableInterface, ReportDiagramInterface {

  /**
   * The footer row.
   *
   * @var array
   */
  protected $footer = [];

  /**
   * {@inheritdoc}
   */
  public static function getReportTitle(): string {
    return 'Taxonomy: Vocabulary counts';
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
    return $this->t("This is a snapshot of this site's vocabulary types.");
  }

  /**
   * {@inheritdoc}
   */
  public function getCaption(): string {
    return $this->t('List of taxonomy vocabulary types and the number of each in use.');
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaderRow(): array {
    $header = [
      $this->t('Label'),
      $this->t('Id'),
      $this->t('Total'),
      $this->t('Published'),
      $this->t('Unpublished'),
    ];
    if ($this->config->get('taxonomy')) {
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
    $rows = $this->getData();
    if ($this->config->get('taxonomy')) {
      $rows = $this->addDocumentationColumn('taxonomy_term', '', $rows);
    }
    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  public function getCsvBodyRows(): array {
    // Will need to convert links to urls.
    $rows = $this->getData();
    if ($this->config->get('taxonomy')) {
      $rows = $this->addDocumentationColumn('taxonomy_term', '', $rows, TRUE);
    }

    return $rows;
  }

  /**
   * Gets the data for the table.
   *
   * @return array
   *   The data of the rows, but not including documentation.
   */
  protected function getData(): array {
    // Get label of content types.
    $label_content_types = $this->getContentTypeLabels();
    $bundles_counted = $this->getBundlePublishedCount();
    $bundles_counted = $this->addUnpublished($bundles_counted);
    $total = 0;
    $total_published = 0;
    $total_not_published = 0;
    foreach ($label_content_types as $bundle => $content_type_label) {
      /** @var int $publish */
      $publish = $bundles_counted[$bundle]['publish'] ?? 0;
      /** @var int $unpublish */
      $unpublish = $bundles_counted[$bundle]['unpublish'] ?? 0;
      $resultTable[$bundle] = [
        'label' => $content_type_label,
        'id' => $bundle,
        'total' => $publish + $unpublish,
        'publish' => $publish,
        'no_publish' => $unpublish,
      ];
      $total += $publish + $unpublish;
      $total_published += $publish;
      $total_not_published += $unpublish;
    }
    $vocabulary_count = count($resultTable);

    $footer = [];
    $footer['label'] = (string) $this->t('TOTAL');
    $footer['id'] = "{$vocabulary_count} {$this->t('Vocabularies')}";
    $footer['total'] = "{$total} {$this->t('terms')}";
    $footer['publish'] = "{$total_published} {$this->t('published terms')}";
    $footer['no-publish'] = "{$total_not_published} {$this->t('un-published terms')}";
    $this->footer = [$footer];

    return $resultTable;
  }

  /**
   * Gets the count of any vocabularies.
   *
   * @return array
   *   The array of vocabularies with a count.
   */
  protected function getVocabularyCount() {
    $bundles_counted = [];
    $query = $this->entityTypeManager->getStorage('taxonomy_term')->getAggregateQuery();
    $bundles = $query->accessCheck(FALSE)
      ->groupBy('vid')
      ->aggregate('tid', 'count')
      ->execute();
    /** @var array<string> $bundle */
    foreach ($bundles as $bundle) {
      $bundles_counted[$bundle['vid']] = $bundle['tid_count'];
    }
    return $bundles_counted;
  }

  /**
   * Gets an array of node content labels.
   *
   * @return array
   *   An array of node content labels.
   */
  protected function getContentTypeLabels() {
    $label_content_types = [];
    $types = $this->entityTypeManager->getStorage('taxonomy_vocabulary')->loadMultiple();
    foreach ($types as $key => $type) {
      $label_content_types[$key] = $type->label();
    }
    natcasesort($label_content_types);

    return $label_content_types;
  }

  /**
   * Gets the count of published nodes.
   *
   * @return array
   *   The array of bundles with a published count.
   */
  protected function getBundlePublishedCount() {
    $bundles_counted = [];
    $query = $this->entityTypeManager->getStorage('taxonomy_term')->getAggregateQuery();
    $publish_bundle = $query->accessCheck(FALSE)
      ->condition('status', '1')
      ->groupBy('vid')
      ->aggregate('tid', 'count')
      ->execute();
    /** @var array<string> $bundle */
    foreach ($publish_bundle as $bundle) {
      $bundles_counted[$bundle['vid']]['publish'] = $bundle['tid_count'];
    }
    return $bundles_counted;
  }

  /**
   * Adds the unpublished count column.
   *
   * @param array $bundles_counted
   *   The node content bundles and count data.
   *
   * @return array
   *   The original array with the unpublished column added.
   */
  protected function addUnpublished($bundles_counted) {
    $query = $this->entityTypeManager->getStorage('taxonomy_term')->getAggregateQuery();
    $unpublish_bundle = $query->accessCheck(FALSE)
      ->condition('status', '1', '!=')
      ->groupBy('vid')
      ->aggregate('tid', 'count')
      ->execute();
    /** @var array<string> $bundle */
    foreach ($unpublish_bundle as $bundle) {
      $bundles_counted[$bundle['vid']]['unpublish'] = $bundle['tid_count'];
    }

    return $bundles_counted;
  }

  /**
   * Builds the Mermaid string for the diagram.
   *
   * @return string
   *   The string that is the Mermaid Diagram.
   */
  protected function getDiagram(): string {
    $label_content_types = $this->getContentTypeLabels();
    // Sorting is largely irrelevant because mermaid will sort from high to low.
    // The reason for the sort is in the case of screen readers it reads raw.
    asort($label_content_types, SORT_NATURAL);
    $bundles = $this->getVocabularyCount();
    $bundle_count = count($bundles);
    $vars = ['@total_count' => $bundle_count];

    $title = $this->t('There are @total_count content types (bundles).', $vars);
    $mermaid = "pie showData title $title" . PHP_EOL;
    foreach ($label_content_types as $machine_name => $label_content_type) {
      $count = $bundles[$machine_name] ?? 0;
      $mermaid .= "  \"$label_content_type\": {$count}" . PHP_EOL;
    }

    return $mermaid;
  }

  /**
   * {@inheritdoc}
   */
  public function getDiagramList(): array {
    $diagrams = [
      'Content Types' => [
        'diagram' => $this->getDiagram(),
        'caption' => $this->t('All content bundle node counts.'),
        'key' => '',
      ],
    ];
    return $diagrams;
  }

  /**
   * Gets a render array for something to display above the table.
   *
   * @return array
   *   A drupal render array for the diagram.
   */
  protected function getPreReport(): array {
    return $this->buildDiagramPage(' ');
  }

}
