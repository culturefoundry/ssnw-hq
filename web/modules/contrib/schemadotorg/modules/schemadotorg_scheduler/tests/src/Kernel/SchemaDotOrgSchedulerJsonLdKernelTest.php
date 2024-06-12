<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_scheduler\Kernel;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\node\Entity\Node;
use Drupal\Tests\schemadotorg_jsonld\Kernel\SchemaDotOrgJsonLdKernelTestBase;

/**
 * Tests the functionality of the Schema.org Scheduler module JSON-LD integration.
 *
 * @covers scheduler_schemadotorg_jsonld_schema_property_alter(()
 * @group schemadotorg
 */
class SchemaDotOrgSchedulerJsonLdKernelTest extends SchemaDotOrgJsonLdKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'scheduler',
    'schemadotorg_scheduler',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_scheduler']);
  }

  /**
   * Test Schema.org scheduler JSON-LD.
   */
  public function testJsonLdAddress(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    DateFormat::create([
      'id' => 'long',
      'label' => 'long',
      'pattern' => 'l, F j, Y - H:i',
    ])->save();

    $this->config('schemadotorg_scheduler.settings')
      ->set('scheduled_types.Article', ['publish', 'unpublish'])
      ->save();

    $this->createSchemaEntity('node', 'Article');

    $node = Node::create([
      'type' => 'article',
      'title' => 'Some article',
      'publish_on' => ['value' => strtotime('2020-01-01')],
      'unpublish_on' => ['value' => strtotime('2021-01-01')],
    ]);
    $node->save();

    $expected_value = [
      '@type' => 'Article',
      '@url' => $node->toUrl()->setAbsolute()->toString(),
      'inLanguage' => 'en',
      'headline' => 'Some article',
      'dateCreated' => $this->formatDateTime($node->getCreatedTime()),
      'dateModified' => $this->formatDateTime($node->getChangedTime()),
      'datePublished' => $this->formatDateTime(strtotime('2020-01-01')),
      'expires' => $this->formatDateTime(strtotime('2021-01-01')),
    ];
    $actual_value = $this->builder->buildEntity($node);
    $this->assertEquals($expected_value, $actual_value);
  }

}
