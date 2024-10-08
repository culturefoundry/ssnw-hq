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
  /** @var \Drupal\schemadotorg_pathauto\SchemaDotOrgPathautoManagerInterface $schema_pathauto_manager */
  $schema_pathauto_manager = \Drupal::service('schemadotorg_pathauto.manager');
  $schema_pathauto_manager->tokenInfoAlter($info);
}

/**
 * Implements hook_tokens().
 */
function schemadotorg_pathauto_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): ?array {
  /** @var \Drupal\schemadotorg_pathauto\SchemaDotOrgPathautoManagerInterface $schema_pathauto_manager */
  $schema_pathauto_manager = \Drupal::service('schemadotorg_pathauto.manager');
  return $schema_pathauto_manager->tokens($type, $tokens, $data, $options, $bubbleable_metadata);
}
