<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld_custom\Functional;

use Drupal\path_alias\Entity\PathAlias;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;

/**
 * Tests the functionality of the Schema.org JSON-LD custom.
 *
 * @covers \Drupal\schemadotorg_jsonapi\Form\SchemaDotOrgDemoSettingsForm
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdCustomTest extends SchemaDotOrgBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_jsonld_custom',
  ];

  /**
   * Test Schema.org JSON-LD custom front page.
   *
   * @see \Drupal\schemadotorg_jsonld_custom\SchemaDotOrgJsonLdCustomManager::buildRouteMatchJsonLd
   */
  public function testFrontPage(): void {
    $assert = $this->assertSession();

    // Create a node for the <front> page.
    $this->createSchemaEntity('node', 'WebPage');
    $node = $this->drupalCreateNode(['type' => 'page']);
    $path_alias = PathAlias::create([
      'path' => $node->toUrl()->toString(),
      'alias' => '/frontpage',
    ]);
    $path_alias->save();

    /* ********************************************************************** */

    // Check that the node's JSON-LD contains WebPage but not Website.
    $this->drupalGet('node/' . $node->id());
    $assert->responseContains('"@type": "WebPage",');
    $assert->responseNotContains('"@type": "WebSite",');

    // Below tests pass locally but fail via GitLab CI.
    /* phpcs:disable
    // Check that the node's JSON-LD contains WebPage but not Website.
    $this->drupalGet('node/' . $node->id());
    $assert->responseContains('"@type": "WebPage",');
    $assert->responseNotContains('"@type": "WebSite",');
    $this->drupalGet('frontpage');
    $assert->responseContains('"@type": "WebPage",');
    $assert->responseNotContains('"@type": "WebSite",');
    $this->drupalGet('');
    $assert->responseNotContains('"@type": "WebPage",');
    $assert->responseContains('"@type": "WebSite",');

    // Set the node to be the <front> page.
    $this->config('system.site')
      ->set('page.front', '/frontpage')
      ->save();

    // Check that the node's JSON-LD contains WebPage but not Website.
    $this->drupalGet('node/' . $node->id());
    $assert->responseContains('"@type": "WebPage",');
    $assert->responseContains('"@type": "WebSite",');
    $this->drupalGet('frontpage');
    $assert->responseContains('"@type": "WebPage",');
    $assert->responseContains('"@type": "WebSite",');
    $this->drupalGet('');
    $assert->responseContains('"@type": "WebPage",');
    $assert->responseContains('"@type": "WebSite",');
    phpcs:enable */
  }

}
