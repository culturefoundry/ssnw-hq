<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_block_content\Kernel;

use Drupal\block_content\Entity\BlockContentType;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org block content installation.
 *
 * @covers \schemadotorg_block_content_install()
 * @group schemadotorg
 */
class SchemaDotOrgBlockContentInstallKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntityDependencies('block_content');
  }

  /**
   * Test Schema.org block_content installation.
   */
  public function testInstall(): void {
    BlockContentType::create([
      'id' => 'basic',
      'label' => 'Basic',
    ])->save();

    \Drupal::moduleHandler()->loadInclude('schemadotorg_block_content', 'install');
    schemadotorg_block_content_install(FALSE);

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = SchemaDotOrgMapping::load('block_content.basic');

    // Confirm block_content.basic mapping is created and mapped to WebContent.
    $this->assertEquals('WebContent', $mapping->getSchemaType());
  }

}
