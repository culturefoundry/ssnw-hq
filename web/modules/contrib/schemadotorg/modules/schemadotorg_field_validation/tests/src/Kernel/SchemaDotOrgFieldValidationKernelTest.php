<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_field_validation\Kernel;

use Drupal\field_validation\Entity\FieldValidationRuleSet;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the Schema.org field validation.
 *
 * @coversClass schemadotorg_field_validation_field_config_insert()
 * @group schemadotorg
 */
class SchemaDotOrgFieldValidationKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_validation',
    'schemadotorg_field_validation',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(static::$modules);
  }

  /**
   * Test Schema.org entity display builder.
   */
  public function testEntityDisplayBuilder(): void {
    $this->appendSchemaTypeDefaultProperties('Organization', 'globalLocationNumber');
    $this->createSchemaEntity('node', 'Organization');

    /** @var \Drupal\field_validation\FieldValidationRuleSetInterface $ruleset */
    $ruleset = FieldValidationRuleSet::load('node_organization');

    // Check that node organization ruleset is created.
    $this->assertEquals('node_organization', $ruleset->getName());
    $this->assertEquals('node organization validation', $ruleset->label());
    $this->assertEquals('node', $ruleset->getAttachedEntityType());
    $this->assertEquals('organization', $ruleset->getAttachedBundle());

    // Check that Global Location Number rule is added.
    $rules = $ruleset->getFieldValidationRules();
    $this->assertCount(1, $rules);
    $rule = $rules->get(array_key_first($rules->getInstanceIds()));
    $configuration = $rule->getConfiguration();
    $this->assertEquals('regex_constraint_rule', $configuration['id']);
    $this->assertEquals('Schema.org: Global Location Number (GLN)', $configuration['title']);
    $this->assertEquals('/^\\d{13}$/', $configuration['data']['pattern']);
    $this->assertEquals('Global Location Number (GLN) must be a 13-digit number.', $configuration['data']['message']);
    $this->assertEquals('schema_global_location_number', $configuration['field_name']);

    // Delete the organization and reset rule set storage.
    NodeType::load('organization')->delete();
    $this->entityTypeManager
      ->getStorage('field_validation_rule_set')
      ->resetCache();

    // Check that the ruleset still exists.
    /** @var \Drupal\field_validation\FieldValidationRuleSetInterface $ruleset */
    $ruleset = FieldValidationRuleSet::load('node_organization');
    $this->assertNotNull($ruleset);
    $this->assertCount(1, $ruleset->getFieldValidationRules());

    // Recreate the organization content type.
    $this->createSchemaEntity('node', 'Organization');
    $this->entityTypeManager
      ->getStorage('field_validation_rule_set')
      ->resetCache();

    // Check that a duplicate rule is not created.
    $ruleset = FieldValidationRuleSet::load('node_organization');
    $this->assertCount(1, $ruleset->getFieldValidationRules());
  }

}
