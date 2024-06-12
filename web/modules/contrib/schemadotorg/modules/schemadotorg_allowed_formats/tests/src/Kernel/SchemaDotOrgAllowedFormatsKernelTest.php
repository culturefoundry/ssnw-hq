<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_allowed_formats\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org allowed formats.
 *
 * @covers schemadotorg_allowed_formats_schemadotorg_property_field_alter()
 * @group schemadotorg
 */
class SchemaDotOrgAllowedFormatsKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'filter',
    'allowed_formats',
    'schemadotorg_allowed_formats',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg_allowed_formats']);

    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
    ])->save();

    FilterFormat::create([
      'format' => 'basic_html',
      'name' => 'Basic HTML',
    ])->save();

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
  }

  /**
   * Test Schema.org allowed formats.
   */
  public function testAllowedFormats(): void {
    $default_allowed_formats = $this->config('schemadotorg_allowed_formats.settings')
      ->get('default_allowed_formats');
    $default_allowed_formats = ['articleBody' => ['full_html', 'basic_html']] + $default_allowed_formats;
    $this->config('schemadotorg_allowed_formats.settings')
      ->set('default_allowed_formats', $default_allowed_formats)
      ->save();

    $this->createSchemaEntity('node', 'WebPage');
    $this->createSchemaEntity('node', 'Article');

    // Check settings default allowed formats.
    /** @var \Drupal\field\FieldConfigInterface $text_field */
    $text_field = FieldConfig::loadByName('node', 'page', 'body');
    $this->assertEquals(['full_html'], $text_field->getSetting('allowed_formats'));

    // Checking setting hide help and hide guidelines.
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'page');
    $form_component = $form_display->getComponent('body');
    $expected_values = [
      'allowed_formats' => [
        'hide_help' => '1',
        'hide_guidelines' => '1',
      ],
    ];
    $this->assertEquals($expected_values, $form_component['third_party_settings']);

    // Check settings property allowed formats.
    /** @var \Drupal\field\FieldConfigInterface $text_field */
    $text_field = FieldConfig::loadByName('node', 'article', 'body');
    $this->assertEquals(['full_html', 'basic_html'], $text_field->getSetting('allowed_formats'));

  }

}
