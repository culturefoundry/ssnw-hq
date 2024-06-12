<?php

/**
 * @file
 * Builds placeholder replacement tokens for Schema.org path data.
 */

declare(strict_types=1);

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info_alter().
 */
function schemadotorg_pathauto_token_info_alter(array &$info): void {
  /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface[] $mapping_types */
  $mapping_types = Drupal::entityTypeManager()
    ->getStorage('schemadotorg_mapping_type')
    ->loadMultiple();

  $entity_definitions = \Drupal::entityTypeManager()->getDefinitions();
  foreach ($mapping_types as $mapping_type) {
    $entity_type_id = $mapping_type->get('target_entity_type_id');
    $entity_info = $entity_definitions[$entity_type_id] ?? NULL;
    if (!$entity_info || !$entity_info->get('token_type')) {
      continue;
    }

    $token_type = $entity_info->get('token_type');
    $info['tokens'][$token_type]['schemadotorg']['base-path'] = [
      'name' => t('Schema.org type base path'),
      'description' => t('The Schema.org type base path of the @entity.', ['@entity' => mb_strtolower((string) $entity_info->getLabel())]),
    ];
  }
}

/**
 * Implements hook_tokens().
 */
function schemadotorg_pathauto_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): ?array {
  $mapping = _schemadotorg_tokens_get_mapping($type, $data);
  if (!$mapping) {
    return NULL;
  }

  /** @var \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schema_type_manager */
  $schema_type_manager = \Drupal::service('schemadotorg.schema_type_manager');

  $schema_type = $mapping->getSchemaType();
  /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
  $entity = $data[$type];

  // Get additionalType and replace the schema type, if it is valid subtype.
  // @see schemadotorg_additional_type.module.
  $additional_type = NULL;
  $additional_type_field_name = $mapping->getSchemaPropertyFieldName('additionalType');
  if ($additional_type_field_name && $entity->hasField($additional_type_field_name)) {
    $additional_type = $entity->{$additional_type_field_name}->value;
    if ($additional_type && $schema_type_manager->isType($additional_type)) {
      $schema_type = $additional_type;
    }
  }

  $replacements = [];
  foreach ($tokens as $name => $original) {
    switch ($name) {
      case 'schemadotorg:base-path':
        $paths = \Drupal::config('schemadotorg_pathauto.settings')->get('base_paths');
        foreach ($paths as $path_schema_type => $path) {
          // Allows custom subtypes to have base paths.
          if ($path_schema_type === $additional_type
            || $schema_type_manager->isSubTypeOf($schema_type, $path_schema_type)) {
            $replacements[$original] = \Drupal::token()->replace($path, $data, $options, $bubbleable_metadata);
            break;
          }
        }
        break;
    }
  }

  return $replacements;
}
