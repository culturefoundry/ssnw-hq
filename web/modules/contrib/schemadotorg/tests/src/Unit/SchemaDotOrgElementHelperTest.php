<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Unit;

use Drupal\schemadotorg\Utility\SchemaDotOrgElementHelper;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\schemadotorg\Utility\SchemaDotOrgElementHelper
 * @group schemadotorg
 */
class SchemaDotOrgElementHelperTest extends UnitTestCase {

  /**
   * Tests SchemaDotOrgElementHelper::setElementParents().
   *
   * @covers ::setElementParents
   */
  public function testsSetElementParents(): void {
    $elements = [
      'textfield' => ['#type' => 'textfield'],
      'empty' => [],
    ];
    SchemaDotOrgElementHelper::setElementParents(
      $elements,
      ['one', 'two', 'three']
    );
    $this->assertEquals(
      ['one', 'two', 'three', 'textfield'],
      $elements['textfield']['#parents']
    );
    $this->assertEquals(
      ['one', 'two', 'three', 'empty'],
      $elements['empty']['#parents']
    );
  }

}
