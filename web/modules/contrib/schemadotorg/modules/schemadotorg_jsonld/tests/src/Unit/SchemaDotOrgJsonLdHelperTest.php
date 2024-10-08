<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Unit;

use Drupal\schemadotorg_jsonld\Utility\SchemaDotOrgJsonLdHelper;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\schemadotorg_jsonld\Utility\SchemaDotOrgJsonLdHelper
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdHelperTest extends UnitTestCase {

  /**
   * Tests SchemaDotOrgJsonLdHelper::AppendValue().
   *
   * @param array $data
   *   The array of JSON-LD data the value should be appended.
   * @param string $schema_property
   *   The specific Schema.org property.
   * @param mixed $value
   *   The value to be appended.
   * @param array $expected
   *   The expected JSON-LD data.
   *
   * @see SchemaDotOrgJsonLdHelper::AppendValue()
   *
   * @dataProvider providerAppendValue
   */
  public function testAppendValue(array $data, string $schema_property, mixed $value, array $expected): void {
    SchemaDotOrgJsonLdHelper::AppendValue($data, $schema_property, $value);
    $this->assertEquals($expected, $data);
  }

  /**
   * Data provider for testAppendValue().
   *
   * @see testAppendValue()
   */
  public function providerAppendValue(): array {
    $tests = [];

    $tests[] = [
      [
        '@type' => 'Thing',
        'name' => 'Thing',
      ],
      'alternateName',
      'other thing',
      [
        '@type' => 'Thing',
        'name' => 'Thing',
        'alternateName' => 'other thing',
      ],
    ];

    $tests[] = [
      [
        '@type' => 'Thing',
        'name' => 'Thing',
        'alternateName' => 'some thing',
      ],
      'alternateName',
      'other thing',
      [
        '@type' => 'Thing',
        'name' => 'Thing',
        'alternateName' => ['some thing', 'other thing'],
      ],
    ];

    $tests[] = [
      [
        '@type' => 'Thing',
        'name' => 'Thing',
        'alternateName' => ['some thing'],
      ],
      'alternateName',
      'other thing',
      [
        '@type' => 'Thing',
        'name' => 'Thing',
        'alternateName' => ['some thing', 'other thing'],
      ],
    ];

    return $tests;
  }

}
