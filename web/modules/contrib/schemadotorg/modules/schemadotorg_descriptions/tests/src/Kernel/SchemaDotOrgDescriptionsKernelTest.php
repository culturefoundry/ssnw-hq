<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_descriptions\Kernel;

use Drupal\node\Entity\NodeType;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org descriptions.
 *
 * @group schemadotorg
 */
class SchemaDotOrgDescriptionsKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_descriptions',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(self::$modules);

    $this->config('schemadotorg_descriptions.settings')
      ->set('custom_descriptions.page--body', 'This is a custom description.')
      ->save();

  }

  /**
   * Test Schema.org descriptions.
   */
  public function testDescriptions(): void {
    /* ********************************************************************** */
    // Entity: Page.
    /* ********************************************************************** */

    // Create an Event with the default description.
    $this->createSchemaEntity('node', 'WebPage');

    // Check the body field config has a custom description.
    /** @var \Drupal\field\FieldConfigInterface $field_config */
    $field_config = $this->entityTypeManager->getStorage('field_config')
      ->load('node.page.body');
    $this->assertEquals('This is a custom description.', $field_config->getDescription());

    /* ********************************************************************** */
    // Schema.org type: Event.
    /* ********************************************************************** */

    // Create an Event with the default description.
    $this->createSchemaEntity('node', 'Event');

    // Check the node type description is empty when stored via configuration.
    $this->assertEmpty(\Drupal::configFactory()->getEditable('node.type.event')->get('description'));

    // Check the node type description is populate with the Schema.org comment.
    /** @var \Drupal\node\NodeTypeInterface $node_type */
    $node_type = NodeType::load('event');
    $this->assertEquals('An event happening at a certain time and location, such as a concert, lecture, or festival.', $node_type->getDescription());

    /* ********************************************************************** */
    // Schema.org type: FAQPage.
    /* ********************************************************************** */

    // Create an FAQPage with a custom description.
    $this->createSchemaEntity('node', 'FAQPage');

    // Check the node type description is empty when stored via configuration.
    $this->assertEmpty(\Drupal::configFactory()->getEditable('node.type.faq')->get('description'));

    // Check the node type description is populate with the Schema.org comment.
    /** @var \Drupal\node\NodeTypeInterface $node_type */
    $node_type = NodeType::load('faq');
    $this->assertEquals('A page presenting one or more "Frequently asked questions".', $node_type->getDescription());
  }

}
