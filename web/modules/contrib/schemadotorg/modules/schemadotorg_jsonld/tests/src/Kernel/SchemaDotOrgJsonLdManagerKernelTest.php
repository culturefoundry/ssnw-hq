<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Kernel;

use Drupal\Core\Language\LanguageInterface;
use Drupal\filter\Entity\FilterFormat;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;

/**
 * Tests the functionality of the Schema.org JSON-LD manager.
 *
 * @covers \Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManager;
 * @group schemadotorg
 */
class SchemaDotOrgJsonLdManagerKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * Test Schema.org JSON-LD manager.
   */
  public function testManager(): void {
    // Allow https://schema.org/Event to default to supporting
    // https://schema.org/eventSchedule property.
    $config = $this->config('schemadotorg.settings');
    $event_default_properties = $config->get('schema_types.default_properties.Event');
    $config
      ->set('schema_types.default_properties.Event', array_merge($event_default_properties, ['eventSchedule']))
      ->save();

    $this->createMediaImage();
    $this->createSchemaEntity('media', 'ImageObject');
    $this->createSchemaEntity('node', 'Place');
    $this->createSchemaEntity('node', 'Event');
    $this->createSchemaEntity('node', 'ContactPoint');

    // Filter format.
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

    // Place node.
    $place_node = Node::create([
      'type' => 'place',
      'title' => 'Somewhere',
      'langcode' => 'es',
      'body' => [
        'value' => 'Some description',
        'format' => 'empty_format',
      ],
      'schema_image' => [
        'target_id' => $media->id(),
      ],
      'schema_address' => '{Somewhere}',
      'schema_telephone' => [
        'value' => '123456789',
      ],
    ]);
    $place_node->save();

    // ContactPoint node.
    $contact_point_node = Node::create([
      'type' => 'contact_point',
      'title' => 'Some contact',
    ]);
    $contact_point_node->save();

    // Not mapping node.
    NodeType::create(['type' => 'page'])->save();
    $page_node = Node::create([
      'type' => 'page',
      'title' => 'Some page',
    ]);
    $page_node->save();

    /* ********************************************************************** */

    // Check getting an entity's canonical route match.
    $node_route_match = $this->manager->getEntityRouteMatch($place_node);
    $this->assertEquals('entity.node.canonical', $node_route_match->getRouteName());
    $this->assertEquals($place_node, $node_route_match->getParameter('node'));
    $this->assertEquals($place_node->id(), $node_route_match->getRawParameter('node'));

    // Check returning the entity of the current route.
    $route_entity = $this->manager->getRouteMatchEntity($node_route_match);
    $this->assertEquals($place_node, $route_entity);

    // Check sorting Schema.org properties in specified order and
    // then alphabetically.
    $sort_properties = $this->manager->sortProperties(['zzz' => 'zzz', 'aaa' => 'aaa', 'name' => 'name']);
    $sort_keys = array_keys($sort_properties);
    $this->assertEquals('name', $sort_keys[0]);
    $this->assertEquals('aaa', $sort_keys[1]);
    $this->assertEquals('zzz', $sort_keys[2]);

    // Language.
    $actual_value = $this->manager->getSchemaPropertyValue($place_node->langcode->get(0));
    $this->assertEquals('es', $actual_value);
    $place_node->langcode->value = LanguageInterface::LANGCODE_NOT_SPECIFIED;
    $actual_value = $this->manager->getSchemaPropertyValue($place_node->langcode->get(0));
    $this->assertNull($actual_value);

    // Body.
    $actual_value = $this->manager->getSchemaPropertyValue($place_node->body->get(0));
    $this->assertEquals('Some description', $actual_value);

    // Entity reference.
    $actual_value = $this->manager->getSchemaPropertyValue($place_node->schema_image->get(0));
    $this->assertEquals('Some image', $actual_value);

    // @todo Determine why we can't generate the media's image derivative.
    // Image.
    // $actual_value = $this->manager->getSchemaPropertyValue($media->field_media_image->get(0));
    // $this->assertEquals('Some image', $actual_value);
    // Created.
    $actual_value = $this->manager->getSchemaPropertyValue($place_node->created->get(0));
    $this->assertEquals(1, preg_match('/^\d\d\d\d-\d\d-\d\d/', $actual_value));

    // Check getting a Schema.org property's value converted to
    // the default Schema.org type.
    $this->assertEquals(
      ['@type' => 'Organization', 'name' => '{some_organization}'],
      $this->manager->getSchemaPropertyValueDefaultType('Organization', 'alumniOf', '{some_organization}')
    );
    $this->assertEquals(
      ['{some_answer}'],
      $this->manager->getSchemaPropertyValueDefaultType('Organization', 'acceptedAnswer', ['{some_answer}'])
    );
    $this->assertEquals(
      ['@type' => 'Answer', 'text' => 1],
      $this->manager->getSchemaPropertyValueDefaultType('Organization', 'acceptedAnswer', 1)
    );
    // Check that Schema.org Answer uses 'text' instead of 'name' as
    // the main property.
    $this->assertEquals(
      ['@type' => 'Answer', 'text' => '{some_answer}'],
      $this->manager->getSchemaPropertyValueDefaultType('Organization', 'acceptedAnswer', '{some_answer}')
    );
    // Check that default type property values are appended.
    $this->assertEquals(
      ['@type' => 'Offer', 'price' => 100.00, 'priceCurrency' => 'USD'],
      $this->manager->getSchemaPropertyValueDefaultType('MenuItem', 'offers', 100.00)
    );
    // Check that default type property values are appended.
    $this->assertEquals(
      ['@type' => 'Offer', 'price' => 100.00, 'priceCurrency' => 'USD'],
      $this->manager->getSchemaPropertyValueDefaultType('MenuItem', 'offers', ['@type' => 'Offer', 'price' => 100.00])
    );

    // Check getting how an entity reference should be included in JSON-LD.
    $this->assertEquals(
      SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_URL,
      $this->manager->getSchemaTypeEntityReferenceDisplay($place_node)
    );
    $this->assertEquals(
      SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_ENTITY,
      $this->manager->getSchemaTypeEntityReferenceDisplay($contact_point_node)
    );
    $this->assertEquals(
      SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_LABEL,
      $this->manager->getSchemaTypeEntityReferenceDisplay($page_node)
    );
    $this->config('schemadotorg_jsonld.settings')
      ->set('schema_type_entity_references_display.node--page', 'none')
      ->save();
    $this->assertEquals(
      SchemaDotOrgJsonLdManagerInterface::ENTITY_REFERENCE_DISPLAY_NONE,
      $this->manager->getSchemaTypeEntityReferenceDisplay($page_node)
    );

    // Check determining if the entity's Schema should include an @url.
    $this->assertTrue($this->manager->hasSchemaUrl($place_node));
    $this->assertFalse($this->manager->hasSchemaUrl($media));
  }

}
