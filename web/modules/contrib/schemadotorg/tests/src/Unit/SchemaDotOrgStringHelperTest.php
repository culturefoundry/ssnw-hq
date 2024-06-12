<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Unit;

use Drupal\schemadotorg\Utility\SchemaDotOrgStringHelper;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\schemadotorg\Utility\SchemaDotOrgStringHelper
 * @group schemadotorg
 */
class SchemaDotOrgStringHelperTest extends UnitTestCase {

  /**
   * Tests SchemaDotOrgStringHelper::getFirstSentence().
   *
   * @param string $string
   *   The string to run through SchemaDotOrgStringHelper::getFirstSentence().
   * @param string $expected
   *   The expected result from calling the function.
   *
   * @see SchemaDotOrgStringHelper::getFirstSentence()
   *
   * @dataProvider providerGetFirstSentence
   */
  public function testGetFirstSentence(string $string, string $expected): void {
    $result = SchemaDotOrgStringHelper::getFirstSentence($string);
    $this->assertEquals($expected, $result, serialize($string));
  }

  /**
   * Data provider for testGetFirstSentence().
   *
   * @see testGetFirstSentence()
   */
  public function providerGetFirstSentence(): array {
    $tests = [];
    $tests[] = [
      'This is a test. This is a test.',
      'This is a test.',
    ];
    $tests[] = [
      'A specific question - e.g. from a user seeking answers online, or collected in a Frequently Asked Questions (FAQ) document.',
      'A specific question - e.g. from a user seeking answers online, or collected in a Frequently Asked Questions (FAQ) document.',
    ];
    $tests[] = [
      'A description of the job location (e.g. TELECOMMUTE for telecommute jobs).',
      'A description of the job location (e.g. TELECOMMUTE for telecommute jobs).',
    ];
    $tests[] = [
      'Event type: Exhibition event, e.g. at a museum, library, archive, tradeshow, ...',
      'Event type: Exhibition event, e.g. at a museum, library, archive, tradeshow, ...',
    ];
    $tests[] = [
      'Text representing an XPath (typically but not necessarily version 1.0).',
      'Text representing an XPath (typically but not necessarily version 1.0).',
    ];
    $tests[] = [
      'True if this item\'s name is a proprietary/brand name (vs. generic name).',
      'True if this item\'s name is a proprietary/brand name (vs. generic name).',
    ];
    $tests[] = [
      'A <a href="https://schema.org/FAQPage">FAQPage</a> is a <a href="https://schema.org/WebPage">WebPage</a> presenting one or more "<a href="https://en.wikipedia.org/wiki/FAQ">Frequently asked questions</a>" (see also <a href="https://schema.org/QAPage">QAPage</a>).',
      'A <a href="https://schema.org/FAQPage">FAQPage</a> is a <a href="https://schema.org/WebPage">WebPage</a> presenting one or more "<a href="https://en.wikipedia.org/wiki/FAQ">Frequently asked questions</a>" (see also <a href="https://schema.org/QAPage">QAPage</a>).',
    ];
    $tests[] = [
      'This is a link (source Wikipedia.org).',
      'This is a link (source Wikipedia.org).',
    ];
    $tests[] = [
      'A discrete unit of inheritance which affects one or more biological traits (Source: <a href="https://en.wikipedia.org/wiki/Gene">https://en.wikipedia.org/wiki/Gene</a>). Examples include FOXP2 (Forkhead box protein P2), SCARNA21 (small Cajal body-specific RNA 21), A- (agouti genotype).',
      'A discrete unit of inheritance which affects one or more biological traits (Source: <a href="https://en.wikipedia.org/wiki/Gene">https://en.wikipedia.org/wiki/Gene</a>).',
    ];
    $tests[] = [
      'A meeting room, conference room, or conference hall is a room provided for singular events such as business conferences and meetings (source: Wikipedia, the free encyclopedia, see http://en.wikipedia.org/wiki/Conference_hall).',
      'A meeting room, conference room, or conference hall is a room provided for singular events such as business conferences and meetings.',
    ];
    $tests[] = [
      'An image of the item. This can be a <a href="https://schema.org/URL">URL</a> or a fully described <a href="https://schema.org/ImageObject">ImageObject</a>.',
      'An image of the item.',
    ];
    return $tests;
  }

}
