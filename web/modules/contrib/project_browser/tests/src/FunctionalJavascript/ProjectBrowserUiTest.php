<?php

declare(strict_types=1);

namespace Drupal\Tests\project_browser\FunctionalJavascript;

use Behat\Mink\Element\NodeElement;
use Drupal\Core\Extension\MissingDependencyException;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

// cspell:ignore coverageall doomer eggman quiznos statusactive statusmaintained
// cspell:ignore vetica

/**
 * Provides tests for the Project Browser UI.
 *
 * These tests rely on a module that replaces Project Browser data with
 * test data.
 *
 * @see project_browser_test_install()
 *
 * @group project_browser
 */
class ProjectBrowserUiTest extends WebDriverTestBase {

  use ProjectBrowserUiTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'project_browser',
    'project_browser_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->config('project_browser.admin_settings')->set('enabled_sources', ['drupalorg_mockapi'])->save(TRUE);
    $this->drupalLogin($this->drupalCreateUser([
      'administer modules',
      'administer site configuration',
    ]));
  }

  /**
   * Tests the grid view.
   */
  public function testGrid(): void {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    $this->getSession()->resizeWindow(1250, 1000);
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-project.pb-project--grid');
    $assert_session->waitForElementVisible('css', '#pb-project-browser .pb-display__button[value="Grid"]');
    $grid_text = $this->getElementText('#project-browser .pb-display__button[value="Grid"]');
    $this->assertEquals('Grid', $grid_text);
    $this->svelteInitHelper('text', '9 Results');
    $assert_session->elementsCount('css', '#project-browser .pb-project.pb-project--grid', 9);
    $this->assertTrue($assert_session->waitForText('Results'));
    $assert_session->pageTextNotContains('No modules found');
    $page->pressButton('List');
    $this->assertNotNull($assert_session->waitForElementVisible('css', '#project-browser .pb-project.pb-project--list'));
    $assert_session->elementsCount('css', '#project-browser .pb-project.pb-project--list', 9);
    $page->pressButton('Grid');
    $this->assertNotNull($assert_session->waitForElementVisible('css', '#project-browser .pb-project.pb-project--grid'));
    $this->getSession()->resizeWindow(1100, 1000);
    $assert_session->assertNoElementAfterWait('css', '.pb-display__button[value="List"]');
    $this->assertNotNull($assert_session->waitForElementVisible('css', '#project-browser .pb-project.pb-project--list'));
    $assert_session->elementsCount('css', '#project-browser .pb-project.pb-project--list', 9);
    $this->getSession()->resizeWindow(1210, 1210);
    $this->assertNotNull($assert_session->waitForElementVisible('css', '#project-browser .pb-project.pb-project--grid'));
    $assert_session->elementsCount('css', '#project-browser .pb-project.pb-project--grid', 9);
  }

  /**
   * Tests the available categories.
   */
  public function testCategories(): void {
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-filter__checkbox');
    $assert_session->elementsCount('css', '.pb-filter__checkbox', 19);
  }

  /**
   * Tests the clickable category functionality.
   */
  public function testClickableCategory(): void {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Dancing Queen');
    $page->clickLink('Dancing Queen');
    $this->svelteInitHelper('text', 'E-commerce');

    // Click 'E-commerce' category on module page.
    $this->clickWithWait('li.pb-module-page__categories-list-item:nth-child(2)');
    $module_category_e_commerce_filter_selector = 'p.filter-applied:nth-child(3)';
    $this->assertEquals('E-commerce', $this->getElementText("$module_category_e_commerce_filter_selector .filter-applied__label"));
    $this->assertTrue($assert_session->waitForText('6 Results'));
  }

  /**
   * Tests category filtering.
   */
  public function testCategoryFiltering(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '#104');

    // Click 'E-commerce' checkbox.
    $this->clickWithWait('#104');

    $module_category_e_commerce_filter_selector = 'p.filter-applied:nth-child(3)';
    // Make sure the 'E-commerce' module category filter is applied.
    $this->assertEquals('E-commerce', $this->getElementText("$module_category_e_commerce_filter_selector .filter-applied__label"));

    // This call has the second argument, `$reload`, set to TRUE due to it
    // failing on ~2% of GitLabCI test runs. It is not entirely clear why this
    // specific call intermittently fails while others do not. It's known the
    // Svelte app has occasional initialization problems on GitLabCI that are
    // reliably fixed by a page reload, so we allow that here to prevent random
    // failures that are not representative of real world use.
    $this->assertProjectsVisible([
      'Cream cheese on a bagel',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Helvetica',
      'Astronaut Simulator',
    ], TRUE);

    $this->pressWithWait('Clear filters', '25 Results');

    // Click 'Media' checkbox.
    $this->clickWithWait('#67');

    // Click 'E-commerce' checkbox.
    $this->clickWithWait('#104');

    // Make sure the 'Media' module category filter is applied.
    $this->assertEquals('Media', $this->getElementText('p.filter-applied:nth-child(2) .filter-applied__label'));
    // Assert that only media and administration module categories are shown.
    $this->assertProjectsVisible([
      'Jazz',
      'Eggman',
      'Tooth Fairy',
      'Vitamin&C;$?',
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'No Scrubs',
      'Soup',
      'Mad About You',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
    ]);
    $this->assertTrue($assert_session->waitForText('20 Results'));
  }

  /**
   * Tests the Target blank functionality.
   */
  public function testTargetBlank(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Helvetica');
    $page->clickLink('Helvetica');
    $this->assertTrue($assert_session->waitForText('Categories:'));
    $link = $page->find('css', '.pb-module-page__description a');
    $target = $link->getAttribute('target');
    $this->assertEquals('_blank', $target);
  }

  /**
   * Tests read-only input fields for referred commands.
   */
  public function testReadonlyFields(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Helvetica');

    $page->clickLink('Helvetica');
    // Simulate the condition that determines if the clipboard is enabled so
    // clipboard related UI elements are present.
    $this->getSession()->executeScript('navigator.clipboard = true');
    $this->assertTrue($assert_session->waitForText('By Hel Vetica'));
    $this->clickWithWait('#project-browser .project__action_button');
    $require_command = $assert_session->waitForElement('css', 'input[value="composer require drupal/helvetica"]');
    $this->assertNotEmpty($require_command);
    $this->assertTrue($require_command->hasAttribute('readonly'));
    $install_command = $assert_session->waitForElement('css', 'input[value="drush pm:install helvetica"]');
    $this->assertNotEmpty($install_command);
    $this->assertTrue($install_command->hasAttribute('readonly'));

    // Tests alt text for copy command image.
    $download_commands = $page->findAll('css', '.command-box img');
    $this->assertCount(3, $download_commands);
    $this->assertEquals('Copy the download command', $download_commands[0]->getAttribute('alt'));
    $this->assertEquals('Copy the install command', $download_commands[1]->getAttribute('alt'));
    $this->assertEquals('Copy the install Drush command', $download_commands[2]->getAttribute('alt'));
  }

  /**
   * Tests paging through results.
   */
  public function testPaging(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', '9 Results');

    $this->assertProjectsVisible([
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Octopus',
      'Helvetica',
      'Unwritten&:/',
      'Astronaut Simulator',
    ]);
    $this->assertPagerItems([]);

    $page->pressButton('Clear filters');
    $this->assertTrue($assert_session->waitForText('25 Results'));
    $this->assertProjectsVisible([
      'Jazz',
      'Eggman',
      'Tooth Fairy',
      'Vitamin&C;$?',
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Ice Ice',
      'No Scrubs',
      'Soup',
      'Mad About You',
      'Dancing Queen',
      'Kangaroo',
    ]);
    $this->assertPagerItems(['1', '2', '3', 'Next', 'Last']);
    $assert_session->elementExists('css', '.pager__item--active > .is-active[aria-label="Page 1"]');

    $this->clickWithWait('[aria-label="Next page"]');
    $this->assertProjectsVisible([
      '9 Starts With a Higher Number',
      'Quiznos',
      'Octopus',
      'Helvetica',
      '1 Starts With a Number',
      'Ruh roh',
      'Fire',
      'Looper',
      'Grapefruit',
      'Become a Banana',
      'Unwritten&:/',
      'Doomer',
    ]);
    $this->assertPagerItems(['First', 'Previous', '1', '2', '3', 'Next', 'Last']);

    $this->clickWithWait('[aria-label="Next page"]');
    $this->assertProjectsVisible([
      'Astronaut Simulator',
    ]);
    $this->assertPagerItems(['First', 'Previous', '1', '2', '3']);

    // Ensure that when the number of projects is even divisible by the number
    // shown on a page, the pager has the correct number of items.
    $this->clickWithWait('[aria-label="First page"]');

    // Click 'Media' checkbox.
    $this->clickWithWait('#67', '', TRUE);

    // Click 'E-commerce' checkbox.
    $this->clickWithWait('#104', '', TRUE);

    // Click 'E-commerce' checkbox.
    $this->clickWithWait('#104', '18 results');
    $this->assertPagerItems(['1', '2', 'Next', 'Last']);

    $this->clickWithWait('[aria-label="Next page"]');

    $this->assertPagerItems(['First', 'Previous', '1', '2']);
  }

  /**
   * Tests paging options.
   */
  public function testPagingOptions(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-project.pb-project--list');
    $this->pressWithWait('Clear filters');
    $assert_session->waitForText('Modules per page');
    $assert_session->elementsCount('css', '#project-browser .pb-project.pb-project--list', 12);
    $assert_session->waitForText('Modules per page');
    $page->selectFieldOption('num-projects', '24');
    $assert_session->waitForElementVisible('css', '#project-browser .pb-project.pb-project--list');
    $assert_session->elementsCount('css', '#project-browser .pb-project.pb-project--list', 24);
  }

  /**
   * Tests advanced filtering.
   */
  public function testAdvancedFiltering(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Astronaut Simulator');
    $this->pressWithWait('Clear filters');
    $this->pressWithWait('Recommended filters');
    $this->assertProjectsVisible([
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Octopus',
      'Helvetica',
      'Unwritten&:/',
      'Astronaut Simulator',
    ]);

    $second_filter_selector = 'p.filter-applied:nth-child(2)';
    // Make sure the second filter applied is the security covered filter.
    $this->assertEquals('Covered by a security policy', $this->getElementText("$second_filter_selector .filter-applied__label"));

    // Clear the security covered filter.
    $this->clickWithWait("$second_filter_selector > button");
    $this->assertProjectsVisible([
      'Jazz',
      'Vitamin&C;$?',
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Ice Ice',
      'No Scrubs',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Quiznos',
      'Octopus',
      'Helvetica',
    ]);

    $this->openAdvancedFilter();

    // Check aria-labelledby property for advanced filter.
    foreach ($page->findAll('css', '.filters [role="group"]') as $element) {
      $this->assertSame($element->findAll('xpath', 'div')[0]->getAttribute('id'), $element->getAttribute('aria-labelledby'));
    }

    // Click the Active filter.
    $assert_session->waitForElementVisible('css', '#developmentStatusactive');
    $this->clickWithWait('#developmentStatusactive');

    // Make sure the correct filter was applied.
    $this->assertEquals('Active', $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label'));

    $this->assertProjectsVisible([
      'Jazz',
      'Cream cheese on a bagel',
      'Ice Ice',
      'No Scrubs',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Octopus',
      'Helvetica',
      '1 Starts With a Number',
      'Become a Banana',
      'Astronaut Simulator',
    ]);

    // Click the "Show all" filter for security.
    $this->clickWithWait('#securityCoverageall', '', TRUE);
    $this->assertProjectsVisible([
      'Jazz',
      'Cream cheese on a bagel',
      'Ice Ice',
      'No Scrubs',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Octopus',
      'Helvetica',
      '1 Starts With a Number',
      'Become a Banana',
      'Astronaut Simulator',
    ]);

    // Clear all filters.
    $this->pressWithWait('Clear filters', '25 Results');

    // Click the Actively maintained filter.
    $this->clickWithWait('#maintenanceStatusmaintained');
    $this->assertEquals('Maintained', $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label'));

    $this->assertProjectsVisible([
      'Jazz',
      'Vitamin&C;$?',
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Ice Ice',
      'No Scrubs',
      'Dancing Queen',
      'Kangaroo',
      '9 Starts With a Higher Number',
      'Quiznos',
      'Octopus',
      'Helvetica',
    ]);
  }

  /**
   * Tests sorting criteria.
   */
  public function testSortingCriteria(): void {
    $assert_session = $this->assertSession();
    // Clear filters.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Clear Filters');
    $this->pressWithWait('Clear filters');
    $assert_session->elementsCount('css', '#pb-sort option', 4);
    $this->assertEquals('Most Popular', $this->getElementText('#pb-sort option:nth-child(1)'));
    $this->assertEquals('A-Z', $this->getElementText('#pb-sort option:nth-child(2)'));
    $this->assertEquals('Z-A', $this->getElementText('#pb-sort option:nth-child(3)'));
    $this->assertEquals('Newest First', $this->getElementText('#pb-sort option:nth-child(4)'));

    // Select 'A-Z' sorting order.
    $this->sortBy('a_z');

    // Assert that the projects are listed in ascending order of their titles.
    $this->assertProjectsVisible([
      '1 Starts With a Number',
      '9 Starts With a Higher Number',
      'Astronaut Simulator',
      'Become a Banana',
      'Cream cheese on a bagel',
      'Dancing Queen',
      'Doomer',
      'Eggman',
      'Fire',
      'Grapefruit',
      'Helvetica',
      'Ice Ice',
    ]);

    // Select 'Z-A' sorting order.
    $this->sortBy('z_a');

    $this->assertProjectsVisible([
      'Vitamin&C;$?',
      'Unwritten&:/',
      'Tooth Fairy',
      'Soup',
      'Ruh roh',
      'Quiznos',
      'Pinky and the Brain',
      'Octopus',
      'No Scrubs',
      'Mad About You',
      'Looper',
      'Kangaroo',
    ]);

    // Select 'Active installs' option.
    $this->sortBy('usage_total');

    // Assert that the projects are listed in descending order of their usage.
    $this->assertProjectsVisible([
      'Jazz',
      'Eggman',
      'Tooth Fairy',
      'Vitamin&C;$?',
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Ice Ice',
      'No Scrubs',
      'Soup',
      'Mad About You',
      'Dancing Queen',
      'Kangaroo',
    ]);

    // Select 'Newest First' option.
    $this->sortBy('created');

    // Assert that the projects are listed in descending order of their date of
    // creation.
    $this->assertProjectsVisible([
      '9 Starts With a Higher Number',
      'Helvetica',
      'Become a Banana',
      'Ice Ice',
      'Astronaut Simulator',
      'Grapefruit',
      'Fire',
      'Cream cheese on a bagel',
      'No Scrubs',
      'Soup',
      'Octopus',
      'Tooth Fairy',
    ]);
  }

  /**
   * Tests search with strings that need URI encoding.
   */
  public function testSearchForSpecialChar(): void {

    // Clear filters.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', '9 Results');
    $this->pressWithWait('Clear filters', '25 Results');

    // Tests for the presence of search bar placeholder text.
    $search_field = $this->getSession()->getPage()->find('css', '#pb-text');
    $this->assertSame('Module Name, Keyword(s), etc.', $search_field->getAttribute('placeholder'));

    // Fill in the search field.
    $this->inputSearchField('', TRUE);
    $this->inputSearchField('&');
    $this->assertProjectsVisible([
      'Vitamin&C;$?',
      'Unwritten&:/',
    ]);

    // Fill in the search field.
    $this->inputSearchField('', TRUE);
    $this->inputSearchField('n&');
    $this->assertProjectsVisible([
      'Vitamin&C;$?',
      'Unwritten&:/',
    ]);

    $this->inputSearchField('', TRUE);
    $this->inputSearchField('$');
    $this->assertProjectsVisible([
      'Vitamin&C;$?',
    ]);

    $this->inputSearchField('', TRUE);
    $this->inputSearchField('?');
    $this->assertProjectsVisible([
      'Vitamin&C;$?',
    ]);

    $this->inputSearchField('', TRUE);
    $this->inputSearchField('&:');
    $this->assertProjectsVisible([
      'Unwritten&:/',
    ]);

    $this->inputSearchField('', TRUE);
    $this->inputSearchField('$?');
    $this->assertProjectsVisible([
      'Vitamin&C;$?',
    ]);
  }

  /**
   * Tests the detail page.
   */
  public function testDetailPage(): void {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Helvetica');
    $page->clickLink('Helvetica');
    $this->assertTrue($assert_session->waitForText('By Hel Vetica'));
    $assert_session->addressEquals('admin/modules/browse/helvetica');
    $page->clickLink('Back to Browsing');
    $assert_session->addressEquals('admin/modules/browse');
  }

  /**
   * Tests that filtering, sorting, paging persists.
   */
  public function testPersistence(): void {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Clear Filters');
    $this->pressWithWait('Clear filters');

    $this->openAdvancedFilter();

    // Select 'Z-A' sorting order.
    $this->sortBy('z_a');

    // Select the active development status filter.
    $assert_session->waitForElementVisible('css', '#developmentStatusactive');
    $this->clickWithWait('#developmentStatusactive');

    // Select the E-commerce filter.
    $assert_session->waitForElementVisible('css', '#104');
    $this->clickWithWait('#104', '', TRUE);

    // Select the Media filter.
    $assert_session->waitForElementVisible('css', '#67');
    $this->clickWithWait('#67', '', TRUE);

    $this->assertTrue($assert_session->waitForText('15 Results'));
    $this->assertProjectsVisible([
      'Octopus',
      'No Scrubs',
      'Mad About You',
      'Kangaroo',
      'Jazz',
      'Helvetica',
      'Grapefruit',
      'Eggman',
      'Doomer',
      'Dancing Queen',
      'Cream cheese on a bagel',
      'Become a Banana',
    ]);

    $this->clickWithWait('[aria-label="Next page"]');
    $this->assertProjectsVisible([
      'Astronaut Simulator',
      '9 Starts With a Higher Number',
      '1 Starts With a Number',
    ]);
    $this->getSession()->reload();
    // Should still be on second results page.
    $this->svelteInitHelper('css', '#project-browser .pb-project');
    $this->assertProjectsVisible([
      'Astronaut Simulator',
      '9 Starts With a Higher Number',
      '1 Starts With a Number',
    ]);
    $this->assertTrue($assert_session->waitForText('15 Results'));

    $this->assertEquals('Active', $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label'));
    $this->assertEquals('E-commerce', $this->getElementText('p.filter-applied:nth-child(2) .filter-applied__label'));
    $this->assertEquals('Media', $this->getElementText('p.filter-applied:nth-child(3) .filter-applied__label'));

    $this->clickWithWait('[aria-label="First page"]');
    $this->assertProjectsVisible([
      'Octopus',
      'No Scrubs',
      'Mad About You',
      'Kangaroo',
      'Jazz',
      'Helvetica',
      'Grapefruit',
      'Eggman',
      'Doomer',
      'Dancing Queen',
      'Cream cheese on a bagel',
      'Become a Banana',
    ], TRUE);

    $this->assertEquals('Active', $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label'));
    $this->assertEquals('E-commerce', $this->getElementText('p.filter-applied:nth-child(2) .filter-applied__label'));
    $this->assertEquals('Media', $this->getElementText('p.filter-applied:nth-child(3) .filter-applied__label'));
  }

  /**
   * Tests recommended filters.
   */
  public function testRecommendedFilter(): void {
    $assert_session = $this->assertSession();
    // Clear filters.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Clear Filters');
    $this->pressWithWait('Clear filters', '25 Results');
    $this->pressWithWait('Recommended filters');

    // Check that the actively maintained tag is present.
    $this->assertEquals('Maintained', $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label'));
    // Make sure the second filter applied is the security covered filter.
    $this->assertEquals('Covered by a security policy', $this->getElementText('p.filter-applied:nth-child(2) .filter-applied__label'));
    $this->assertTrue($assert_session->waitForText('9 Results'));
  }

  /**
   * Tests multiple source plugins at once.
   */
  public function testMultiplePlugins(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();
    // Enable module for extra source plugin.
    $this->container->get('module_installer')->install(['project_browser_devel'], TRUE);
    // Test categories with multiple plugin enabled.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-filter__checkbox');
    $assert_session->elementsCount('css', '.pb-filter__checkbox', 19);

    $this->svelteInitHelper('css', '#project-browser .pb-project');
    // Count tabs.
    $tab_count = $page->findAll('css', '.pb-tabs__link');
    $this->assertCount(2, $tab_count);
    // Get result count for first tab.
    $this->assertEquals('9 Results', $this->getElementText('.pb-search-results'));

    // Apply filters in drupalorg_mockapi(first tab).
    $assert_session->waitForElement('css', '.views-exposed-form__item input[type="checkbox"]');

    $this->pressWithWait('Clear filters', '25 Results');

    // Click 'E-commerce' checkbox.
    $this->clickWithWait('#104');

    // Click 'Media' checkbox.
    $this->clickWithWait('#67', '20 Results');

    // Filter by search text.
    $this->inputSearchField('Number');
    $this->assertTrue($assert_session->waitForText('2 Results'));
    $this->assertProjectsVisible([
      '9 Starts With a Higher Number',
      '1 Starts With a Number',
    ]);

    // Click other tab.
    $this->pressWithWait('random_data');
    $this->svelteInitHelper('css', '.pb-filter__checkbox');
    $assert_session->elementsCount('css', '.pb-filter__checkbox', 20);
    $assert_session->waitForElementVisible('css', '#project-browser .pb-project');
    $this->assertNotEquals('9 Results Sorted by Active installs', $this->getElementText('.pb-search-results'));
    $assert_session->waitForElementVisible('css', '#project-browser .pb-project');
    $result_count_text = $page->find('css', '.pb-search-results')->getText();
    $this->assertNotEquals('9 Results Sorted by Active installs', $result_count_text);
    // Apply the second module category filter.
    $second_category_filter_selector = '#project-browser > div.pb-layout > .pb-layout__aside > div > form > section > details > fieldset > label:nth-child(3)';
    $this->clickWithWait("$second_category_filter_selector");

    // Save the filter applied in second tab.
    $applied_filter = $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label');
    // Save the number of results.
    $results_before = count($page->findAll('css', '#project-browser .pb-project.list'));

    // Switch back to first tab.
    $this->pressWithWait('drupalorg_mockapi');
    // Assert that the filters persist.
    $this->assertTrue($assert_session->waitForText('2 Results'));
    $first_filter_element = $page->find('css', 'p.filter-applied:nth-child(1)');
    $this->assertEquals('E-commerce', $first_filter_element->find('css', '.filter-applied__label')->getText());
    $second_filter_element = $page->find('css', 'p.filter-applied:nth-child(2)');
    $this->assertEquals('Media', $second_filter_element->find('css', '.filter-applied__label')->getText());
    $this->assertProjectsVisible([
      '9 Starts With a Higher Number',
      '1 Starts With a Number',
    ]);

    // Again switch to second tab.
    $this->pressWithWait('random_data');
    // Assert that the filters persist.
    $this->assertEquals($applied_filter, $this->getElementText('p.filter-applied:nth-child(1) .filter-applied__label'));

    // Assert that the number of results is the same.
    $results_after = count($page->findAll('css', '#project-browser .pb-project.list'));
    $this->assertEquals($results_before, $results_after);
  }

  /**
   * Tests the view mode toggle keeps its state.
   */
  public function testToggleViewState(): void {
    $page = $this->getSession()->getPage();
    $viewSwitches = [
      [
        'selector' => '.pb-display__button[value="Grid"]',
        'value' => 'Grid',
      ], [
        'selector' => '.pb-display__button[value="List"]',
        'value' => 'List',
      ],
    ];
    $this->getSession()->resizeWindow(1300, 1300);

    foreach ($viewSwitches as $selector) {
      $this->drupalGet('admin/modules/browse');
      $this->svelteInitHelper('css', $selector['selector']);
      $this->getSession()->getPage()->pressButton($selector['value']);
      $this->svelteInitHelper('text', 'Helvetica');
      $page->clickLink('Helvetica');
      $this->svelteInitHelper('text', 'Back to Browsing');
      $page->clickLink('Back to Browsing');
      $this->assertSession()->elementExists('css', $selector['selector'] . '.pb-display__button--selected');

    }
  }

  /**
   * Tests tabledrag on configuration page.
   */
  public function testTabledrag(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();
    $this->container->get('module_installer')->install(['project_browser_devel'], TRUE);

    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Random data');
    // Count tabs.
    $tab_count = $page->findAll('css', '.pb-tabs__link');
    $this->assertCount(2, $tab_count);

    // Verify that Drupal.org mockapi is first tab.
    $first_tab = $page->find('css', '.pb-tabs__link:nth-child(1)');
    $this->assertEquals('drupalorg_mockapi', $first_tab->getValue());

    // Re-order plugins.
    $this->drupalGet('admin/config/development/project_browser');
    $first_plugin = $page->find('css', '#source--drupalorg_mockapi');
    $second_plugin = $page->find('css', '#source--random_data');
    $first_plugin->find('css', '.handle')->dragTo($second_plugin);
    $this->assertTableRowWasDragged($first_plugin);
    $this->submitForm([], 'Save');

    // Verify that Random data is first tab.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('text', 'Drupal.org (mocked)');
    $first_tab = $page->find('css', '.pb-tabs__link:nth-child(1)');
    $this->assertEquals('random_data', $first_tab->getValue());

    // Disable Drupal.org mockapi plugin.
    $this->drupalGet('admin/config/development/project_browser');
    $enabled_row = $page->find('css', '#source--drupalorg_mockapi');
    $disabled_region_row = $page->find('css', '.status-title-disabled');
    $enabled_row->find('css', '.handle')->dragTo($disabled_region_row);
    $this->assertTableRowWasDragged($enabled_row);
    $this->submitForm([], 'Save');
    $assert_session->pageTextContains('The configuration options have been saved.');

    // Verify that only Random data plugin is enabled.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-filter__checkbox');
    $assert_session->elementsCount('css', '.pb-filter__checkbox', 20);

    // Enable only Drupal.org mockapi plugin through config update. It is done
    // this way because dragging was not working reliably for enabling
    // Drupal.org mockapi plugin.
    $this->config('project_browser.admin_settings')->set('enabled_sources', ['drupalorg_mockapi'])->save(TRUE);
    $this->drupalGet('admin/config/development/project_browser');
    $this->assertTrue($assert_session->optionExists('edit-enabled-sources-drupalorg-mockapi-status', 'enabled')->isSelected());
    $this->assertTrue($assert_session->optionExists('edit-enabled-sources-random-data-status', 'disabled')->isSelected());

    // Verify that only Drupal.org mockapi plugin is enabled.
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-filter__checkbox');
    $assert_session->elementsCount('css', '.pb-filter__checkbox', 19);
  }

  /**
   * Tests the visibility of categories in list and grid view.
   */
  public function testCategoriesVisibility(): void {
    $assert_session = $this->assertSession();
    $view_options = [
      [
        'selector' => '.pb-display__button[value="Grid"]',
        'value' => 'Grid',
      ], [
        'selector' => '.pb-display__button[value="List"]',
        'value' => 'List',
      ],
    ];
    $this->getSession()->resizeWindow(1300, 1300);

    // Check visibility of categories in each view.
    foreach ($view_options as $selector) {
      $this->drupalGet('admin/modules/browse');
      $this->svelteInitHelper('css', $selector['selector']);
      $this->getSession()->getPage()->pressButton($selector['value']);
      $this->svelteInitHelper('text', 'Helvetica');
      $assert_session->elementsCount('css', '#project-browser .pb-layout__main ul li:nth-child(7) .pb-project-categories ul li', 1);
      $grid_text = $this->getElementText('#project-browser .pb-layout__main ul li:nth-child(7) .pb-project-categories ul li:nth-child(1)');
      $this->assertEquals('E-commerce', $grid_text);
      $assert_session->elementsCount('css', '#project-browser .pb-layout__main  ul li:nth-child(9) .pb-project-categories ul li', 2);
      $grid_text = $this->getElementText('#project-browser .pb-layout__main ul li:nth-child(7) .pb-project-categories ul li:nth-child(1)');
      $this->assertEquals('E-commerce', $grid_text);
      $grid_text = $this->getElementText('#project-browser .pb-layout__main ul li:nth-child(9) .pb-project-categories ul li:nth-child(2)');
      $this->assertEquals('E-commerce', $grid_text);
    }
  }

  /**
   * Tests the pagination and filtering.
   */
  public function testPaginationWithFilters(): void {
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/modules/browse');
    $this->pressWithWait('Clear filters');
    $this->assertProjectsVisible([
      'Jazz',
      'Eggman',
      'Tooth Fairy',
      'Vitamin&C;$?',
      'Cream cheese on a bagel',
      'Pinky and the Brain',
      'Ice Ice',
      'No Scrubs',
      'Soup',
      'Mad About You',
      'Dancing Queen',
      'Kangaroo',
    ]);

    $this->assertPagerItems(['1', '2', '3', 'Next', 'Last']);
    $this->clickWithWait('[aria-label="Last page"]');
    $this->assertProjectsVisible([
      'Astronaut Simulator',
    ]);

    // Click 'Media' checkbox.
    $this->clickWithWait('#67');
    $this->assertPagerItems(['1', '2', 'Next', 'Last']);
    $assert_session->elementExists('css', '.pager__item--active > .is-active[aria-label="Page 1"]');
  }

  /**
   * Tests install button link.
   */
  public function testInstallButtonLink(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();
    $this->config('project_browser.admin_settings')
      ->set('enabled_sources', ['drupal_core'])
      ->save(TRUE);
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-project.pb-project--list');

    $this->inputSearchField('inline form errors');
    $this->svelteInitHelper('text', 'Inline Form Errors');

    $install_link = $page->find('css', '.pb-layout__main .pb-actions a');

    $this->assertStringContainsString('admin/modules#module-inline-form-errors', $install_link->getAttribute('href'));
    $this->drupalGet($install_link->getAttribute('href'));
    $assert_session->waitForElementVisible('css', "#edit-modules-inline-form-errors-enable");
    $assert_session->assertVisibleInViewport('css', '#edit-modules-inline-form-errors-enable');
  }

  /**
   * Confirms UI install can not be enabled without Package Manager installed.
   */
  public function testUiInstallNeedsPackageManager() {
    $this->drupalGet('admin/config/development/project_browser');
    $ui_install_input = $this->getSession()->getPage()->find('css', '[data-drupal-selector="edit-allow-ui-install"]');
    $this->assertTrue($ui_install_input->getAttribute('disabled') === 'disabled');

    // @todo Remove try/catch in https://www.drupal.org/i/3349193.
    try {
      $this->container->get('module_installer')->install(['package_manager'], TRUE);
    }
    catch (MissingDependencyException $e) {
      $this->markTestSkipped($e->getMessage());
    }
    $this->drupalGet('admin/config/development/project_browser');
    $ui_install_input = $this->getSession()->getPage()->find('css', '[data-drupal-selector="edit-allow-ui-install"]');
    $this->assertFalse($ui_install_input->hasAttribute('disabled'));
  }

  /**
   * Tests that we can clear search results with one click.
   */
  public function testClearKeywordSearch() {
    $assert_session = $this->assertSession();
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-search-results');

    // Get the original result count.
    $results = $assert_session->elementExists('css', '.pb-search-results');
    $this->assertTrue($results->waitFor(10, fn (NodeElement $element) => str_contains($element->getText(), 'Results')));
    $original_text = $results->getText();

    // Search for something to change it.
    $this->inputSearchField('abcdefghijklmnop');
    $this->assertTrue($results->waitFor(10, fn (NodeElement $element) => $element->getText() !== $original_text));

    // Remove the search text and make sure it auto-updates.
    // Use our clear search button to do it.
    $assert_session->elementExists('css', '.search__search-clear')->click();
    $this->assertTrue($results->waitFor(10, fn (NodeElement $element) => $element->getText() === $original_text));
  }

  /**
   * Test that the clear search link is not in the tab-index.
   *
   * @see https://www.drupal.org/project/project_browser/issues/3446109
   */
  public function testSearchClearNoTabIndex(): void {
    $page = $this->getSession()->getPage();
    $this->assertSession();
    $this->drupalGet('admin/modules/browse');
    $this->svelteInitHelper('css', '.pb-search-results');

    // Search and confirm clear button has no focus after tabbing.
    $this->inputSearchField('abcdefghijklmnop');

    $this->getSession()->getDriver()->keyPress($page->getXpath(), '9');
    $has_focus_id = $this->getSession()->evaluateScript('document.activeElement.id');
    $this->assertNotEquals('clear-text', $has_focus_id);
  }

}
