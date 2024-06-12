<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_role\Functional;

use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org role mapping UI form.
 *
 * @covers \Drupal\schemadotorg_role\SchemaDotOrgRoleFieldManager::mappingDefaultsAlter
 * @covers \Drupal\schemadotorg_role\SchemaDotOrgRoleFieldManager::mappingFormAlter
 * @group schemadotorg
 */
class SchemaDotOrgRoleMappingUiFormTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_ui', 'schemadotorg_role'];

  /**
   * Test Schema.org role mapping ui form.
   */
  public function testMappingUi(): void {
    global $base_path;

    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    // Check that field creation form is replaced with text and edit links.
    $this->drupalGet('admin/structure/types/schemadotorg', ['query' => ['type' => 'PodcastEpisode']]);
    $assert->responseContains('<p>The <em class="placeholder">actor</em> property is mapped to the below role-related fields.</p>');
    $assert->responseContains('<ul data-drupal-selector="edit-mapping-properties-actor-field-data-fields">');
    $assert->responseContains('<li>Hosts (schema_role_host)</li>');
    $assert->responseContains('<li>Guests (schema_role_guest)</li>');
    $assert->responseContains('<a href="' . $base_path . 'admin/config/schemadotorg/settings/properties?destination=' . $base_path . 'admin/structure/types/schemadotorg%3Ftype%3DPodcastEpisode#edit-schemadotorg-role" class="button button--small button--extrasmall" data-drupal-selector="edit-mapping-properties-actor-field-data-edit" id="edit-mapping-properties-actor-field-data-edit">Edit settings</a>');
  }

}
