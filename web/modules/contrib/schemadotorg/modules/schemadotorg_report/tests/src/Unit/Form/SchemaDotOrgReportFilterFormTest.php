<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_report\Unit\Breadcrumb;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormState;
use Drupal\Core\Routing\RouteMatch;
use Drupal\schemadotorg_report\Form\SchemaDotOrgReportFilterForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Drupal\schemadotorg_report\Form\SchemaDotOrgReportFilterForm
 * @group schemadotorg
 */
class SchemaDotOrgReportFilterFormTest extends UnitTestCase {

  /**
   * The Schema.org report filter type or property form being tested.
   */
  protected SchemaDotOrgReportFilterForm $filterForm;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Build a new container.
    $container = new ContainerBuilder();

    // Mock string translation.
    $container->set('string_translation', $this->getStringTranslationStub());

    // Mock URL generator.
    $url_generator = $this->createMock('Drupal\Core\Routing\UrlGenerator');
    $container->set('url_generator', $url_generator);

    // Mock Schema.org type manager which is only used to validate
    // type and property ids.
    $schema_type_manager = $this->createMock('\Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager');
    $schema_type_manager->method('isId')->willReturnMap([
      ['types', 'Thing', TRUE],
      ['types', 'Th', FALSE],
      ['properties', 'name', TRUE],
      ['properties', 'na', FALSE],
    ]);
    $container->set('schemadotorg.schema_type_manager', $schema_type_manager);

    // Mock current route match is used to the reset the filter form.
    $request = new Request();
    $route_match = RouteMatch::createFromRequest($request);
    $container->set('current_route_match', $route_match);

    // Set the container.
    \Drupal::setContainer($container);

    // Create the filter form using the container.
    $this->filterForm = SchemaDotOrgReportFilterForm::create(\Drupal::getContainer());
  }

  /**
   * Tests SchemaDotOrgReportFilterForm::buildForm().
   *
   * @covers ::buildForm
   */
  public function testBuildForm(): void {
    // Build the filter form without a table of id.
    $form = [];
    $form_state = new FormState();
    $form = $this->filterForm->buildForm($form, $form_state);

    // Check that submit button is set and reset is not.
    $this->assertTrue(isset($form['filter']['submit']));
    $this->assertFalse(isset($form['filter']['reset']));

    // Build the filter form with the table and id.
    $form = [];
    $form_state = new FormState();
    $form = $this->filterForm->buildForm($form, $form_state, 'types', 'Thing');

    // Check that submit button and reset is set.
    $this->assertTrue(isset($form['filter']['submit']));
    $this->assertTrue(isset($form['filter']['reset']));
  }

  /**
   * Tests SchemaDotOrgReportFilterForm::submitForm().
   *
   * @covers ::submitForm
   */
  public function testSubmitForm(): void {
    // Build the filter by Schema.org types form.
    $form = [];
    $form_state = new FormState();
    $form = $this->filterForm->buildForm($form, $form_state, 'types');

    // Check that 'Thing' redirects to Schema.org type 'Thing' page.
    $form_state->setValue('id', 'Thing');
    $this->filterForm->submitForm($form, $form_state);
    /** @var \Drupal\Core\Url $redirect_url */
    $redirect_url = $form_state->getRedirect();
    $this->assertEquals('schemadotorg_report', $redirect_url->getRouteName());
    $this->assertEquals(['id' => 'Thing'], $redirect_url->getRouteParameters());
    $this->assertEquals([], $redirect_url->getOptions());

    // Check that 'Th' redirects to Schema.org types page filtered by 'Th'.
    $form_state->setValue('id', 'Th');
    $this->filterForm->submitForm($form, $form_state);
    /** @var \Drupal\Core\Url $redirect_url */
    $redirect_url = $form_state->getRedirect();
    $this->assertEquals('schemadotorg_report.types', $redirect_url->getRouteName());
    $this->assertEquals([], $redirect_url->getRouteParameters());
    $this->assertEquals(['query' => ['id' => 'Th']], $redirect_url->getOptions());
  }

  /**
   * Tests SchemaDotOrgReportFilterForm::resetForm().
   *
   * @covers ::resetForm
   */
  public function testResetForm(): void {
    // Trigger the reset form handler.
    $form = [];
    $form_state = new FormState();
    $this->filterForm->resetForm($form, $form_state);

    // Confirm the redirect returns the mocked current route which is NULL.
    $redirect_url = $form_state->getRedirect();
    $this->assertNull($redirect_url->getRouteName());
    $this->assertSame([], $redirect_url->getRouteParameters());
  }

}
