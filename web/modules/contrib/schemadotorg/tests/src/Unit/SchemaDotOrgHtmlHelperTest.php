<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Unit;

use Drupal\schemadotorg\Utility\SchemaDotOrgHtmlHelper;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\schemadotorg\Utility\SchemaDotOrgHtmlHelper
 * @group schemadotorg
 */
class SchemaDotOrgHtmlHelperTest extends UnitTestCase {

  /**
   * Tests SchemaDotOrgHtmlHelper::fromMarkdown().
   *
   * @param string $string
   *   The string to run through SchemaDotOrgHtmlHelper::fromMarkdown().
   * @param string $expected
   *   The expected result from calling the function.
   *
   * @see SchemaDotOrgHtmlHelper::fromMarkdown()
   *
   * @dataProvider providerFromMarkdown
   */
  public function testFromMarkdown(string $string, string $expected): void {
    $result = SchemaDotOrgHtmlHelper::fromMarkdown($string);
    $this->assertEquals($expected, $result, serialize($string));
  }

  /**
   * Data provider for testFromMarkdown().
   *
   * @see testFromMarkdown()
   */
  public function providerFromMarkdown(): array {
    $tests = [];
    // Check converting some text.
    $tests[] = [
      'Some text',
      '<p>Some text</p>',
    ];
    // Check converting URLs.
    $tests[] = [
      'https://drupal.org',
      '<p><a href="https://drupal.org">https://drupal.org</a></p>',
    ];
    // Check removing the table of contents.
    $tests[] = [
      'Table of contents
-----------------
* Introduction
Introduction
------------
Some text',
      '<h2>Introduction</h2>
<p>Some text</p>',
    ];
    // Check remove <p> tags with <li> tags.
    $tests[] = [
      '- Item 1

- Item 2
',
      '<ul>
<li>Item 1</li>
<li>Item 2</li>
</ul>',
    ];
    // Check convert <p><code> tags to <pre> tags.
    $tests[] = [
      '```text
Some code
```
',
      '<pre><code class="language-text">Some code
</code></pre>',
    ];
    $tests[] = [
      '
    Some code
',
      '<pre><code>Some code
</code></pre>',
    ];

    return $tests;
  }

}
