<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_recipe\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org Recipe report enhancements.
 *
 * @see \Drupal\schemadotorg_recipe\EventSubscriber\SchemaDotOrgRecipeEventSubscriber
 * @group schemadotorg
 */
class SchemaDotOrgRecipeReportTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_report',
    'schemadotorg_recipe',
  ];

  /**
   * Test Schema.org recipe report enhancements.
   */
  public function testController(): void {
    $assert = $this->assertSession();
    $this->drupalLogin($this->drupalCreateUser(['access site reports']));

    // Check that recipe default are added to the Schema.org type report.
    $this->drupalGet('admin/reports/schemadotorg/Person');
    $assert->responseContains('The below mapping defaults are used when the Schema.org type is created via a recipe.');
    $assert->responseContains('name: &#039;Schema.org Person Recipe.&#039;
description: &#039;A person (alive, dead, undead, or fictional).&#039;
type: &#039;Schema.org Blueprints Recipe&#039;
config:
  actions:
    schemadotorg.schemadotorg_mappings.node.person:
      createSchemaType:
        schema_type: Person
        entity:
          label: Person
          id: person
          description: &#039;A person (alive, dead, undead, or fictional).&#039;
        properties:
          additionalName: true
          description: true
          email: true
          familyName: true
          givenName: true
          image: true
          knowsLanguage: true
          memberOf: true
          name: true
          sameAs: true
          telephone: true
          worksFor: true
        third_party_settings: {  }
        additional_mappings: {  }');
  }

}
