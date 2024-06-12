<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_metatag\Kernel;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\metatag\Entity\MetatagDefaults;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org metatag.
 *
 * @covers schemadotorg_metatag_schemadotorg_mapping_presave()
 * @covers schemadotorg_metatag_schemadotorg_mapping_insert()
 * @group schemadotorg
 */
class SchemaDotOrgMetatagKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'token',
    'metatag',
    'metatag_open_graph',
    'metatag_facebook',
    'metatag_twitter_cards',
    'schemadotorg_metatag',
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

    $this->installConfig(['metatag', 'schemadotorg_metatag']);
    $this->entityDisplayRepository = $this->container->get('entity_display.repository');

    DateFormat::create([
      'id' => 'fallback',
      'label' => 'Fallback',
      'pattern' => 'Y-m-d',
    ])->save();
  }

  /**
   * Test Schema.org metatag.
   */
  public function testMetatag(): void {
    // Set image_src to an invalid token, which prevents it from being created.
    \Drupal::configFactory()->getEditable('schemadotorg_metatag.settings')
      ->set('default_tags.node.image.image_src', '[invalid:title]')
      ->save();

    // Create node:place.
    $this->createSchemaEntity('media', 'ImageObject');
    $this->createSchemaEntity('node', 'Place');

    /* ********************************************************************** */
    // Default tags.
    // @see schemadotorg_metatag_schemadotorg_mapping_presave()
    /* ********************************************************************** */

    // Check that node meta tag defaults are set when a mapping is saved.
    /** @var \Drupal\metatag\MetatagDefaultsInterface $node_metatag_defaults */
    $node_metatag_defaults = MetatagDefaults::load('node');
    $expected_tags = [
      'title' => '[node:title] | [site:name]',
      'description' => '[node:summary]',
      'canonical_url' => '[node:url]',
      'og_description' => '[node:summary]',
      'twitter_cards_description' => '[node:summary]',
      'og_image' => '[node:schema_image:entity:field_media_image:medium]',
      'og_image_secure_url' => '[node:schema_image:entity:field_media_image:medium]',
      'og_image_alt' => '[node:schema_image:entity:field_media_image:alt]',
      'og_image_height' => '[node:schema_image:entity:field_media_image:medium:height]',
      'og_image_type' => '[node:schema_image:entity:field_media_image:medium:mimetype]',
      'og_image_width' => '[node:schema_image:entity:field_media_image:medium:width]',
      'twitter_cards_image' => '[node:schema_image:entity:field_media_image:medium]',
      'twitter_cards_image_alt' => '[node:schema_image:entity:field_media_image:alt]',
      'og_title' => '[node:title]',
      'twitter_cards_title' => '[node:title]',
      'og_phone_number' => '[node:schema_telephone]',
      'og_url' => '[node:url]',
    ];
    $this->assertEquals($expected_tags, $node_metatag_defaults->get('tags'));

    // Check that node--place meta tag defaults are set when a mapping is saved.
    $node_place_metatag_defaults = MetatagDefaults::load('node--place');
    $this->assertEquals('Content: Place', $node_place_metatag_defaults->label());
    $expected_tags = [
      'og_street_address' => '[node:schema_address]',
    ];
    $this->assertEquals($expected_tags, $node_place_metatag_defaults->get('tags'));

    /* ********************************************************************** */
    // Meta tag field.
    // @see schemadotorg_metatag_schemadotorg_mapping_insert()
    /* ********************************************************************** */

    // Check creating meta tag field storage.
    $this->assertNotNull(FieldStorageConfig::loadByName('node', 'field_metatag'));

    // Check creating meta tag field instance.
    $this->assertNotNull(FieldConfig::loadByName('node', 'place', 'field_metatag'));

    // Check setting meta tag component in the default form display.
    $expected_component = [
      'type' => 'metatag_firehose',
      'weight' => 99,
      'region' => 'content',
      'settings' => [
        'sidebar' => TRUE,
        'use_details' => TRUE,
      ],
      'third_party_settings' => [],
    ];
    $form_display = $this->entityDisplayRepository->getFormDisplay('node', 'place', 'default');
    $this->assertEquals($expected_component, $form_display->getComponent('field_metatag'));
  }

}
