<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_ui\Functional;

use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org UI route subscriber and more.
 *
 * @covers \Drupal\schemadotorg_ui\Routing\SchemaDotOrgRouteSubscriber
 * @covers \Drupal\schemadotorg_ui\Plugin\Derivative\SchemaDotOrgUiLocalAction
 * @covers \Drupal\schemadotorg_ui\Plugin\Derivative\SchemaDotOrgUiLocalTask
 * @covers \Drupal\schemadotorg_ui\Plugin\Derivative\SchemaDotOrgUiMenuLink
 *
 * @group schemadotorg
 */
class SchemaDotOrgUiRouteTest extends SchemaDotOrgBrowserTestBase {
  use MediaTypeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'media',
    'field',
    'field_ui',
    'schemadotorg_media',
    'schemadotorg_ui',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');

    $account = $this->drupalCreateUser([
      'administer user fields',
      'administer content types',
      'administer node fields',
      'administer media types',
      'administer media fields',
    ]);
    $this->drupalLogin($account);
  }

  /**
   * Test Schema.org UI routes, actions, tasks, and menu links.
   */
  public function testSchemaDotOrgUiRoutes(): void {
    global $base_path;

    $assert = $this->assertSession();

    /* ********************************************************************** */
    // Routes.
    // @see \Drupal\schemadotorg_ui\Routing\SchemaDotOrgRouteSubscriber
    /* ********************************************************************** */

    // Check that node 'Add Schema.org type' route exists.
    $this->assertRouteExists('schemadotorg.node_type.type_add');
    $this->drupalGet('admin/structure/types/schemadotorg');
    $assert->statusCodeEquals(200);

    // Check that media 'Add Schema.org type' route does exist.
    $this->assertRouteExists('schemadotorg.media_type.type_add');
    $this->drupalGet('admin/structure/media/schemadotorg');
    $assert->statusCodeEquals(200);

    /* ********************************************************************** */
    // Local actions.
    // @see \Drupal\schemadotorg_ui\Plugin\Derivative\SchemaDotOrgUiLocalAction
    /* ********************************************************************** */

    // Check that node 'Add Schema.org type' action exists.
    $this->drupalGet('admin/structure/types');
    $assert->statusCodeEquals(200);
    $assert->linkExists('Add Schema.org type');
    $assert->linkByHrefExists($base_path . 'admin/structure/types/schemadotorg');

    // Check that media 'Add Schema.org type' action exists.
    $this->drupalGet('admin/structure/media');
    $assert->statusCodeEquals(200);
    $assert->linkExists('Add Schema.org type');
    $assert->linkByHrefExists($base_path . 'admin/structure/media/schemadotorg');

    /* ********************************************************************** */
    // Local tasks.
    // @see \Drupal\schemadotorg_ui\Plugin\Derivative\SchemaDotOrgUiLocalTask
    /* ********************************************************************** */

    // Check that node 'Schema.org' task exists.
    $content_type = $this->drupalCreateContentType();
    $content_type_id = $content_type->id();
    $this->drupalGet("/admin/structure/types/manage/$content_type_id/fields");
    $assert->statusCodeEquals(200);
    $assert->linkExists('Schema.org');
    $assert->linkByHrefExists($base_path . "admin/structure/types/manage/$content_type_id/schemadotorg");
    $this->drupalGet("/admin/structure/types/manage/$content_type_id/schemadotorg");
    $assert->statusCodeEquals(200);

    // Check that media 'Schema.org' task exists.
    $media_type = $this->createMediaType('image', ['id' => 'image']);
    $media_type_id = $media_type->id();
    $this->drupalGet("/admin/structure/media/manage/$media_type_id/fields");
    $assert->statusCodeEquals(200);
    $assert->linkExists('Schema.org');
    $assert->linkByHrefExists($base_path . "admin/structure/media/manage/$media_type_id/schemadotorg");
    $this->drupalGet("/admin/structure/media/manage/$media_type_id/schemadotorg");
    $assert->statusCodeEquals(200);

    // Check that user 'Schema.org' task exists.
    $this->drupalGet("/admin/config/people/accounts/fields");
    $assert->statusCodeEquals(200);
    $assert->linkExists('Schema.org');
    $assert->linkByHrefExists($base_path . "admin/config/people/accounts/schemadotorg");
    $this->drupalGet("/admin/config/people/accounts/schemadotorg");
    $assert->statusCodeEquals(200);

    /* ********************************************************************** */
    // Menu links.
    // @see \Drupal\schemadotorg_ui\Plugin\Derivative\SchemaDotOrgUiMenuLink
    /* ********************************************************************** */

    /** @var \Drupal\Core\Menu\MenuLinkManagerInterface $menu_link_manager */
    $menu_link_manager = \Drupal::service('plugin.manager.menu.link');

    // Check that node 'Add Schema.org type' menu link exists.
    $menu_links = $menu_link_manager->loadLinksByRoute('schemadotorg.node_type.type_add');
    $this->assertCount(1, $menu_links);
    $menu_link = reset($menu_links);
    $this->assertEquals('entity.node_type.collection', $menu_link->getParent());

    // Check that media 'Add Schema.org type' menu link exists.
    $menu_links = $menu_link_manager->loadLinksByRoute('schemadotorg.media_type.type_add');
    $this->assertCount(1, $menu_links);
    $menu_link = reset($menu_links);
    $this->assertEquals('entity.media_type.collection', $menu_link->getParent());

    // Check that uninstalling the schemadotorg_media module removes
    // routes and links.
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $installer */
    $installer = \Drupal::service('module_installer');
    $installer->uninstall(['schemadotorg_media']);
    drupal_flush_all_caches();

    // Check that add Schema.org media type route is removed.
    $this->drupalGet('admin/structure/media/schemadotorg');
    $assert->statusCodeEquals(404);
    // Check that Add Schema.org media type action is removed.
    $this->drupalGet('admin/structure/media');
    $assert->linkNotExists('Add Schema.org type');
    $assert->linkByHrefNotExists($base_path . 'admin/structure/media/schemadotorg');
    // Check that Add Schema.org media type menu link is removed.
    $menu_links = $menu_link_manager->loadLinksByRoute('schemadotorg.media_type.type_add');
    $this->assertCount(0, $menu_links);
  }

  /**
   * Assert route exists.
   *
   * @param string $name
   *   A route name.
   */
  protected function assertRouteExists(string $name): void {
    $router = $this->container->get('router');
    $this->assertTrue((boolean) $router->getRouteCollection()->get($name));
  }

  /**
   * Assert route not exists.
   *
   * @param string $name
   *   A route name.
   */
  protected function assertRouteNotExists(string $name): void {
    $router = $this->container->get('router');
    $this->assertFalse((boolean) $router->getRouteCollection()->get($name));
  }

}
