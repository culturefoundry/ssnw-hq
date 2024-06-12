<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Kernel;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\filter\Entity\FilterFormat;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

/**
 * Tests the functionality of the Schema.org JSON-LD builder.
 *
 * @covers \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilder;
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdBuilderKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['token'];

  /**
   * Test Schema.org JSON-LD builder.
   */
  public function testBuilder(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $now = time();

    // Create media image.
    $this->createSchemaEntity('media', 'ImageObject');

    // Create thing with sameAs.
    $this->appendSchemaTypeDefaultProperties('Thing', ['name', 'sameAs']);
    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.Thing--sameAs.type', 'field_ui:entity_reference:node')
      ->save();
    $this->createSchemaEntity('node', 'Thing');

    // Create a creative work.
    $this->appendSchemaTypeDefaultProperties('CreativeWork', ['subjectOf', 'alternateName', 'image']);
    $this->createSchemaEntity('node', 'CreativeWork');

    DateFormat::create([
      'id' => 'fallback',
      'label' => 'Fallback',
      'pattern' => 'Y-m-d',
    ])->save();

    FilterFormat::create([
      'format' => 'empty_format',
      'name' => 'Empty format',
    ])->save();

    // Image file.
    $file = $this->createFileImage();

    // Media.
    $media = Media::create([
      'bundle' => 'image',
      'name' => 'Some image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => 'default alt',
        'title' => 'default title',
      ],
    ]);
    $media->save();

    // Node.
    $creative_work_node = Node::create([
      'type' => 'creative_work',
      'title' => 'Something',
      'schema_image' => [
        'target_id' => $media->id(),
      ],
      'schema_alternate_name' => [
        'value' => 'Something else',
      ],
      'schema_subject_of' => [
        ['value' => 'Some subject'],
      ],
      'body' => [
        'summary' => 'A summary',
        'value' => 'Some description',
        'format' => 'empty_format',
      ],
      'created' => $now,
      'changed' => $now,
    ]);
    $creative_work_node->save();

    // Check building JSON-LD for an entity that is mapped to a Schema.org type.
    $expected_result = [
      '@type' => 'CreativeWork',
      '@url' => $creative_work_node->toUrl()->setAbsolute()->toString(),
      'name' => 'Something',
      'alternateName' => [
        'Something else',
      ],
      'description' => 'A summary',
      'text' => 'Some description',
      'image' => \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri()),
      'subjectOf' => [
        [
          '@type' => 'CreativeWork',
          'name' => 'Some subject',
        ],
      ],
      'inLanguage' => 'en',
      'dateCreated' => $this->formatDateTime($now),
      'dateModified' => $this->formatDateTime($now),
    ];
    $this->assertEquals($expected_result, $this->builder->buildEntity($creative_work_node));

    /* ********************************************************************* */

    // Set relatedLink to use an entity reference field instead of a link field.
    $this->config('schemadotorg.settings')
      ->set('schema_properties.default_fields.relatedLink.type', 'field_ui:entity_reference:node')
      ->save();
    $this->createSchemaEntity('node', 'WebPage');

    $node_1 = Node::create([
      'type' => 'page',
      'title' => 'Node 1',
    ]);
    $node_1->save();

    $node_2 = Node::create([
      'type' => 'page',
      'title' => 'Node 2',
      'schema_related_link' => ['target_id' => $node_1->id()],
    ]);
    $node_2->save();

    // Check that for Schema.org properties that can only contain a URL,
    // we return the entity's absolute URL.
    $jsonld = $this->builder->buildEntity($node_2);
    $this->assertEquals([$node_1->toUrl()->setAbsolute()->toString()], $jsonld['relatedLink']);
  }

}
