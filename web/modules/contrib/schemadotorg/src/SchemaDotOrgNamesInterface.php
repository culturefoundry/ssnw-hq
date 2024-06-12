<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

/**
 * Schema.org names interface.
 */
interface SchemaDotOrgNamesInterface {

  /**
   * Default prefix for Schema.org table and field names.
   */
  const DEFAULT_PREFIX = 'schema_';

  /**
   * Gets the field prefix for Schema.org properties.
   *
   * @return string
   *   The field suffix for Schema.org properties.
   */
  public function getFieldPrefix(): string;

  /**
   * Gets the max length for Schema.org type or property.
   *
   * Drupal limits type and field names to 32 characters.
   * Schema.org fields are prefixed with 'schema_' which limits
   * the name to 25 characters.
   *
   * @param string $table
   *   Schema.org type or property table name.
   *
   * @return int
   *   The max length for Schema.org type (32 characters)
   *   or property (32 characters - {field_prefix}).
   */
  public function getNameMaxLength(string $table): int;

  /**
   * Convert snake case (snake_case) to upper camel case (CamelCase).
   *
   * @param string $string
   *   The snake case string.
   *
   * @return string
   *   The snake case (snake_case) to upper camel case (CamelCase).
   */
  public function snakeCaseToUpperCamelCase(string $string): string;

  /**
   * Convert snake case (snake_case) to camel case (CamelCase).
   *
   * @param string $string
   *   The snake case string.
   *
   * @return string
   *   The snake case (snake_case) to camel case (CamelCase).
   */
  public function snakeCaseToCamelCase(string $string): string;

  /**
   * Convert camel case (camelCase) to snake case (snake_case).
   *
   * @param string $string
   *   The camel case string.
   *
   * @return string
   *   The camel case string converted to snake case.
   */
  public function camelCaseToSnakeCase(string $string): string;

  /**
   * Convert camel case (camelCase) to title case (Title Case).
   *
   * @param string $string
   *   The camel case string.
   *
   * @return string
   *   The camel case string converted to title case.
   */
  public function camelCaseToTitleCase(string $string): string;

  /**
   * Convert camel case (camelCase) to sentence case (Sentence ase).
   *
   * @param string $string
   *   Thecamel case string.
   *
   * @return string
   *   The camel case string converted to sentence case.
   */
  public function camelCaseToSentenceCase(string $string): string;

  /**
   * Convert camel case to a Drupal machine name.
   *
   * @param string $string
   *   The Schema.org type or property.
   * @param array $options
   *   An optional array of options including maxlength and truncate.
   *
   * @return string
   *   Camel case converted to a Drupal machine name.
   */
  public function camelCaseToDrupalName(string $string, array $options = []): string;

  /**
   * Convert Schema.org type or property ID to a Drupal label.
   *
   * @param string $table
   *   The Schema.org table.
   * @param string $string
   *   The Schema.org type or property.
   *
   * @return string
   *   Schema.org type or property ID converted to a Drupal label.
   */
  public function schemaIdToDrupalLabel(string $table, string $string): string;

  /**
   * Convert Schema.org type or property ID to a Drupal machine name.
   *
   * @param string $table
   *   The Schema.org table.
   * @param string $string
   *   The Schema.org type or property.
   *
   * @return string
   *   The Schema.org type or property ID converted to a Drupal machine name.
   */
  public function schemaIdToDrupalName(string $table, string $string): string;

}
