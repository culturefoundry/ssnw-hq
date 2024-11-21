<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_entity_reference_override\Kernel;

use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;
use Drupal\node\Entity\Node;

/**
 * Tests the functionality of the Schema.org entity reference override JSON-LD.
 *
 * @covers schemadotorg_entity_reference_override_schemadotorg_property_field_alter()
 * @covers schemadotorg_entity_reference_override_schemadotorg_jsonld_schema_property_alter()
 * @group schemadotorg
 */
class SchemaDotOrgEntityReferenceOverrideJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the entity_reference_override.module has fixed its schema.
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
    'schemadotorg_entity_reference_override',
    'entity_reference_override',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_entity_reference_override']);
  }

  /**
   * Test Schema.org role entity reference override JSON-LD support.
   */
  public function testEntityReferenceOverride(): void {
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
