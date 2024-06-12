<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\node\Entity\Node;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Tests the views filter for Schema.org types.
 *
 * @coversDefaultClass \Drupal\schemadotorg\Plugin\views\filter\SchemaDotOrgViewsSchemaTypeFilter
 *
 * @group schemadotorg
 */
class SchemaDotOrgViewsSchemaTypeFilterKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * Views to be enabled.
   *
   * @var array
   */
  public static $testViews = [];

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block_content',
    'views',
    'schemadotorg_views_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(static::$modules);
  }

  /**
   * Tests views Schema.org type filter.
   */
  public function testSchemaTypeFilterMultiple(): void {
    // Create Place with mapping.
    $this->createSchemaEntity('node', 'Place');

    // Create Organization with mapping.
    $this->createSchemaEntity('node', 'Organization');

    // Create Person with mapping.
    $this->createSchemaEntity('node', 'Person');

    // Test as a non-admin.
    $this->drupalSetUpCurrentUser();

    // Create nodes.
    $types = ['place', 'place', 'organization', 'person'];
    foreach ($types as $type) {
      Node::create(['type' => $type, 'title' => $type, 'status' => TRUE])->save();
    }

    /* ********************************************************************** */

    // Get the view being tested.
    $view = Views::getView('schemadotorg_type_filter');

    // Tests \Drupal\views\Plugin\views\filter\Bundle::calculateDependencies().
    $expected_config = [
      'module' => [
        'node',
        'schemadotorg',
        'user',
      ],
    ];
    $this->assertSame($expected_config, $view->getDependencies());

    // Check that the default 'Place' and 'Organization' filter returns 3 results.
    $view->initDisplay();
    $this->executeView($view);
    $this->assertCount(3, $view->result);

    $view->destroy();

    // Check that the Person filter returns 1 results.
    $view->initDisplay();
    $filters = $view->display_handler->getOption('filters');
    $filters['schemadotorg_type']['value'] = ['Person' => 'Person'];
    $view->display_handler->setOption('filters', $filters);
    $this->executeView($view);
    $this->assertCount(1, $view->result);

    $view->destroy();

    // Check that the Place filter returns 2 results.
    $view->initDisplay();
    $filters = $view->display_handler->getOption('filters');
    $filters['schemadotorg_type']['value'] = ['Place' => 'Place'];
    $view->display_handler->setOption('filters', $filters);
    $this->executeView($view);
    $this->assertCount(2, $view->result);

    $view->destroy();

    // Check that an empty filter returns 4 results.
    $view->initDisplay();
    $filters = $view->display_handler->getOption('filters');
    $filters['schemadotorg_type']['value'] = [];
    $view->display_handler->setOption('filters', $filters);
    $this->executeView($view);
    $this->assertCount(4, $view->result);
  }

  /**
   * Executes a view.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The view object.
   * @param array $args
   *   (optional) An array of the view arguments to use for the view.
   */
  protected function executeView(ViewExecutable $view, array $args = []): void {
    $view->setDisplay();
    $view->preExecute($args);
    $view->execute();
  }

}
