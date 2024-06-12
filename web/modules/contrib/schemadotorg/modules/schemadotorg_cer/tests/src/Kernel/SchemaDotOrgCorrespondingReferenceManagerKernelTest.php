<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_cer\Kernel;

use Drupal\cer\Entity\CorrespondingReference;
use Drupal\field\Entity\FieldConfig;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org Corresponding Entity References manager.
 *
 * @group schemadotorg
 */
class SchemaDotOrgCorrespondingReferenceManagerKernelTest extends SchemaDotOrgEntityKernelTestBase {

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

    $this->installConfig(self::$modules);

    \Drupal::moduleHandler()->loadInclude('schemadotorg_cer', 'install');
    schemadotorg_cer_install(FALSE);

    // Get the Schema.org mapping manager.
    $this->mappingManager = $this->container->get('schemadotorg.mapping_manager');
  }

  /**
   * Test Schema.org corresponding entity reference manager.
   */
  public function testManager(): void {
    // Check altering Schema.org mapping entity defaults value to always
    // enable correspond node references.
    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'Person',
    );
    $this->assertEquals('_add_', $mapping_defaults['properties']['subjectOf']['name']);
    $this->assertEquals('field_ui:entity_reference:node', $mapping_defaults['properties']['subjectOf']['type']);
    $this->assertEquals('_add_', $mapping_defaults['properties']['memberOf']['name']);
    $this->assertEquals('field_ui:entity_reference:node', $mapping_defaults['properties']['memberOf']['type']);

    // Create node:Person.
    $this->createSchemaEntity('node', 'Person');

    $this->assertCount(0, CorrespondingReference::loadMultiple());

    $mapping_defaults = $this->mappingManager->getMappingDefaults(
      entity_type_id: 'node',
      schema_type: 'WebPage',
    );
    $this->assertEquals('_add_', $mapping_defaults['properties']['about']['name']);
    $this->assertEquals('field_ui:entity_reference:node', $mapping_defaults['properties']['about']['type']);
    $this->assertEquals('schema_subject_of', $mapping_defaults['properties']['subjectOf']['name']);

    // Create node:WebPage.
    $this->createSchemaEntity('node', 'WebPage');

    // Check adding corresponding entity references when a mapping is
    // inserted or updated.
    $this->assertCount(2, CorrespondingReference::loadMultiple());

    /** @var \Drupal\cer\Entity\CorrespondingReferenceInterface $corresponding_reference */
    $corresponding_reference = CorrespondingReference::load('schema_subject_of');
    $this->assertEquals('Schema.org: Subject of ↔ About', $corresponding_reference->label());
    $this->assertTrue($corresponding_reference->isEnabled());
    $this->assertEquals('schema_subject_of', $corresponding_reference->getFirstField());
    $this->assertEquals('schema_about', $corresponding_reference->getSecondField());
    $this->assertEquals('append', $corresponding_reference->getAddDirection());
    $this->assertEquals(['node' => ['*']], $corresponding_reference->getBundles());

    // Check that page:about has page and person as target bundles.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field */
    $field = FieldConfig::loadByName('node', 'page', 'schema_about');
    $this->assertEquals(
      [
        'target_type' => 'node',
        'schema_types' => ['Thing' => 'Thing'],
        'target_bundles' => ['page' => 'page', 'person' => 'person'],
      ],
      $field->getSetting('handler_settings')
    );

    // Check that person:schema_subject_of now has page as a target bundle.
    /** @var \Drupal\Core\Field\FieldConfigInterface $field */
    $field = FieldConfig::loadByName('node', 'person', 'schema_subject_of');
    $this->assertEquals(
      [
        'target_type' => 'node',
        'schema_types' => ['CreativeWork' => 'CreativeWork', 'Event' => 'Event'],
        'target_bundles' => ['page' => 'page'],
      ],
      $field->getSetting('handler_settings')
    );
  }

}
