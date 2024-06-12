<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_custom\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD custom validation.
 *
 * @covers \Drupal\schemadotorg_jsonapi\Form\SchemaDotOrgDemoSettingsForm
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdCustomValidationTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_ui',
    'schemadotorg_ui',
    'schemadotorg_jsonld_custom',
  ];

  /**
   * Test Schema.org JSON-LD validation.
   */
  public function testValidation(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    // Check validation of associative array setting's JSON.
    $this->drupalGet('admin/config/schemadotorg/settings/jsonld');
    $this->submitForm(['schemadotorg_jsonld_custom[default_schema_type_json]' => 'xxx: yyy'], 'Save configuration');
    $assert->statusMessageContains('Default Schema.org type custom JSON-LD field is not valid JSON for xxx. Syntax error', 'error');

    // Check validation of a mapping's JSON.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Article']]);
    $this->submitForm(['mapping[third_party_settings][schemadotorg_jsonld_custom][json]' => 'xxx'], 'Save');
    $assert->statusMessageContains('Custom JSON-LD field is not valid JSON. Syntax error', 'error');
  }

}
