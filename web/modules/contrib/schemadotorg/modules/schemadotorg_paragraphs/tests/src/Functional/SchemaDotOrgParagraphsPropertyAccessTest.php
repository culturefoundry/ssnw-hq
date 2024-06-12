<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_paragraphs\Functional;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org paragraphs property access.
 *
 * @covers schemadotorg_paragraphs_entity_field_access()
 * @group schemadotorg
 */
class SchemaDotOrgParagraphsPropertyAccessTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_paragraphs'];

  /**
   * Test Schema.org Paragraphs property access.
   */
  public function testPropertyAccess(): void {
    $assert = $this->assertSession();

    // Create Person and Organization with ContactPoint.
    $this->appendSchemaTypeDefaultProperties('ContactPoint', 'faxNumber');
    $this->appendSchemaTypeDefaultProperties('Person', 'contactPoint');
    $this->appendSchemaTypeDefaultProperties('Organization', 'contactPoint');
    $this->createSchemaEntity('paragraph', 'ContactPoint');
    $this->createSchemaEntity('node', 'Person');
    $this->createSchemaEntity('node', 'Organization');

    // Create person and organization node's with contact_point.
    $contact_point_values = [
      'type' => 'contact_point',
      'schema_contact_type' => ['value' => '{Contact Point}'],
      'schema_telephone' => ['value' => '888-888-8888'],
      'schema_fax_number' => ['value' => '666-666-666'],
    ];
    $person_node = Node::create([
      'type' => 'person',
      'title' => 'Person',
      'schema_contact_point' => Paragraph::create($contact_point_values),
    ]);
    $person_node->save();
    $organization_node = Node::create([
      'type' => 'organization',
      'title' => 'Organization',
      'schema_contact_point' => Paragraph::create($contact_point_values),
    ]);
    $organization_node->save();

    // Login as administrator.
    $this->drupalLogin($this->rootUser);

    // Check that Person view does NOT include the fax number field.
    $this->drupalGet($person_node->toUrl());
    $assert->responseContains('{Contact Point}');
    $assert->responseContains('888-888-888');
    $assert->responseNotContains('666-666-666');

    // Check that Person edit does NOT include the fax number field.
    $this->drupalGet('node/' . $person_node->id() . '/edit');
    $assert->responseContains('{Contact Point}');
    $assert->responseContains('888-888-888');
    $assert->responseNotContains('666-666-666');

    // Check that Person create does NOT include the fax number field.
    $this->drupalGet('node/add/person');
    $this->submitForm([], 'Add Contact Point');
    $assert->fieldExists('schema_contact_point[0][subform][schema_contact_type][0][value]');
    $assert->fieldExists('schema_contact_point[0][subform][schema_telephone][0][value]');
    $assert->fieldNotExists('schema_contact_point[0][subform][schema_fax_number][0][value]');

    // Check that Organization view does include the fax number field.
    $this->drupalGet($organization_node->toUrl());
    $assert->responseContains('{Contact Point}');
    $assert->responseContains('888-888-888');
    $assert->responseContains('666-666-666');

    // Check that Organization edit does include the fax number field.
    $this->drupalGet('node/' . $organization_node->id() . '/edit');
    $assert->responseContains('{Contact Point}');
    $assert->responseContains('888-888-888');
    $assert->responseContains('666-666-666');

    // Check that Organization edit does include the fax number field.
    $this->drupalGet('node/add/organization');
    $this->submitForm([], 'Add Contact Point');
    $assert->fieldExists('schema_contact_point[0][subform][schema_contact_type][0][value]');
    $assert->fieldExists('schema_contact_point[0][subform][schema_telephone][0][value]');
    $assert->fieldExists('schema_contact_point[0][subform][schema_fax_number][0][value]');
  }

}
