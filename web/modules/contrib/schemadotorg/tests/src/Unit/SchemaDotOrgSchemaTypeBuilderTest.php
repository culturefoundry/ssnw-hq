<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Unit;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilder;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilder::getItemUrl
 * @group schemadotorg
 */
class SchemaDotOrgSchemaTypeBuilderTest extends UnitTestCase {

  /**
   * The mock module handler.
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The mock current user.
   */
  protected AccountInterface $currentUser;

  /**
   * The mock Schema.org type manager.
   */
  protected SchemaDotOrgSchemaTypeManager $schemaTypeManager;

  /**
   * The Schema.org type builder.
   */
  protected SchemaDotOrgSchemaTypeBuilder $schemaTypeBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->moduleHandler = $this->createMock('Drupal\Core\Extension\ModuleHandlerInterface');

    $this->currentUser = $this->createMock('Drupal\Core\Session\AccountInterface');

    $this->schemaTypeManager = $this->createMock('Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager');

    $this->schemaTypeBuilder = new SchemaDotOrgSchemaTypeBuilder(
      $this->moduleHandler,
      $this->currentUser,
      $this->schemaTypeManager
    );
  }

  /**
   * Tests SchemaDotOrgSchemaTypeBuilder::getItemUrl().
   *
   * @covers ::getItemUrl
   */
  public function testGetItemUrl(): void {
    $this->moduleHandler
      ->method('moduleExists')
      ->with('schemadotorg_report')
      ->willReturnOnConsecutiveCalls(FALSE, TRUE);

    $this->currentUser->method('hasPermission')->willReturn(TRUE);

    // Check that Schema.org URI is returned when the schemadotorg_report module
    // is not installed.
    $item_url = $this->schemaTypeBuilder->getItemUrl('Thing');
    $this->assertTrue($item_url->isExternal());
    $this->assertEquals('https://schema.org/Thing', $item_url->getUri());

    // Check that Schema.org URI is returned when the schemadotorg_report module
    // is not installed.
    $item_url = $this->schemaTypeBuilder->getItemUrl('Thing');
    $this->assertFalse($item_url->isExternal());
    $this->assertEquals('schemadotorg_report', $item_url->getRouteName());
    $this->assertEquals(['id' => 'Thing'], $item_url->getRouteParameters());
  }

  /**
   * Tests SchemaDotOrgSchemaTypeBuilder::buildItemsLinks().
   *
   * @covers ::buildItemsLinks
   */
  public function testBuildItemsLinks(): void {
    $this->moduleHandler->method('moduleExists')->willReturn(FALSE);
    $this->currentUser->method('hasPermission')->willReturn(TRUE);
    $this->schemaTypeManager->method('parseIds')->willReturn(['Thing', 'NotThing']);
    $this->schemaTypeManager->method('isItem')->willReturnMap([
      ['Thing', TRUE],
      ['NotThing', FALSE],
    ]);

    $item_links = $this->schemaTypeBuilder->buildItemsLinks('https://schema.org/Thing, https://schema.org/NotThing', ['attributes' => ['target' => 'top']]);

    // Check that Thing is a link.
    $this->assertEquals('link', $item_links[0]['#type']);
    $this->assertEquals('Thing', $item_links[0]['#title']);
    $this->assertEquals('', $item_links[0]['#prefix']);
    $this->assertEquals(['target' => 'top'], $item_links[0]['#attributes']);

    // Check that NotThing is a plain text with comma prefix.
    $this->assertEquals('https://schema.org/Thing', $item_links[0]['#url']->getUri());
    $this->assertEquals('NotThing', $item_links[1]['#plain_text']);
    $this->assertEquals(', ', $item_links[1]['#prefix']);
  }

  /**
   * Tests SchemaDotOrgSchemaTypeBuilder::buildTypeTree().
   *
   * @covers ::buildTypeTree
   */
  public function testBuildTypeTree(): void {
    $this->moduleHandler->method('moduleExists')->willReturn(FALSE);
    $this->currentUser->method('hasPermission')->willReturn(TRUE);

    $tree = [
      'Root' => [
        'subtypes' => [
          'Child_01' => [
            'subtypes' => [
              'Grandchild_01' => [],
            ],
          ],
        ],
        'enumerations' => [
          'Child_02' => [],
        ],
      ],
    ];

    $type_tree = $this->schemaTypeBuilder->buildTypeTree($tree);

    // Check Root item.
    $this->assertEquals('item_list', $type_tree['#theme']);
    $this->assertEquals('link', $type_tree['#items']['Root']['#type']);
    $this->assertEquals('Root', $type_tree['#items']['Root']['#title']);
    $this->assertEquals('https://schema.org/Root', $type_tree['#items']['Root']['#url']->getUri());

    // Check Child 01 item.
    $this->assertEquals('item_list', $type_tree['#items']['Root']['children']['#theme']);
    $this->assertEquals('link', $type_tree['#items']['Root']['children']['#items']['Child_01']['#type']);
    $this->assertEquals('Child_01', $type_tree['#items']['Root']['children']['#items']['Child_01']['#title']);
    $this->assertEquals('https://schema.org/Child_01', $type_tree['#items']['Root']['children']['#items']['Child_01']['#url']->getUri());

    // Check Child 02 item.
    $this->assertEquals('item_list', $type_tree['#items']['Root']['children']['#theme']);
    $this->assertEquals('link', $type_tree['#items']['Root']['children']['#items']['Child_02']['#type']);
    $this->assertEquals('Child_02', $type_tree['#items']['Root']['children']['#items']['Child_02']['#title']);
    $this->assertEquals('https://schema.org/Child_02', $type_tree['#items']['Root']['children']['#items']['Child_02']['#url']->getUri());
  }

  /**
   * Tests SchemaDotOrgSchemaTypeBuilder::formatComment().
   *
   * @param string $comment
   *   A Schema.org comment.
   * @param array $options
   *   An array of options.
   * @param string $expected
   *   SchemaDotOrgSchemaTypeBuilder::formatComment() expected result.
   *
   * @dataProvider providerTestFormatComment
   * @covers ::formatComment
   */
  public function testFormatComment(string $comment, array $options, string $expected): void {
    $this->moduleHandler->method('moduleExists')->willReturn(FALSE);
    $this->currentUser->method('hasPermission')->willReturn(TRUE);

    $actual = $this->schemaTypeBuilder->formatComment($comment, $options);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Provides test data for testApplies().
   *
   * @return array
   *   Array of datasets for testApplies(). Structured as such:
   *   - SchemaDotOrgReportBreadcrumbBuilder::applies() expected result.
   *   - SchemaDotOrgReportBreadcrumbBuilder::applies() route name.
   */
  public function providerTestFormatComment(): array {
    return [
      [
        'This is a comment.',
        [],
        'This is a comment.',
      ],
      [
        'This is a <a href="/Thing" class="some-class">Thing</a>.',
        [],
        'This is a <a href="https://schema.org/Thing">Thing</a>.',
      ],
      [
        'This is a <a href="/Thing" class="some-class">Thing</a>.',
        ['attributes' => ['target' => 'top']],
        'This is a <a href="https://schema.org/Thing" target="top">Thing</a>.',
      ],
      [
        'This is a <a href="/Thing" class="some-class">Thing</a>.',
        ['base_path' => 'https://somewhere.com/'],
        'This is a <a href="https://somewhere.com/Thing">Thing</a>.',
      ],
      [
        'This is a <a href="/Thing" class="some-class">Thing</a> and some <a href="/some/docs">docs</a>.',
        ['base_path' => 'https://somewhere.com/'],
        'This is a <a href="https://somewhere.com/Thing">Thing</a> and some <a href="https://schema.org/some/docs">docs</a>.',
      ],
    ];
  }

}
