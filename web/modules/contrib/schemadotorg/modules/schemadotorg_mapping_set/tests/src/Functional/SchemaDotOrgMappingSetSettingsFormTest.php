<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_mapping_set\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org mapping set settings form.
 *
 * @covers \Drupal\schemadotorg_mapping_set\Form\SchemaDotOrgMappingSetSettingsForm
 * @group schemadotorg
 */
class SchemaDotOrgMappingSetSettingsFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'paragraphs',
    'taxonomy',
    'block_content',
    'schemadotorg_mapping_set',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org Mapping Set settings form.
   */
  public function testSettingsForm(): void {
    $assert = $this->assertSession();

    // Check saving config form.
    $this->assertSaveSettingsConfigForm('schemadotorg_mapping_set.settings', '/admin/config/schemadotorg/sets/settings');

    // Check type validation.
    $this->drupalGet('admin/config/schemadotorg/sets/settings');
    $this->submitForm([
    'sets' => "test:
  label: Test
  types:
    - 'test'",
    ], 'Save configuration');
    $assert->statusMessageContains('test in Test is not valid. Please enter the entity type id and Schema.org type (i.e. entity_type_id:SchemaType).', 'error');

    // Check entity type id validation.
    $this->drupalGet('admin/config/schemadotorg/sets/settings');
    $this->submitForm([
    'sets' => "test:
  label: Test
  types:
    - 'test:Test'",
    ], 'Save configuration');
    $assert->statusMessageContains('test in Test is not valid entity type.', 'error');

    // Check Schema.org type validation.
    $this->drupalGet('admin/config/schemadotorg/sets/settings');
    $this->submitForm([
    'sets' => "test:
  label: Test
  types:
    - 'node:Test'",
    ], 'Save configuration');
    $assert->statusMessageContains('Test in Test is not valid Schema.org type.', 'error');

    // Check node:Thing is valid.
    $this->drupalGet('admin/config/schemadotorg/sets/settings');
    $this->submitForm([
    'sets' => "test:
  label: Test
  types:
    - 'node:Thing'",
    ], 'Save configuration');
    $assert->responseContains('The configuration options have been saved.');
  }

}
