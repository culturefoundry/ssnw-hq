<?php

namespace Drupal\content_model_documentation;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * A trait to provide some Mermaid helpers.
 */
trait MermaidTrait {
  use StringTranslationTrait;

  /**
   * Wraps the contents in a mermaid shape.
   *
   * @param string $content
   *   The content to go inside the shape.
   * @param string $shape
   *   The shape to use as the wrapper.  Defaults to rectangle.
   *
   * @return string
   *   Wrapped content.
   */
  public function wrapMermaidShape($content, $shape = 'rectangle'): string {
    $item = '';
    if (!empty($content)) {
      $wrapper = $this->getShape($shape);
      $item = "{$wrapper[0]}\"{$content}\"{$wrapper[1]}";
    }
    return $item;
  }

  /**
   * Gets an array of shape wrapper arrays.  Each containing an open and close.
   *
   * @return array
   *   Mermaid shape wrappers.
   *
   * @see https://mermaid.js.org/syntax/flowchart.html
   */
  public function getShapes(): array {
    return [
      'rectangle' => ['[', ']'],
      'circle' => ['((', '))'],
      'trapezoid' => ['[/', '\]'],
      'parallelogram' => ['[/', '/]'],
      'hexagon' => ['{{', '}}'],
      'flag' => ['>', ']'],
      'rounded rectangle' => ['(', ')'],
      'double circle' => ['(((', ')))'],
      'parallelogram alt.' => ['[\\', '\\]'],
      'rhombus' => ['{', '}'],
      'trapezoid alt.' => ['[\\', '/]'],
      'flag alt.' => ['[', ']'],
      'rectangle subroutine' => ['[[', ']]'],
    ];
  }

  /**
   * Gets the pattern for a shape if it exists, rectangle if it doesn't.
   *
   * @param string $shape
   *   The name of a shape to look get.
   *
   * @return array
   *   An array containing the opening and closing of the shape wrapper.
   */
  public function getShape($shape): array {
    $item = $this->getShapes()[$shape] ?? $this->getShapes()['rectangle'];
    return $item;
  }

  /**
   * Wraps a string of entries in a subgraph with title.
   *
   * @param string $title
   *   The title of the subgraph.
   * @param string $content
   *   The other mermaid items to wrap in the subgraph.
   * @param bool $showtitle
   *   Set to FALSE if there should be no title.
   *
   * @return string
   *   The mermaid subgraph.
   */
  public function wrapSubgraph($title, $content, $showtitle = TRUE): string {
    $subgraph = ($showtitle) ? "subgraph \"{$title}\"\n" : "subgraph \"{$title}\"[ ]\n";
    $subgraph .= $content;
    $subgraph .= "  end\n";

    return $subgraph;
  }

  /**
   * Creates an arrow connector with optional label.
   *
   * @param string $from_id
   *   The id for the shape at the tail of the arrow.
   * @param string $to_id
   *   The id for the shape at the head of the arrow.
   * @param string $label
   *   The optional label for the arrow.
   *
   * @return string
   *   The mermaid code for an arrow connector.
   */
  public function makeArrow(string $from_id, string $to_id, string $label = ''): string {
    if (empty($label)) {
      return "  {$from_id} --> {$to_id}\n";
    }
    return "  {$from_id} -->|\"$label\"|{$to_id}\n";
  }

  /**
   * Highlight a shape.  Use this to draw focus to a specific element.
   *
   * @param string $shape_id
   *   The id of the shape element to highlight.
   *
   * @return string
   *   The mermaid style to make the highlight.
   */
  public function highlightShape(string $shape_id): string {
    return "style $shape_id fill:#ffaacc,stroke:#333,stroke-width:4px;\n";
  }

  /**
   * Renders the raw mermaid code, usually for copy paste.
   *
   * @param string $mermaid_code
   *   The string used to create the mermaid diagram.
   *
   * @return array
   *   A renderable array of markup.
   */
  public function getRenderRaw($mermaid_code): array {
    if (!empty($mermaid_code)) {
      return [
        '#type' => 'details',
        '#title' => $this->t('Mermaid graph code'),
        '#markup' => "<pre>\n <code>\n $mermaid_code \n</code>\n </pre>\n",
      ];
    }
    return [];
  }

  /**
   * Renders the raw mermaid code, usually for copy paste.
   *
   * @param array $errors
   *   An array containing errors to report.
   *
   * @return array
   *   A renderable array of markup.
   */
  public function getRenderErrors(array $errors): array {
    if (!empty($errors)) {
      $message = implode(PHP_EOL, $errors);
      $count = count($errors);
      return [
        '#type' => 'details',
        '#title' => $this->t('Errors found') . "($count)" ,
        '#markup' => "<pre>\n <code>\n{$message} \n</code>\n </pre>\n",
      ];
    }
    return [];
  }

  /**
   * Returns mermaid 'key' of shapes with names.
   *
   * @param array $blocks
   *   Shapes and names of the blocks. ['rectangle' => 'Label',].
   *
   * @return string
   *   Mermaid md to show entity names and shapes.
   */
  public function key(array $blocks): string {
    if (empty($blocks)) {
      return '';
    }
    $output = "flowchart LR\n";
    $subgraph = '';
    foreach ($blocks as $shape => $state_type) {
      $subgraph .= "    {$shape}{$this->wrapMermaidShape($state_type, $shape)}\n";
    }
    return $output . $this->wrapSubgraph("identifier", $subgraph, FALSE);
  }

}
