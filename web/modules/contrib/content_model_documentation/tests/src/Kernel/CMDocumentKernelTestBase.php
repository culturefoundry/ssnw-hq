<?php

declare(strict_types=1);

namespace Drupal\Tests\content_model_documentation\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\content_model_documentation\Traits\CMDocumentTestTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Defines a base kernel test for CMDocument tests.
 */
abstract class CMDocumentKernelTestBase extends KernelTestBase {

  use UserCreationTrait;
  use CMDocumentTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'content_model_documentation',
    'system',
    'user',
    'datetime_range',
    'datetime',
    'options',
    'filter',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['system', 'content_model_documentation', 'filter']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('cmDocument');
    $this->setUpCurrentUser();
  }

}
