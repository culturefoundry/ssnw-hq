<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Config\Action\ConfigActionException;
use Drupal\Core\Config\Action\ConfigActionManager;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the Schema.org config actions.
 *
 * @group config
 */
class SchemaDotOrgConfigActionTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'user',
    'block_content',
    'schemadotorg',
    'schemadotorg_address',
    'schemadotorg_block_content',
  ];

  /**
   * The config action manager.
   */
  protected ConfigActionManager $configActionManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig('schemadotorg');
    $this->installConfig('schemadotorg_address');
    $this->installConfig('schemadotorg_block_content');

    $this->configActionManager = $this->container->get('plugin.manager.config_action');
  }

  /**
   * Test execute install hook config action.
   */
  public function testExecuteInstallHook(): void {
    // Call the simple config update action with an array of modules.
    $this->configActionManager->applyAction('executeInstallHook', 'core.extension', ['schemadotorg', 'schemadotorg_address']);

    // Check that the schemadotorg_address_install() hook was executed.
    $this->assertEquals(
      ['address', 'string_long'],
      $this->config('schemadotorg.settings')
        ->get('schema_types.default_field_types.PostalAddress')
    );

    // Check that the 'schemadotorg_address' installed hook is tracked.
    $installed_hooks = \Drupal::state()->get('schemadotorg.installed_hooks') ?? [];
    $this->assertEquals([
      'schemadotorg_address' => 'schemadotorg_address',
    ], $installed_hooks);

    // Call the simple config update action with a module prefix.
    $this->configActionManager->applyAction('executeInstallHook', 'core.extension', 'schemadotorg');
    $installed_hooks = \Drupal::state()->get('schemadotorg.installed_hooks') ?? [];
    $this->assertEquals([
      'schemadotorg_address' => 'schemadotorg_address',
      'schemadotorg_block_content' => 'schemadotorg_block_content',
    ], $installed_hooks);

    // Check that the 'schemadotorg_address' installed hook is removed via uninstall.
    schemadotorg_modules_uninstalled(['schemadotorg_address']);
    $installed_hooks = \Drupal::state()->get('schemadotorg.installed_hooks') ?? [];
    $this->assertEquals(['schemadotorg_block_content' => 'schemadotorg_block_content'], $installed_hooks);

    // Check that not 'core.extension' exception is thrown as expected.
    try {
      $this->configActionManager->applyAction('executeInstallHook', 'not.core.extension', ['schemadotorg', 'schemadotorg_address']);
      $this->fail('Expected exception not thrown');
    }
    catch (ConfigActionException $e) {
      $this->assertSame("The 'executeInstallHook' config action can only be triggered via core.extension.", $e->getMessage());
    }
  }

}
