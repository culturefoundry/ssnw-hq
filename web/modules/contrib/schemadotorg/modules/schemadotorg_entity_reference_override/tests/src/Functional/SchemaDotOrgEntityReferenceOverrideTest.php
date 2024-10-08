<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_entity_reference_override\Functional;

use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org role entity reference override support.
 *
 * @group schemadotorg
 */
class SchemaDotOrgEntityReferenceOverrideTest extends SchemaDotOrgBrowserTestBase {

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
  protected static $modules = ['schemadotorg_entity_reference_override'];

  /**
   * Test Schema.org role entity reference override support.
   */
  public function testEntityReferenceOverride(): void {
    $assert = $this->assertSession();

    // Convert the member role text field to select menu.
    $this->config('schemadotorg_entity_reference_override.settings')
      ->set('schema_properties', [
        'member' => [
          'type' => 'select',
          'size' => NULL,
          'options' => [
            '' => '- None -',
            'Employee' => 'Employee',
            'Manager' => 'Manager',
          ],
        ],
      ])
      ->save();

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

    $this->drupalLogin($this->rootUser);

    /* ********************************************************************** */

    // Check that the entity reference override text field is a select menu.
    $this->drupalGet('node/add/organization');
    $assert->selectExists('schema_member[0][override]');
    $assert->optionExists('schema_member[0][override]', 'Employee');
    $assert->optionExists('schema_member[0][override]', 'Manager');

    // Check that #options includes the #default_value to #options.
    $this->drupalGet('node/' . $organization_node->id() . '/edit');
    $assert->selectExists('schema_member[0][override]');
    $assert->optionExists('schema_member[0][override]', 'Employee');
    $assert->optionExists('schema_member[0][override]', 'Manager');
    $assert->optionExists('schema_member[0][override]', 'President');

  }

}
