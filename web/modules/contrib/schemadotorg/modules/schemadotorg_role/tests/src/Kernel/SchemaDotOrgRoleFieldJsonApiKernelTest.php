<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_role\Kernel;

use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\Tests\schemadotorg_jsonapi\Kernel\SchemaDotOrgJsonApiKernelTestBase;

/**
 * Tests the functionality of the Schema.org role JSON:API support.
 *
 * @covers schemadotorg_role_jsonapi_resource_config_presave()
 * @group schemadotorg
 */
class SchemaDotOrgRoleFieldJsonApiKernelTest extends SchemaDotOrgJsonApiKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_role',
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
    $this->installConfig(['schemadotorg_role']);

    $this->mappingManager = $this->container->get('schemadotorg.mapping_manager');
  }

  /**
   * Test Schema.org role JSON:API support.
   */
  public function testRoleJsonApi(): void {
    $this->mappingManager->createType('node', 'PodcastEpisode');

    // Check that JSON:API resource was created for Thing.
    /** @var \Drupal\jsonapi_extras\Entity\JsonapiResourceConfig $resource */
    $resource = $this->resourceStorage->load('node--podcast_episode');
    $resource_fields = $resource->get('resourceFields');
    $expected_result = [
      'disabled' => FALSE,
      'fieldName' => 'schema_role_guest',
      'publicName' => 'guest',
      'enhancer' => ['id' => ''],
    ];
    $this->assertEquals($expected_result, $resource_fields['schema_role_guest']);
    $expected_result = [
      'disabled' => FALSE,
      'fieldName' => 'schema_role_host',
      'publicName' => 'host',
      'enhancer' => ['id' => ''],
    ];
    $this->assertEquals($expected_result, $resource_fields['schema_role_host']);
  }

}
