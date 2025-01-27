<?php

namespace Drupal\Tests\layout_section_classes\Functional;

use Drupal\Core\Form\FormState;
use Drupal\layout_builder\LayoutEntityHelperTrait;
use Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage;
use Drupal\layout_builder\Section;
use Drupal\layout_section_classes_test\NewTestClassyLayout;
use Drupal\Tests\layout_builder\Kernel\LayoutBuilderCompatibilityTestBase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Test layout section classes.
 *
 * @group layout_section_classes
 */
class LayoutSectionClassesTest extends LayoutBuilderCompatibilityTestBase {

  use LayoutEntityHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'layout_builder',
    'layout_section_classes',
    'layout_section_classes_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installLayoutBuilder();
    $this->enableOverrides();
  }

  /**
   * Test the layout section classes.
   */
  public function testLayoutSectionClasses() {
    $sections = $this->entity->get(OverridesSectionStorage::FIELD_NAME);
    $sections->appendSection(new Section('test_layout', [
      'additional' => [
        'classes' => [
          'style' => 'background--wave-dark background--primary-light',
        ],
      ],
    ]));
    $this->entity->save();

    $rendered = $this->renderEntity();
    $this->assertStringContainsString('background--wave-dark background--primary-light', $rendered);
    $crawler = new Crawler($rendered);
    // Assert region classes work as expected.
    $this->assertCount(1, $crawler->filter('.some-region-classes.a-region-class'));
    // Assert attributes work as expected.
    $this->assertCount(1, $crawler->filter('.background--wave-dark.background--primary-light[data-some-attribute="foo"]'));

    // Check if the default value is an array.
    $plugin = \Drupal::service('plugin.manager.core.layout')->createInstance('test_layout');
    $form = $plugin->buildConfigurationForm([], new FormState(), $sections->get(0));
    $this->assertNotEmpty($form);
    $this->assertIsArray($form['classes']['style']['#default_value']);
  }

  /**
   * Test layouts can override the class key in layout definitions.
   */
  public function testLayoutSectionCustomClass() {
    /** @var \Drupal\Core\Layout\LayoutPluginManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.core.layout');
    $definition = $plugin_manager->getDefinition('custom_class_layout');
    $this->assertEquals(NewTestClassyLayout::class, $definition->getClass());
  }

  /**
   * Render the test entity.
   *
   * @return string
   *   The test entity.
   */
  protected function renderEntity() {
    $view_builder = $this->container->get('entity_type.manager')->getViewBuilder($this->entity->getEntityTypeId());
    $build = $view_builder->view($this->entity);
    return $this->render($build);
  }

}
