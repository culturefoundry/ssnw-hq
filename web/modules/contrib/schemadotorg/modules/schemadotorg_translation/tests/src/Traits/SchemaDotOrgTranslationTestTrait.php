<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_translation\Traits;

/**
 * Provides convenience methods for Schema.org assertions.
 */
trait SchemaDotOrgTranslationTestTrait {

  /**
   * Assert translated fields for given entity type and bundle.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param array $expected_fields
   *   The expected field names.
   */
  protected function assertTranslatedFields(string $entity_type_id, string $bundle, array $expected_fields): void {
    $this->assertEquals(
      $expected_fields,
      $this->getTranslatedFields($entity_type_id, $bundle)
    );
  }

  /**
   * Get the translated fields for a given entity type and bundle.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The bundle.
   *
   * @return array
   *   An array of translated fields.
   */
  protected function getTranslatedFields(string $entity_type_id, string $bundle): array {
    $fields = [];
    $field_definitions = $this->fieldManager->getFieldDefinitions($entity_type_id, $bundle);
    foreach ($field_definitions as $field_definition) {
      if ($field_definition->isTranslatable()) {
        $fields[$field_definition->getName()] = $field_definition->getName();
      }
    }
    return $fields;
  }

}
