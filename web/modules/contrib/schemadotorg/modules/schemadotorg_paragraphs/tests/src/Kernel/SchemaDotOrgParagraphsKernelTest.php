<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_paragraphs\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs_library\Entity\LibraryItem;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org paragraphs.
 *
 * @covers schemadotorg_paragraphs_schemadotorg_property_field_alter()
 * @covers schemadotorg_paragraphs_schemadotorg_mapping_presave()
 * @group schemadotorg
 */
class SchemaDotOrgParagraphsKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * Schema.org JSON-LD builder.
   */
  protected SchemaDotOrgJsonLdBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'file',
    'views',
    'paragraphs_library',
    'schemadotorg_jsonld',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('file', ['file_usage']);

    $this->installEntitySchema('file');
    $this->installEntitySchema('view');
    $this->installEntitySchema('paragraphs_library_item');
    $this->installConfig(['schemadotorg_paragraphs', 'schemadotorg_jsonld', 'paragraphs_library']);

    $this->builder = $this->container->get('schemadotorg_jsonld.builder');

    $this->appendSchemaTypeDefaultProperties('Person', 'contactPoint');
  }

  /**
   * Test Schema.org paragraphs.
   */
  public function testParagraphs(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->createSchemaEntity('paragraph', 'ContactPoint');
    $this->createSchemaEntity('node', 'Person');

    /* ********************************************************************** */

    // Check that ContactPoint field target bundles includes the
    // 'from_library' paragraph type.
    // @see schemadotorg_paragraphs_schemadotorg_property_field_alter()
    /** @var \Drupal\field\FieldConfigInterface $field */
    $field = FieldConfig::loadByName('node', 'person', 'schema_contact_point');
    $handler_settings = $field->getSetting('handler_settings');
    $this->assertEquals(['contact_point', 'from_library'], array_values($handler_settings['target_bundles']));

    // Check that ContactPoint paragraph type support library conversion.
    // @see schemadotorg_paragraphs_schemadotorg_mapping_presave()
    $paragraph_type = ParagraphsType::load('contact_point');
    $this->assertTrue($paragraph_type->getThirdPartySetting('paragraphs_library', 'allow_library_conversion'));

    // Create a Person with ContactPoint.
    $library_contact_point_item = LibraryItem::create([
      'paragraphs' => Paragraph::create([
        'type' => 'contact_point',
        'schema_contact_type' => ['value' => 'Contact Point from library'],
      ]),
    ]);
    $library_contact_point_item->save();
    $paragraph_contact_point = Paragraph::create([
      'type' => 'contact_point',
      'schema_contact_type' => ['value' => 'Contact Point'],
    ]);
    $paragraph_from_library = Paragraph::create([
      'type' => 'from_library',
      'field_reusable_paragraph' => [
        'target_id' => $library_contact_point_item->id(),
      ],
    ]);

    $person_node = Node::create([
      'type' => 'person',
      'title' => 'Person',
      'schema_contact_point' => [
        $paragraph_contact_point,
        $paragraph_from_library,
      ],
    ]);
    $person_node->save();

    // Check that the Person Schema.org JSON-LD includes
    // the ContactPoint paragraph and paragraph library item..
    $expected = [
      '@type' => 'Person',
      '@url' => $person_node->toUrl()->setAbsolute()->toString(),
      'name' => 'Person',
      'contactPoint' => [
        [
          '@type' => 'ContactPoint',
          'contactType' => 'Contact Point',
        ],
        [
          '@type' => 'ContactPoint',
          'contactType' => 'Contact Point from library',
        ],
      ],
    ];
    $result = $this->builder->buildEntity($person_node);
    $this->assertEquals($expected, $result);
  }

}
