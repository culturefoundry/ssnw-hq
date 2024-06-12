<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_report\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;
use Drupal\user\Entity\User;

/**
 * Tests for Schema.org report.
 *
 * @group schemadotorg
 */
class SchemaDotOrgReportTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['block', 'schemadotorg_report'];

  /**
   * A user with permission to access site reports.
   */
  protected User $reportUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->reportUser = $this->drupalCreateUser(['access site reports']);

    $this->drupalPlaceBlock('page_title_block');
    $this->drupalPlaceBlock('local_tasks_block');
  }

  /**
   * Test report routes, controllers, and form.
   *
   * This a baseline test that confirms the Schema.org report renders
   * as expected with the expected page title.
   */
  public function testReport(): void {
    $assert = $this->assertSession();

    /* ********************************************************************** */

    // Check that anonymous users can't access the Schema.org report.
    $this->drupalGet('admin/reports/schemadotorg');
    $assert->statusCodeEquals(403);

    // Login account with 'access site reports' permission.
    $this->drupalLogin($this->reportUser);

    // Check about (index) page.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportItemController::about
    $this->drupalGet('admin/reports/schemadotorg');
    $assert->statusCodeEquals(200);
    $assert->responseContains('Schema.org: About</h1>');

    // Check find Schema.org type form.
    $this->submitForm(['id' => 'Thing'], 'Find');
    $assert->addressEquals('/admin/reports/schemadotorg/Thing');
    $assert->responseContains('Schema.org: Thing (Type)</h1>');

    // Check that the 'About' page/tab points to the same URL.
    // @see schemadotorg_report_menu_local_tasks_alter()
    $this->drupalGet('admin/reports/schemadotorg');
    $assert->linkExists('About');
    $assert->linkByHrefExists('/admin/reports/schemadotorg');
    $this->drupalGet('admin/reports/schemadotorg/Thing');
    $assert->linkExists('About');
    $assert->linkByHrefExists('/admin/reports/schemadotorg');
    $this->drupalGet('admin/reports/schemadotorg/name');
    $assert->linkExists('About');
    $assert->linkByHrefExists('/admin/reports/schemadotorg');

    /* ********************************************************************** */

    // Check Schema.org type item.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportItemController::item
    $this->drupalGet('admin/reports/schemadotorg/Thing');
    $assert->responseContains('Schema.org: Thing (Type)</h1>');

    // Check Schema.org property item.
    $this->drupalGet('admin/reports/schemadotorg/name');
    $assert->responseContains('Schema.org: name (Property)</h1>');

    /* ********************************************************************** */

    // Check Schema.org types table.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportTableController::index
    $this->drupalGet('admin/reports/schemadotorg/docs/types');
    $assert->responseContains('Schema.org: Types</h1>');

    // Check find Schema.org type form.
    $this->submitForm(['id' => 'Thing'], 'Find');
    $assert->addressEquals('/admin/reports/schemadotorg/Thing');
    $assert->responseContains('Schema.org: Thing (Type)</h1>');

    /* ********************************************************************** */

    // Check Things hierarchical tree.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportHierarchyController::index
    $this->drupalGet('admin/reports/schemadotorg/docs/things');
    $assert->responseContains('Schema.org: Things</h1>');

    // Check Intangibles hierarchical tree.
    $this->drupalGet('admin/reports/schemadotorg/docs/intangibles');
    $assert->responseContains('Schema.org: Intangibles</h1>');

    // Check Enumerations hierarchical tree.
    $this->drupalGet('admin/reports/schemadotorg/docs/enumerations');
    $assert->responseContains('Schema.org: Enumerations</h1>');

    // Check Structured values hierarchical tree.
    $this->drupalGet('admin/reports/schemadotorg/docs/structured-values');
    $assert->responseContains('Schema.org: Structured values</h1>');

    // Check Data types hierarchical tree.
    $this->drupalGet('admin/reports/schemadotorg/docs/data-types');
    $assert->responseContains('Schema.org: Data types</h1>');

    /* ********************************************************************** */

    // Check Schema.org properties table.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportTableController::index
    $this->drupalGet('admin/reports/schemadotorg/docs/properties');
    $assert->responseContains('Schema.org: Properties</h1>');

    // Check find Schema.org property form.
    $this->submitForm(['id' => 'name'], 'Find');
    $assert->addressEquals('/admin/reports/schemadotorg/name');
    $assert->responseContains('Schema.org: name (Property)</h1>');

    /* ********************************************************************** */

    // Check Schema.org relationships table.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportRelationshipsController::index
    $this->drupalGet('admin/reports/schemadotorg/docs/relationships');
    $assert->responseContains('Schema.org: Relationships</h1>');

    /* ********************************************************************** */

    // Check Schema.org names overview.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportNamesController::overview
    $this->drupalGet('admin/reports/schemadotorg/docs/names');
    $assert->responseContains('Schema.org: Names overview</h1>');

    // Check Schema.org all names tables.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReportNamesController::table
    $this->drupalGet('admin/reports/schemadotorg/docs/names/all');
    $assert->responseContains('Schema.org: All names</h1>');
    $assert->responseContains('2313 items');

    // Check Schema.org type names tables.
    $this->drupalGet('admin/reports/schemadotorg/docs/names/types');
    $assert->responseContains('Schema.org: Type names</h1>');
    $assert->responseContains('839 types');

    // Check Schema.org property names tables.
    $this->drupalGet('admin/reports/schemadotorg/docs/names/properties');
    $assert->responseContains('Schema.org: Property names</h1>');
    $assert->responseContains('1474 properties');

    // Check Schema.org property names tables.
    $this->drupalGet('admin/reports/schemadotorg/docs/names/abbreviations');
    $assert->responseContains('Schema.org: Abbreviated names</h1>');

    /* ********************************************************************** */

    // Check Schema.org reference.
    // @see \Drupal\schemadotorg_report\Controller\SchemaDotOrgReferencesController::index
    $this->drupalGet('admin/reports/schemadotorg/docs/references');
    $assert->responseContains('Schema.org: References</h1>');
  }

}
