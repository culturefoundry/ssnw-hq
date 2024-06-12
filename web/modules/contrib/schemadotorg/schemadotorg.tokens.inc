<?php

/**
 * @file
 * Builds placeholder replacement tokens for Schema.org mapping data.
 */

declare(strict_types=1);

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Implements hook_token_info().
 */
function schemadotorg_token_info(): array {
  $info = [];

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
    $info['tokens'][$token_type]['schemadotorg'] = [
      'name' => t('Schema.org type'),
      'description' => t('The Schema.org type of the @entity.', ['@entity' => mb_strtolower((string) $entity_info->getLabel())]),
      'module' => 'schemadotorg',
    ];
  }

  return $info;
}

/**
 * Implements hook_tokens().
 */
function schemadotorg_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): ?array {
  $mapping = _schemadotorg_tokens_get_mapping($type, $data);
  if (!$mapping) {
    return NULL;
  }

  $replacements = [];
  foreach ($tokens as $name => $original) {
    switch ($name) {
      case 'schemadotorg':
        $replacements[$original] = $mapping->getSchemaType();
        break;
    }
  }

  return $replacements;
}

/**
 * Get a token replacements Schema.org mapping.
 *
 * @param string $type
 *   The machine-readable name of the type (group) of token being replaced, such
 *   as 'node', 'user', or another type defined by a hook_token_info()
 *   implementation.
 * @param array $data
 *   An associative array of data objects to be used when generating replacement
 *   values, as supplied in the $data parameter to
 *   \Drupal\Core\Utility\Token::replace().
 *
 * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null
 *   A Schema.org mapping.
 */
function _schemadotorg_tokens_get_mapping(string $type, array $data): ?SchemaDotOrgMappingInterface {
  // Check that type is associated with a content entity.
  $entity = $data[$type] ?? NULL;
  if (!$entity instanceof ContentEntityInterface) {
    return NULL;
  }

  // Check that the content entity is mapped to a Schema.org type.
  /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
  $mapping_storage = \Drupal::entityTypeManager()
    ->getStorage('schemadotorg_mapping');
  return $mapping_storage->loadByEntity($entity);
}
