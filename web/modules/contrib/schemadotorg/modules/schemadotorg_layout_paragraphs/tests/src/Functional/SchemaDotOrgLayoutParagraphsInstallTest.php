<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_paragraphs\Functional;

use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org layout paragraphs installation.
 *
 * @covers schemadotorg_layout_paragraphs_install()
 * @group schemadotorg
 */
class SchemaDotOrgLayoutParagraphsInstallTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_layout_paragraphs',
  ];

  /**
   * Test Schema.org layout paragraphs installation.
   */
  public function testInstall(): void {
    $paragraphs_type = ParagraphsType::load('layout');
    $expected_behavior_plugins = [
      'layout_paragraphs' => [
        'enabled' => TRUE,
        'available_layouts' => [
          'layout_onecol' => 'layout_onecol',
          'layout_twocol_section' => 'layout_twocol_section',
          'layout_twocol' => 'layout_twocol',
          'layout_twocol_bricks' => 'layout_twocol_bricks',
          'layout_threecol_section' => 'layout_threecol_section',
          'layout_threecol_25_50_25' => 'layout_threecol_25_50_25',
          'layout_threecol_33_34_33' => 'layout_threecol_33_34_33',
          'layout_fourcol_section' => 'layout_fourcol_section',
        ],
      ],
    ];
    $this->assertEquals($expected_behavior_plugins, $paragraphs_type->get('behavior_plugins'));
  }

}
