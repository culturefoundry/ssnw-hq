<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_role\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org role JSON-LD reference.
 *
 * @covers schemadotorg_role_schemadotorg_property_field_alter()
 * @covers schemadotorg_role_schemadotorg_jsonld_schema_property_alter()
 * @group schemadotorg
 */
class SchemaDotOrgRoleReferenceJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the cer.module has fixed its schema.
   *
   * Issue #3331271: Schema definition for the "override_format" setting is missing.
   *
   * @see https://www.drupal.org/project/entity_reference_override/issues/3331271
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_role',
    'entity_reference_override',
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

    $this->appendSchemaTypeDefaultProperties('Organization', 'member');
    $this->createSchemaEntity('node', 'Person');
    $this->createSchemaEntity('node', 'Organization');

    $person_node = Node::create([
      'type' => 'person',
      'title' => 'John Smith',
    ]);
    $person_node->save();

    $organization_node = Node::create([
      'type' => 'organization',
      'title' => 'Organization',
      'schema_member' => [
        [
          'target_id' => $person_node->id(),
          'override' => 'President',
        ],
      ],
    ]);
    $organization_node->save();

    /* ********************************************************************** */

    // Check that the JSON-LD member property is using roles.
    $jsonld = $this->builder->buildEntity($organization_node);
    $expected_member = [
      [
        '@type' => 'Role',
        'roleName' => 'President',
        'member' =>
          [
            '@type' => 'Person',
            '@url' => $person_node->toUrl()->setAbsolute()->toString(),
            'name' => 'John Smith',
          ],
      ],
    ];
    $this->assertEquals($expected_member, $jsonld['member']);
  }

}
