<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_block_content\Kernel;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org block content module JSON-LD integration.
 *
 * @covers schemadotorg_block_content_schemadotorg_jsonld_schema_type_entity_alter()
 * @group schemadotorg
 */
class SchemaDotOrgBlockContentJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_block_content']);

    \Drupal::moduleHandler()->loadInclude('schemadotorg_block_content', 'install');
    schemadotorg_block_content_install(FALSE);
  }

  /**
   * Test Schema.org block content JSON-LD.
   */
  public function testJsonLdBlockContent(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['administer block content']));

    $this->createSchemaEntity('block_content', 'WebContent');

    $block_content = BlockContent::create([
      'type' => 'basic',
      'info' => 'Some content',
    ]);
    $block_content->save();
    $expected_value = [
      '@type' => 'WebContent',
      'name' => 'Some content',
      'inLanguage' => 'en',
      'dateModified' => $this->formatDateTime($block_content->getChangedTime()),
    ];
    $actual_value = $this->builder->buildEntity($block_content);
    $this->assertEquals($expected_value, $actual_value);
  }

}
