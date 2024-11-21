<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_entity_reference_override\Kernel;

use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\field\Entity\FieldConfig;

/**
 * Tests the functionality of the Schema.org entity reference override.
 */
class SchemaDotOrgEntityReferenceOverrideKernelTest extends SchemaDotOrgEntityKernelTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the entity_reference_override.module has fixed its schema.
   *
   * Issue #3331271: Schema definition for the "override_format" setting is missing.
   *
   * @see https://www.drupal.org/project/entity_reference_override/issues/3331271
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'schemadotorg_entity_reference_override',
    'entity_reference_override',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_entity_reference_override']);
  }

  /**
   * Test Schema.org role entity reference override JSON-LD support.
   */
  public function testEntityReferenceOverride(): void {
    \Drupal::currentUser()->setAccount($this->createUser(['access content']));

    $this->config('schemadotorg_entity_reference_override.settings')
      ->set('schema_properties.LocalBusiness--member', [
        'override_format' => 'custom_format',
        'override_label' => 'Custom label',
      ])
      ->save();

    $this->appendSchemaTypeDefaultProperties('Organization', 'member');
    $this->createSchemaEntity('node', 'Person');
    $this->createSchemaEntity('node', 'Organization');
    $this->createSchemaEntity('node', 'LocalBusiness');

    /* ********************************************************************** */

    $field_config = FieldConfig::load('node.organization.schema_member');
    $this->assertEquals('Enter role', $field_config->getSetting('override_label'));
    $this->assertEquals(NULL, $field_config->getSetting('override_format'));
    $this->assertEquals('entity_reference_override', $field_config->getType());

    $field_config = FieldConfig::load('node.local_business.schema_member');
    $this->assertEquals('Custom label', $field_config->getSetting('override_label'));
    $this->assertEquals('custom_format', $field_config->getSetting('override_format'));
    $this->assertEquals('entity_reference_override', $field_config->getType());
  }

}
