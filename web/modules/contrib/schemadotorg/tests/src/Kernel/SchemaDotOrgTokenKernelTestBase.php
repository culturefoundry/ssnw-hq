<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Tests\token\Functional\TokenTestTrait;

/**
 * Defines an abstract test base for Schema.org token kernel tests.
 */
abstract class SchemaDotOrgTokenKernelTestBase extends SchemaDotOrgEntityKernelTestBase {
  use TokenTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['token'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    DateFormat::create([
      'id' => 'fallback',
      'label' => 'Fallback',
      'pattern' => 'Y-m-d',
    ])->save();
  }

}
