<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Kernel;

use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;
use Drupal\node\Entity\Node;

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
    $schema_config_manager->setSchemaTypeDefaultProperties('Organization', 'additionalType');

    // Add OtherOrganization to Organization allowed type.
    $this->config('schemadotorg_additional_type.settings')
      ->set('default_types', ['Organization'])
      ->set('default_allowed_values.Organization', ['ResearchOrganization' => 'ResearchOrganization', 'OtherOrganization' => 'OtherOrganization'])
      ->save();

    $this->createSchemaEntity('node', 'Organization');

    $research_node = Node::create([
      'type' => 'organization',
      'title' => 'ResearchOrganization',
      'schema_organization_type' => 'ResearchOrganization',
    ]);
    $research_node->save();

    $other_node = Node::create([
      'type' => 'organization',
      'title' => 'OtherOrganization',
      'schema_organization_type' => 'OtherOrganization',
    ]);
    $other_node->save();

    // Check that ResearchOrganization additional type sets the @type to ResearchOrganization.
    $expected_result = [
      '@type' => 'ResearchOrganization',
      '@url' => $research_node->toUrl()->setAbsolute()->toString(),
      'name' => 'ResearchOrganization',
    ];
    $this->assertEquals($expected_result, $this->builder->buildEntity($research_node));

    // Check using machine name for additional type.
    $research_node->schema_organization_type->value = 'research_organization';
    $expected_result = [
      '@type' => 'ResearchOrganization',
      '@url' => $research_node->toUrl()->setAbsolute()->toString(),
      'name' => 'ResearchOrganization',
    ];
    $this->assertEquals($expected_result, $this->builder->buildEntity($research_node));

    // Check that OtherOrganization additional type sets the 'additionalType' property
    // to OtherOrganization.
    $expected_result = [
      '@type' => 'Organization',
      '@url' => $other_node->toUrl()->setAbsolute()->toString(),
      'name' => 'OtherOrganization',
      'additionalType' => 'OtherOrganization',
    ];
    $this->assertEquals($expected_result, $this->builder->buildEntity($other_node));

  }

}
