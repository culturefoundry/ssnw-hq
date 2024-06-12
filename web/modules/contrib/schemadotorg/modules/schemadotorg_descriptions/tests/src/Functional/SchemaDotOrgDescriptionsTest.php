<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_descriptions\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests for Schema.org descriptions.
 *
 * @group schemadotorg
 */
class SchemaDotOrgDescriptionsTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_ui',
    'block',
    'help',
    'schemadotorg_ui',
    'schemadotorg_additional_type',
    'schemadotorg_layout_paragraphs',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->placeBlock('help_block');
  }

  /**
   * Test Schema.org descriptions.
   */
  public function testDescriptions(): void {
    $assert = $this->assertSession();

    // Login as node type administrator.
    $account = $this->drupalCreateUser([
      'administer schemadotorg',
      'administer content types',
      'administer node fields',
    ]);
    $this->drupalLogin($account);

    // Check add content type, additionalType, and field descriptions.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Thing']]);
    $assert->fieldValueEquals('mapping[entity][description]', 'The most generic type of item.');
    $assert->fieldValueEquals('mapping[properties][additionalType][field][_add_][description]', 'An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax.');
    $assert->fieldValueEquals('mapping[properties][description][field][_add_][description]', 'A description of the item.');

    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = \Drupal::service('module_installer');
    $module_installer->install(['schemadotorg_descriptions']);

    // Check add content type, additionalType, and field descriptions are empty and
    // the element's #description is updated.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Thing']]);
    $assert->fieldValueEquals('mapping[entity][description]', '');
    $assert->fieldValueEquals('mapping[properties][additionalType][field][_add_][description]', '');
    $assert->fieldValueEquals('mapping[properties][description][field][_add_][description]', '');
    $assert->responseContains("<strong>If left blank, the description will be automatically set to the corresponding Schema.org type's comment or custom description.</strong>");
    $assert->responseContains("<strong>If left blank, the description will be automatically set.</strong>");

    // Check applying the Schema.org type's custom description to the comment.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'FAQPage']]);
    $assert->responseContains('A page presenting one or more "Frequently asked questions".');

    // Check applying the Schema.org property's custom description to the comment.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Recipe']]);
    $assert->responseContains('The length of time it takes to prepare the items to be used in instructions or a directions.');

    // Create the 'Thing' content type with type and alternateName fields.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Thing']]);
    $edit = [
      'mapping[properties][additionalType][field][name]' => TRUE,
      'mapping[properties][alternateName][field][name]' => '_add_',
    ];
    $this->submitForm($edit, 'Save');

    // Create another random content type to enable the node add page.
    $this->drupalCreateContentType();

    // Login as root user since we are not testing node access.
    $this->drupalLogin($this->rootUser);

    // Check that the description is automatically added to the node types page.
    $this->drupalGet('admin/structure/types');
    $assert->responseContains('The most generic type of item.');

    // Check that the description is automatically added to the node add page.
    $this->drupalGet('node/add');
    $assert->responseContains('The most generic type of item.');

    // Check that the descriptions are automatically added to the node edit form.
    $this->drupalGet('node/add/thing');
    $assert->responseContains('The most generic type of item.');
    $assert->responseContains('An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax.');
    $assert->responseContains('An alias for the item.');

    // Add custom descriptions for Thing and alternateName.
    $this->drupalGet('admin/config/schemadotorg/settings/general');
    $edit = [
      'schemadotorg_descriptions[custom_descriptions]' => 'Thing: This is a custom description for a Thing.'
      . PHP_EOL . 'alternateName: This is a custom description for an alternateName.',
    ];
    $this->submitForm($edit, 'Save configuration');

    // Check that the custom description is automatically added to the
    // node types page.
    $this->drupalGet('admin/structure/types');
    $assert->responseNotContains('The most generic type of item.');
    $assert->responseContains('This is a custom description for a Thing.');

    // Check that the custom description is automatically added to the
    // node add page.
    $this->drupalGet('node/add');
    $assert->responseNotContains('The most generic type of item.');
    $assert->responseContains('This is a custom description for a Thing.');

    // Check that the custom descriptions are automatically added to the
    // node edit form.
    $this->drupalGet('node/add/thing');
    $assert->responseNotContains('An alias for the item.');
    $assert->responseContains('This is a custom description for a Thing.');
    $assert->responseContains('This is a custom description for an alternateName.');

    // Add custom descriptions for Thing and alternateName.
    $this->drupalGet('admin/config/schemadotorg/settings/general');
    $edit = [
      'schemadotorg_descriptions[custom_descriptions]' => 'Thing: This is a custom description for a Thing.'
      . PHP_EOL . 'alternateName: This is a custom description for an alternateName.'
      . PHP_EOL . 'Thing--alternateName: This is a custom description for an Thing--alternateName',
    ];
    $this->submitForm($edit, 'Save configuration');

    // Check that the Thing--alternateName custom description is uses.
    $this->drupalGet('node/add/thing');
    $assert->responseContains('This is a custom description for a Thing.');
    $assert->responseNotContains('This is a custom description for an alternateName.');
    $assert->responseContains('This is a custom description for an Thing--alternateName');

    // Remove custom descriptions for Thing and alternateName.
    $this->drupalGet('admin/config/schemadotorg/settings/general');
    $edit = [
      'schemadotorg_descriptions[custom_descriptions]' => 'Thing: null' . PHP_EOL . 'alternateName: null',
    ];
    $this->submitForm($edit, 'Save configuration');

    // Check that NO custom description is added to the node types page.
    $this->drupalGet('admin/structure/types');
    $assert->responseNotContains('The most generic type of item.');
    $assert->responseNotContains('This is a custom description for a Thing.');

    // Check that NO custom description is added to the node add page.
    $this->drupalGet('node/add');
    $assert->responseNotContains('The most generic type of item.');
    $assert->responseNotContains('This is a custom description for a Thing.');

    // Check that NOT custom descriptions are added to the node edit form.
    $this->drupalGet('node/add/thing');
    $assert->responseNotContains('An alias for the item.');
    $assert->responseNotContains('This is a custom description for a Thing.');
    $assert->responseNotContains('This is a custom description for an alternateName.');

    // Create 'Offer' with 'price' which has a long description.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Offer']]);
    $this->submitForm([], 'Save');

    // Check that the price and priceCurrency descriptions are trimmed.
    $this->drupalGet('node/add/offer');
    $assert->responseContains('The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.');
    $assert->responseNotContains('Usage guidelines:');

    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = \Drupal::service('module_installer');
    $module_installer->uninstall(['schemadotorg_descriptions']);

    // Check that the descriptions are not added to the node add page.
    $this->drupalGet('node/add');
    $assert->responseNotContains('The most generic type of item.');

    // Check that descriptions are not added to the node edit form.
    $this->drupalGet('node/add/thing');
    $assert->responseNotContains('A more specific additionalType for the item. This is used to allow more specificity without having to create dedicated Schema.org entity types.');
    $assert->responseNotContains('An alias for the item.');
  }

}
