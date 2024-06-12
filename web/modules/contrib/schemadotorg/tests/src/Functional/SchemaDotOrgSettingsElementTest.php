<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

/**
 * Tests the functionality of the Schema.org settings element.
 *
 * @covers \Drupal\schemadotorg\Element\SchemaDotOrgSettings
 * @group schemadotorg
 */
class SchemaDotOrgSettingsElementTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_settings_element_test'];

  /**
   * Test Schema.org settings form.
   */
  public function testSchemaDotOrgSettingsElement(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    $this->drupalGet('schemadotorg-settings-element-test');

    // Check expected values when submitting the form via text format.
    $assert->fieldValueEquals('schemadotorg_settings_element_test[yaml]', 'title: YAML');
    $this->submitForm([], 'Submit');
    $expected_data = <<<EOT
indexed:
  - one
  - two
  - three
indexed_grouped:
  A:
    - one
    - two
    - three
  B:
    - four
    - five
    - six
indexed_grouped_named:
  A:
    label: 'Group A'
    items:
      - one
      - two
      - three
  B:
    label: 'Group B'
    items:
      - four
      - five
      - six
associative:
  one: One
  two: Two
  three: Three
associative_grouped:
  A:
    one: One
    two: Two
    three: Three
  B:
    four: Four
    five: Five
    six: Six
associative_grouped_named:
  A:
    label: 'Group A'
    items:
      one: One
      two: Two
      three: Three
  B:
    label: 'Group B'
    items:
      four: Four
      five: Five
      six: Six
links_grouped:
  A:
    -
      title: Yahoo!!!
      uri: 'https://yahoo.com'
  B:
    -
      title: Google
      uri: 'https://google.com'
associative_advanced:
  title: Title
  required: true
  height: 100
  width: 100
yaml:
  title: YAML
yaml_raw: 'title: YAML raw'
json_raw: |-
  {
    "name": "value"
  }
EOT;
    $assert->responseContains($expected_data);

    // Check browse token and Schema.org links.
    $assert->linkExists('Browse available tokens.');
    $assert->linkExists('Browse Schema.org types.');

    // Check YAML validation.
    $this->drupalGet('schemadotorg-settings-element-test');
    $this->submitForm(['schemadotorg_settings_element_test[indexed]' => '"not: valid yaml'], 'Submit');
    $assert->responseContains('Error message');

    // Check YAML raw validation.
    $this->drupalGet('schemadotorg-settings-element-test');
    $this->submitForm(['schemadotorg_settings_element_test[yaml_raw]' => '"not: valid yaml'], 'Submit');
    $assert->responseContains('Error message');

    // Check JSON raw validation.
    $this->drupalGet('schemadotorg-settings-element-test');
    $this->submitForm(['schemadotorg_settings_element_test[json_raw]' => '"not: valid json'], 'Submit');
    $assert->responseContains('Error message');

    // Check configuration Schema.org validation.
    $this->drupalGet('schemadotorg-settings-element-test');
    $this->submitForm(['schemadotorg_settings_element_test[indexed]' => 'not: [valid schema]'], 'Submit');
    $assert->responseContains('indexed field is invalid.');
    $assert->responseContains('The configuration property indexed.not.0 doesn&#039;t exist.');
  }

}
