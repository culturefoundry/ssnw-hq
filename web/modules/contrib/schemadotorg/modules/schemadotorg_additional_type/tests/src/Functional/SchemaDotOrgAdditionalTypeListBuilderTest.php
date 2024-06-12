<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Functional;

use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\Tests\schemadotorg\Functional\SchemaDotOrgBrowserTestBase;
use Drupal\Tests\schemadotorg_additional_type\Traits\SchemaDotOrgAdditionalTypeTestTrait;

/**
 * Tests the functionality of the Schema.org additional type list builder enhancements.
 *
 * @covers \Drupal\schemadotorg_additional_type\EventSubscriber\SchemaDotOrgAdditionalTypeEventSubscriber
 * @group schemadotorg
 */
class SchemaDotOrgAdditionalTypeListBuilderTest extends SchemaDotOrgBrowserTestBase {
  use SchemaDotOrgAdditionalTypeTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['schemadotorg_additional_type'];

  /**
   * Test Schema.org list builder enhancements.
   */
  public function testSchemaDotOrgListBuilder(): void {
    $assert = $this->assertSession();

    // Create Thing content type with a Schema.org mapping.
    $this->drupalCreateContentType(['type' => 'thing']);
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = SchemaDotOrgMapping::create([
      'target_entity_type_id' => 'node',
      'target_bundle' => 'thing',
      'schema_type' => 'Thing',
    ]);
    $mapping->save();

    $account = $this->drupalCreateUser(['administer schemadotorg']);
    $this->drupalLogin($account);

    /* ********************************************************************** */

    $this->drupalGet('admin/config/schemadotorg/mappings');

    // Check additional type header.
    $assert->responseContains('<th class="priority-low" width="10%">Additional type</th>');

    // Check additional type cell is set to No.
    $assert->responseContains('<td class="priority-low">No</td>');
    $assert->responseNotContains('<td class="priority-low">Yes</td>');

    // Add additional type property mapping.
    $mapping
      ->setSchemaPropertyMapping('schema_thing_type', 'additionalType')
      ->save();

    $this->drupalGet('admin/config/schemadotorg/mappings');

    // Check additional type cell is set to Yes.
    $assert->responseNotContains('<td class="priority-low">No</td>');
    $assert->responseContains('<td class="priority-low">Yes</td>');
  }

}
