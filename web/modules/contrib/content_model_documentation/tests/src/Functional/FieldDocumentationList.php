<?php

namespace Drupal\Tests\content_model_documentation\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Defines a class for testing Content model field list functionality.
 *
 * @group content_model_documentation
 */
final class FieldDocumentationList extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['content_model_documentation'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests visibility on admin pages.
   */
  public function testContentModelFieldsListShows(): void {
    $this->drupalLogin($this->createUser([], NULL, TRUE));
    $this->drupalGet('/admin/reports/fields/content-model');
    $assert = $this->assertSession();
    $assert->elementExists('css', '.view-content-model-fields');
  }

}
