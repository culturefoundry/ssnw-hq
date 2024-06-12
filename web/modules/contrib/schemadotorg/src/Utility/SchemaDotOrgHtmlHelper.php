<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Utility;

/**
 * Helper class Schema.org html methods.
 */
class SchemaDotOrgHtmlHelper {

  /**
   * Convert Markdowm to HTML.
   *
   * @param string $markdown
   *   A string containing Markdown.
   *
   * @return string
   *   A string containing Markdown converted to HTMl.
   */
  public static function fromMarkdown(string $markdown): string {
    // Remove the table of contents.
    $markdown = preg_replace('/^.*?(Introduction\s+------------)/s', '$1', $markdown);

    if (!class_exists('\League\CommonMark\GithubFlavoredMarkdownConverter')) {
      return '<pre>' . $markdown . '</pre>';
    }

    // phpcs:ignore Drupal.Classes.FullyQualifiedNamespace.UseStatementMissing
    $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter();
    $html = $converter->convert($markdown)->getContent();

    // Remove <p> tags with <li> tags.
    $html = preg_replace('#<li>\s*<p>#m', '<li>', $html);
    $html = preg_replace('#</p>\s*</li>#m', '</li>', $html);

    // Tidy the HTML markup.
    // @see https://api.html-tidy.org/tidy/quickref_next.html
    if (class_exists('\tidy')) {
      $config = [
        'indent' => FALSE,
        'show-body-only' => TRUE,
        'output-xhtml' => TRUE,
        'wrap' => FALSE,
      ];
      $tidy = new \tidy();
      $tidy->parseString($html, $config, 'utf8');
      $tidy->cleanRepair();
      $html = tidy_get_output($tidy);
    }

    return trim($html);
  }

}
