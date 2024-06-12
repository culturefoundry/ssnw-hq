<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_export\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests for Schema.org export.
 *
 * @group schemadotorg
 */
class SchemaDotOrgExportTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_ui',
    'schemadotorg_ui',
    'schemadotorg_mapping_set',
    'schemadotorg_additional_type',
    'schemadotorg_report',
    'schemadotorg_export',
  ];

  /**
   * Test Schema.org descriptions.
   */
  public function testDescriptions(): void {
    $assert = $this->assertSession();

    $account = $this->drupalCreateUser([
      'access site reports',
      'administer content types',
      'administer node fields',
      'administer schemadotorg',
    ]);
    $this->drupalLogin($account);

    // Create the 'Thing' content type with type and alternateName fields.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'Thing']]);
    $edit = [
      'mapping[properties][additionalType][field][name]' => TRUE,
      'mapping[properties][alternateName][field][name]' => '_add_',
      'mapping[properties][name][field][name]' => '_add_',
    ];
    $this->submitForm($edit, 'Save');

    /* ********************************************************************** */
    // Schema.org mappings.
    /* ********************************************************************** */

    // Check that 'Download CSV' link is added to the Schema.org mapping list.
    $this->drupalGet('admin/config/schemadotorg/mappings');
    $assert->linkByHrefExists('/admin/config/schemadotorg/mappings/export');
    $assert->responseContains('<u>⇩</u> Download CSV');

    // Check Schema.org mapping CSV export.
    $this->drupalGet('admin/config/schemadotorg/mappings/export');
    $assert->responseContains('entity_type,bundle,schema_type,schema_additional_type,schema_properties');
    $assert->responseContains('node,thing,Thing,Yes,"additionalType; alternateName; name"');

    // Check Schema.org mapping HTML export.
    $this->drupalGet('admin/config/schemadotorg/mappings/node.thing/export');
    $assert->responseContains('<head><title>Thing</title></head>');
    $assert->responseContains('<h1>Thing</h1>');
    $assert->responseContains('<p>The most generic type of item.</p>');
    $assert->responseContains('<p><a href="https://schema.org/Thing">https://schema.org/Thing</a></p>');
    $assert->responseContains('<td>alternate_name<br />[string]</td>');

    /* ********************************************************************** */
    // Schema.org mapping set.
    /* ********************************************************************** */

    // Check that 'Download CSV' link is added to the Schema.org
    // mapping set overview.
    $this->drupalGet('admin/config/schemadotorg/sets');
    $assert->linkByHrefExists('/admin/config/schemadotorg/sets/export');
    $assert->responseContains('<u>⇩</u> Download CSV');

    // Check Schema.org mapping set overview CSV export.
    $this->drupalGet('admin/config/schemadotorg/sets/export');
    $assert->responseContains('title,name,types');
    $assert->responseContains('Required,required,"media:AudioObject; media:DataDownload; media:ImageObject; media:VideoObject; taxonomy_term:DefinedTerm; node:Person"');

    // Check Schema.org mapping set details CSV export.
    $this->drupalGet('admin/config/schemadotorg/sets/required/export');
    $assert->responseContains('entity_type,entity_bundle,schema_type,field_label,field_description,schema_property,field_name,existing_field,field_type,unlimited_field');
    $assert->responseContains('node,person,Person,"Middle name","An additional name for a Person, can be used for a middle name.",additionalName,schema__additional_name,No,string,No');

    /* ********************************************************************** */
    // Schema.org type report.
    /* ********************************************************************** */

    // Check that 'Download CSV' link is added to the Schema.org type report.
    $this->drupalGet('admin/reports/schemadotorg/Article');
    $assert->linkByHrefExists('/admin/reports/schemadotorg/Article/export');
    $assert->responseContains('<u>⇩</u> Download CSV');

    // Check Schema.org type CSV export.
    $this->drupalGet('admin/reports/schemadotorg/Article/export');
    $assert->responseContains('id,label,comment,sub_property_of,equivalent_property,subproperties,domain_includes,range_includes,inverse_of,supersedes,superseded_by,is_part_of,drupal_name,drupal_label,drupal_description,status');
    $assert->responseContains('https://schema.org/author,author,"The author of this content or rating. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag. That is equivalent to this and may be used interchangeably.",,,,"https://schema.org/CreativeWork, https://schema.org/Rating","https://schema.org/Organization, https://schema.org/Person",,,,,author,Author,"The author of this content or rating.",');

    // Check Schema.org relationships CSV export.
    $this->drupalGet('admin/reports/schemadotorg/relationships/export');
    $assert->responseContains('Label,ID,Description,"Schema.org type",Hierarchy,Relationships,Enumerations,Taxonomy,Media');
    $assert->responseContains('Thing,thing,"The most generic type of item.",https://schema.org/Thing,,,https://schema.org/additionalType,,');
  }

}
