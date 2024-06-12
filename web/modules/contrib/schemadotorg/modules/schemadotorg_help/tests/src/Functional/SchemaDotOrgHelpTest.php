<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_help\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org help.
 *
 * @group schemadotorg
 */
class SchemaDotOrgHelpTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_help', 'schemadotorg_diagram'];

  /**
   * Test help.
   */
  public function testHelp(): void {
    global $base_path;

    $assert = $this->assertSession();
    $this->drupalLogin($this->createUser(['access help pages', 'administer schemadotorg']));

    $module_path = \Drupal::service('extension.list.module')->getPath('schemadotorg');

    // Check displaying help topics for all Schema.org Blueprints sub-modules.
    $this->drupalGet('admin/help');
    $assert->responseContains('<h2><img class="schemadotorg-logo" src="' . $base_path . $module_path . '/logo.png" alt="Logo for the Schema.org Blueprints project" />
Schema.org Blueprints</h2>');
    $assert->responseContains('<p>The Schema.org Blueprints module uses Schema.org as the blueprint for the content architecture and structured data in a Drupal website.</p>');
    $assert->responseContains('<li><strong><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg">Schema.org Blueprints</a></strong></li>');
    $assert->responseContains('<li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_diagram">Diagram</a></li>');
    $assert->responseContains('<li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_help">Help</a></li>');
    $assert->responseNotContains('<li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_diagram">Schema.org Blueprints Diagram</a></li>');
    $assert->responseNotContains('<li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_help">Schema.org Blueprints Help</a></li>');

    // Check help topic navigation.
    $this->drupalGet('admin/help/schemadotorg/schemadotorg_help');
    $assert->responseContains('<div class="dropbutton-wrapper" data-drupal-ajax-container><div class="dropbutton-widget"><ul class="dropbutton"><li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg">Learn more about the Schema.org Blueprints modules</a></li><li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_diagram">Diagram</a></li><li><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_help">Help</a></li></ul></div></div>');
    $assert->responseContains('&nbsp; or &nbsp;');
    $assert->responseContains('<a href="' . $base_path . 'admin/help/schemadotorg/videos" class="use-ajax button button--small button--extrasmall" data-dialog-type="modal" data-dialog-options="{&quot;width&quot;:800}">► Watch videos</a>');

    // Check converting a sub-module's README.md markdown into HTML.
    $this->drupalGet('admin/help/schemadotorg/schemadotorg_help');
    $assert->responseContains('<li>Displays help topics for all Schema.org Blueprints sub-modules.</li>');
    $assert->responseContains('<li>Converts a sub-module\'s README.md markdown into HTML.</li>');
    $assert->responseContains('<li>Manages videos and provides a watch dialog.</li>');

    // Check that help videos display as expected.
    $this->drupalGet('admin/help/schemadotorg/videos');
    $assert->responseContains('<a href="https://youtu.be/Yo6Vw-s1FtM" target="_blank">');
    $assert->responseContains('<img style="display: block" src="https://img.youtube.com/vi/Yo6Vw-s1FtM/0.jpg" alt="Schema.org Blueprints for Drupal @ Pittsburgh 2023" />');
    $assert->responseContains('<a href="https://youtu.be/Yo6Vw-s1FtM" target="_blank" class="button button--small button--extrasmall">▶ Watch video</a>');

    // Check that for each Schema.org Blueprints sub-module's configuration
    // settings the description includes the sub-module's info with
    // a link to help/documentation.
    $this->drupalGet('admin/config/schemadotorg/settings/general');
    $assert->responseContains('<strong>About the Schema.org Blueprints Diagram module.</strong><br />');
    $assert->responseContains('Provides diagrams for Schema.org relationships.<br />');
    $assert->responseContains('<span class="schemadotorg-help-read"><a href="' . $base_path . 'admin/help/schemadotorg/schemadotorg_diagram" target="_blank" title="Read help/documentation for the Schema.org Blueprints Diagram module.">Read help/documentation</a></span>');
  }

}
