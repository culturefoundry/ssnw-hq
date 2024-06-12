<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_mercury_editor\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org mercury editor.
 *
 * @covers schemadotorg_mercury_editor_schemadotorg_mapping_insert()
 * @group schemadotorg
 */
class SchemaDotOrgMercuryEditorKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the Mercury Editor module has fixed its schema.
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'taxonomy',
    'mercury_editor',
    'schemadotorg_mercury_editor',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(static::$modules);
    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');
  }

  /**
   * Test Schema.org mercury editor.
   */
  public function testMercuryEditor(): void {
    $this->appendSchemaTypeDefaultProperties('WebPage', 'mainEntity');
    $this->createSchemaEntity('node', 'WebPage');

    // Check that WebPage has the mercury editor enabled.
    $this->assertEquals(
      ['page' => 'page'],
      \Drupal::config('mercury_editor.settings')->get('bundles.node')
    );
  }

}
