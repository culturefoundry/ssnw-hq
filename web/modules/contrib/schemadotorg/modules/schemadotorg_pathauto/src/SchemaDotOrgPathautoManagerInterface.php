<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_pathauto;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;

/**
 * Schema.org pathauto manager interface.
 */
interface SchemaDotOrgPathautoManagerInterface {

  /**
   * Create an initial pathauto pattern when a mapping is inserted.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void;

  /**
   * Alter the metadata about available placeholder tokens and token types.
   *
   * @param array $info
   *   The associative array of token definitions from hook_token_info().
   */
  public function tokenInfoAlter(array &$info): void;

  /**
   * Provide replacement values for placeholder tokens.
   *
   * @param string $type
   *   The machine-readable name of the type (group) of token being replaced, such
   *   as 'node', 'user', or another type defined by a hook_token_info()
   *   implementation.
   * @param array $tokens
   *   An array of tokens to be replaced. The keys are the machine-readable token
   *   names, and the values are the raw [type:token] strings that appeared in the
   *   original text.
   * @param array $data
   *   An associative array of data objects to be used when generating replacement
   *   values, as supplied in the $data parameter to
   *   \Drupal\Core\Utility\Token::replace().
   * @param array $options
   *   An associative array of options for token replacement; see
   *   \Drupal\Core\Utility\Token::replace() for possible values.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   The bubbleable metadata.
   *
   * @return ?array
   *   An associative array of replacement values, keyed by the raw [type:token]
   *   strings from the original text.
   */
  public function tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): ?array;

}
