<?php

namespace Drupal\mermaid_diagram_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the Mermaid Diagram formatter.
 *
 * @FieldFormatter(
 *   id = "mermaid_diagram_formatter",
 *   label = @Translation("Mermaid diagram"),
 *   field_types = {
 *     "mermaid_diagram"
 *   }
 * )
 */
class MermaidDiagramFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = [
        '#theme' => 'mermaid_diagram',
        '#mermaid' => $item->diagram,
        '#title' => $item->title,
        '#caption' => $item->caption,
        '#attached' => ['library' => 'mermaid_diagram_field/diagram'],
        '#key' => $item->key,
        '#show_code' => $item->show_code,
      ];
    }

    return $element;
  }

}
