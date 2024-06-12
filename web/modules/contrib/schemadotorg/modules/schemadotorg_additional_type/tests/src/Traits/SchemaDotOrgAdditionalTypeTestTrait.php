<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_additional_type\Traits;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Provides convenience methods for Schema.org additional type assertions.
 */
trait SchemaDotOrgAdditionalTypeTestTrait {

  /**
   * Create Schema.org additional type field.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   */
  protected function createSchemaDotOrgAdditionalTypeField(string $entity_type_id, string $schema_type): void {
    /** @var \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schema_names */
    $schema_names = $this->container->get('schemadotorg.names');
    $bundle = $schema_names->camelCaseToSnakeCase($schema_type);

    /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManager $schema_type_manager */
    $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');
    $allowed_values = $schema_type_manager->getAllTypeChildrenAsOptions($schema_type);

    FieldStorageConfig::create([
      'entity_type' => $entity_type_id,
      'field_name' => 'schema_' . $bundle . '_type',
      'type' => 'list_string',
      'allowed_values' => $allowed_values,
    ])->save();

    $field_config = FieldConfig::create([
      'entity_type' => $entity_type_id,
      'bundle' => $bundle,
      'field_name' => 'schema_' . $bundle . '_type',
    ]);
    $field_config->schemaDotOrgType = $schema_type;
    $field_config->schemaDotOrgProperty = 'additionalType';
    $field_config->save();
  }

}
