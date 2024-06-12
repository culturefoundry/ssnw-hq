<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org Subtype JSON-LD.
 *
 * @covers schemadotorg_additional_type_schemadotorg_jsonld_schema_type_entity_alter()
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalTypeJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_additional_type',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_additional_type']);
  }

  /**
   * Test Schema.org Subtype JSON-LD.
   */
  public function testSubtypeJsonLd(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    /** @var \Drupal\schemadotorg\SchemaDotOrgConfigManagerInterface $schema_config_manager */
    $schema_config_manager = \Drupal::service('schemadotorg.config_manager');
    $schema_config_manager->setSchemaTypeDefaultProperties('Person', 'additionalType');

    // Add Caregiver to Person allowed type.
    $this->config('schemadotorg_additional_type.settings')
      ->set('default_types', ['Person'])
      ->set('default_allowed_values.Person', ['Patient' => 'Patient', 'Caregiver' => 'Caregiver'])
      ->save();

    $this->createSchemaEntity('node', 'Person');

    $patient_node = Node::create([
      'type' => 'person',
      'title' => 'Patient',
      'schema_person_type' => 'Patient',
    ]);
    $patient_node->save();

    $caregiver_node = Node::create([
      'type' => 'person',
      'title' => 'Caregiver',
      'schema_person_type' => 'Caregiver',
    ]);
    $caregiver_node->save();

    // Check that Patient additional type sets the @type to Patient.
    $expected_result = [
      '@type' => 'Patient',
      '@url' => $patient_node->toUrl()->setAbsolute()->toString(),
      'name' => 'Patient',
    ];
    $this->assertEquals($expected_result, $this->builder->buildEntity($patient_node));

    // Check that Caregiver additional type sets the 'additionalType' property
    // to Caregiver.
    $expected_result = [
      '@type' => 'Person',
      '@url' => $caregiver_node->toUrl()->setAbsolute()->toString(),
      'name' => 'Caregiver',
      'additionalType' => 'Caregiver',
    ];
    $this->assertEquals($expected_result, $this->builder->buildEntity($caregiver_node));
  }

}
