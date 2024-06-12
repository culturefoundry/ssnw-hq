<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_cer\Kernel;

use Drupal\node\Entity\Node;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org Corresponding Entity References manager.
 *
 * @group schemadotorg
 */
class SchemaDotOrgCorrespondingReferenceKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the cer.module has fixed its schema.
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'cer',
    'schemadotorg_cer',
  ];

  /**
   * The Schema.org mapping manager.
   */
  protected SchemaDotOrgMappingManagerInterface $mappingManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installConfig(self::$modules);

    \Drupal::moduleHandler()->loadInclude('schemadotorg_cer', 'install');
    schemadotorg_cer_install(FALSE);
  }

  /**
   * Test Schema.org corresponding entity references.
   */
  public function testCorrespondingReference(): void {
    $this->createSchemaEntity('node', 'Person');
    $this->createSchemaEntity('node', 'WebPage');

    $person_node = Node::create([
      'type' => 'person',
      'title' => 'Person',
    ]);
    $person_node->save();

    $page_node = Node::create([
      'type' => 'page',
      'title' => 'Page',
    ]);
    $page_node->save();

    // Check that there are no entity reference btw the person and page.
    $this->assertNull($person_node->schema_subject_of->target_id);
    $this->assertNull($page_node->schema_about->target_id);

    // Add the person to the page's about field.
    $page_node->schema_about->target_id = $person_node->id();
    $page_node->save();

    // Reload the updated corresponding person node.
    $person_node = Node::load($person_node->id());

    // Check that there is no a corresponding entity reference..
    $this->assertEquals($page_node->id(), $person_node->schema_subject_of->target_id);
    $this->assertEquals($person_node->id(), $page_node->schema_about->target_id);

    // Remove the page from person's subject of field.
    $person_node->schema_subject_of->setValue([]);
    $person_node->save();

    // Reload the updated corresponding page node.
    $page_node = Node::load($page_node->id());

    // Check that there are no entity reference btw the person and page.
    $this->assertNull($person_node->schema_subject_of->target_id);
    $this->assertNull($page_node->schema_about->target_id);
  }

}
