<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_field_prefix\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org field prefix.
 *
 * @covers schemadotorg_field_prefix_form_field_ui_field_storage_add_form_alter()
 * @group schemadotorg
 */
class SchemaDotOrgFieldPrefixTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_ui',
    'schemadotorg_field_prefix',
  ];

  /**
   * Test Schema.org field prefix.
   */
  public function testFieldPrefix(): void {
    if (version_compare(\Drupal::VERSION, '10.3', '>=')) {
      $assert = $this->assertSession();
      $this->drupalLogin($this->rootUser);

      // Create the page content type.
      $this->drupalCreateContentType(['type' => 'page']);

      // Set the new field storage type.
      $this->drupalGet('admin/structure/types/manage/page/fields/add-field');
      $this->submitForm(['new_storage_type' => 'email'], 'Continue');

      // Check that changing the field prefix does exist.
      $assert->fieldExists('field_prefix');
      // Check the field prefix options.
      $assert->responseContains('<option value="field_" selected="selected">field_</option><option value="field_page_">field_page_</option><option value="schema_">schema_</option><option value="schema_page_">schema_page_</option><option value="">&lt;none&gt;</option>');
      // Check the field prefix description.
      $assert->responseContains("Select the field's prefix. Use <code>&lt;none&gt;</code> with caution because the machine-readable name can conflict with existing base field/property names.");

      // Check missing label validation.
      $edit = [
        'field_prefix' => 'schema_',
        'label' => '',
        'field_name' => 'test',
      ];
      $this->submitForm($edit, 'Continue');
      $assert->responseContains('Add new field: you need to provide a label.');

      // Check missing field name validation.
      $edit = [
        'field_prefix' => 'schema_',
        'label' => 'Test',
        'field_name' => '',
      ];
      $this->submitForm($edit, 'Continue');
      $assert->responseContains('Add new field: you need to provide a machine name for the field.');

      // Check create a schema_* field.
      $edit = [
        'field_prefix' => 'schema_',
        'label' => 'Test',
        'field_name' => 'test',
      ];
      $this->submitForm($edit, 'Continue');
      $edit = [];
      $this->submitForm($edit, 'Save settings');
      $this->assertNotNull(FieldStorageConfig::loadByName('node', 'schema_test'));
      $this->assertNotNull(FieldConfig::loadByName('node', 'page', 'schema_test'));

      // Check existing schema_* field validation.
      $this->drupalGet('admin/structure/types/manage/page/fields/add-field');
      $this->submitForm(['new_storage_type' => 'email'], 'Continue');
      $edit = [
        'field_prefix' => 'schema_',
        'label' => 'Test',
        'field_name' => 'test',
      ];
      $this->submitForm($edit, 'Continue');
      $edit = [];
      $this->submitForm($edit, 'Save settings');
      $assert->statusMessageContains("An error occurred while saving the field: 'field_storage_config' entity with ID 'node.schema_test' already exists.");
      $assert->statusMessageContains("An error occurred while saving the field: 'field_config' entity with ID 'node.page.schema_test' already exists.");

      // Check that clearing the field option remove the field prefix select menu.
      $this->config('schemadotorg_field_prefix.settings')
        ->set('field_prefix_options', [])
        ->save();
      $this->drupalGet('admin/structure/types/manage/page/fields/add-field');
      $assert->fieldNotExists('field_prefix');
    }
    else {
      // @todo Remove the below assertions when drupal:10.3.0 is full supported.
      $assert = $this->assertSession();
      $this->drupalLogin($this->rootUser);

      // Create the page content type.
      $this->drupalCreateContentType(['type' => 'page']);

      // Check that changing the field prefix does exist.
      $this->drupalGet('admin/structure/types/manage/page/fields/add-field');
      $assert->fieldExists('field_prefix');
      // Check the field prefix options.
      $assert->responseContains('<option value="field_" selected="selected">field_</option><option value="field_page_">field_page_</option><option value="schema_">schema_</option><option value="schema_page_">schema_page_</option><option value="">&lt;none&gt;</option>');
      // Check the field prefix description.
      $assert->responseContains("Select the field's prefix. Use <code>&lt;none&gt;</code> with caution because the machine-readable name can conflict with existing base field/property names.");

      // Check missing label validation.
      $edit = [
        'new_storage_type' => 'email',
        'field_prefix' => 'schema_',
        'label' => '',
        'field_name' => 'test',
      ];
      $this->submitForm($edit, 'Continue');
      $assert->responseContains('Add new field: you need to provide a label.');

      // Check missing field name validation.
      $edit = [
        'new_storage_type' => 'email',
        'field_prefix' => 'schema_',
        'label' => 'Test',
        'field_name' => '',
      ];
      $this->submitForm($edit, 'Continue');
      $assert->responseContains('Add new field: you need to provide a machine name for the field.');

      // Check create a schema_* field.
      $edit = [
        'new_storage_type' => 'email',
        'field_prefix' => 'schema_',
        'label' => 'Test',
        'field_name' => 'test',
      ];
      $this->submitForm($edit, 'Continue');
      $edit = [];
      $this->submitForm($edit, 'Save settings');
      $this->assertNotNull(FieldStorageConfig::loadByName('node', 'schema_test'));
      $this->assertNotNull(FieldConfig::loadByName('node', 'page', 'schema_test'));

      // Check existing schema_* field validation.
      $this->drupalGet('admin/structure/types/manage/page/fields/add-field');
      $edit = [
        'new_storage_type' => 'email',
        'field_prefix' => 'schema_',
        'label' => 'Test',
        'field_name' => 'test',
      ];
      $this->submitForm($edit, 'Continue');
      $edit = [];
      $this->submitForm($edit, 'Save settings');
      $assert->statusMessageContains("An error occurred while saving the field: 'field_storage_config' entity with ID 'node.schema_test' already exists.");
      $assert->statusMessageContains("An error occurred while saving the field: 'field_config' entity with ID 'node.page.schema_test' already exists.");

      // Check that clearing the field option remove the field prefix select menu.
      $this->config('schemadotorg_field_prefix.settings')
        ->set('field_prefix_options', [])
        ->save();
      $this->drupalGet('admin/structure/types/manage/page/fields/add-field');
      $assert->fieldNotExists('field_prefix');
    }
  }

}
