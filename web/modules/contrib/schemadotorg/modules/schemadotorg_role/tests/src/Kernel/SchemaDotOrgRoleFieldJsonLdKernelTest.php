<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_role\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org role JSON-LD.
 *
 * @covers schemadotorg_role_schemadotorg_jsonld_schema_type_entity_load()
 * @group schemadotorg
 */
class SchemaDotOrgRoleFieldJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_role',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_role']);
  }

  /**
   * Test Schema.org role.
   */
  public function testRole(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->createSchemaEntity('node', 'PodcastEpisode');

    $node = Node::create([
      'type' => 'podcast_episode',
      'title' => 'Something',
      'schema_role_guest' => [
        'value' => 'Some Guest',
      ],
      'schema_role_host' => [
        'value' => 'Some Host',
      ],
    ]);
    $node->save();

    /* ********************************************************************** */

    // Check that the JSON-LD actor property is using roles.
    $jsonld = $this->builder->buildEntity($node);
    $expected_actor = [
      [
        '@type' => 'Role',
        'roleName' => 'Host',
        'actor' => [
          '@type' => 'Person',
          'name' => 'Some Host',
        ],
      ],
      [
        '@type' => 'Role',
        'roleName' => 'Guest',
        'actor' => [
          '@type' => 'Person',
          'name' => 'Some Guest',
        ],
      ],
    ];

    $this->assertEquals($expected_actor, $jsonld['actor']);
  }

}
