<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_starterkit\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org Starter kit report enhancements.
 *
 * @see \Drupal\schemadotorg_starterkit\EventSubscriber\SchemaDotOrgStarterkitEventSubscriber
 * @group schemadotorg
 */
class SchemaDotOrgStarterkitReportTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_report',
    'schemadotorg_starterkit',
  ];

  /**
   * Test Schema.org starter kit report enhancements.
   */
  public function testController(): void {
    $assert = $this->assertSession();
    $this->drupalLogin($this->drupalCreateUser(['access site reports']));

    // Check that starter kit default are added to the Schema.org type report.
    $this->drupalGet('admin/reports/schemadotorg/Person');
    $assert->responseContains('The below starter kit defaults are used when the Schema.org type is created via a starter kit.');
    $assert->responseContains('&#039;node:person:Person&#039;:
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
