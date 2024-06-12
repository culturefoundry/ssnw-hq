<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Drush\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgConfigManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgInstallerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;

/**
 * Schema.org Drush commands.
 */
class SchemaDotOrgCommands extends DrushCommands {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Constructs a SchemaDotOrgCommands object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgInstallerInterface $schemaInstaller
   *   The Schema.org installer service.
   * @param \Drupal\schemadotorg\SchemaDotOrgConfigManagerInterface $schemaConfigManager
   *   The Schema.org schema config manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $schemaMappingManager
   *   The Schema.org mapping manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgInstallerInterface $schemaInstaller,
    protected SchemaDotOrgConfigManagerInterface $schemaConfigManager,
    protected SchemaDotOrgMappingManagerInterface $schemaMappingManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('schemadotorg.installer'),
      $container->get('schemadotorg.config_manager'),
      $container->get('schemadotorg.mapping_manager'),
    );
  }

  /**
   * Download Schema.org CSV data.
   *
   * IMPORTANT: This command is used by maintainers to download the latest
   * CSV data from Schema.org.
   *
   * @command schemadotorg:download-schema
   *
   * @usage schemadotorg:download-schema
   */
  public function download(): void {
    if (!$this->io()->confirm(dt('Are you sure you want to download Schema.org CSV data?'))) {
      throw new UserAbortException();
    }

    $this->schemaInstaller->downloadCsvData();

    $this->output()->writeln(dt('Downloaded Schema.org CSV data.'));
  }

  /**
   * Extract translatable strings Schema.org CSV data.
   *
   * IMPORTANT: This command is used by maintainers to extract translatable
   * strings from the latest CSV data from Schema.org.
   *
   * @command schemadotorg:translate-schema
   *
   * @usage schemadotorg:translate-schema
   */
  public function translate(): void {
    if (!$this->io()->confirm(dt('Are you sure you want to extract translatable strings Schema.org CSV data.?'))) {
      throw new UserAbortException();
    }

    $this->schemaInstaller->translateCsvData();

    $this->output()->writeln(dt('Extracted translatable strings Schema.org CSV data.'));
  }

  /**
   * Update Schema.org data.
   *
   * @command schemadotorg:update-schema
   *
   * @usage schemadotorg:update-schema
   *
   * @aliases soup
   */
  public function update(): void {
    if (!$this->io()->confirm(dt('Are you sure you want to update Schema.org data?'))) {
      throw new UserAbortException();
    }

    $this->schemaInstaller->install();

    $this->output()->writeln(dt('Updated Schema.org data.'));
  }

  /* ************************************************************************ */
  // Create type.
  /* ************************************************************************ */

  /**
   * Validates the entity type and Schema.org type to be created.
   *
   * @hook validate schemadotorg:create-type
   */
  public function createTypeValidate(CommandData $commandData): void {
    $arguments = $commandData->getArgsWithoutAppName();
    $types = $arguments['types'] ?? [];
    if (empty($types)) {
      throw new \Exception(dt('Schema.org types are required.'));
    }

    foreach ($types as $type) {
      // Validate mapping type.
      if (!str_contains($type, ':')) {
        $t_args = ['@type' => $type];
        $message = dt("The Schema.org mapping type '@type' is not valid. A Schema.org type must be defined with an entity type and Schema.org type delimited using a colon (:).", $t_args);
        throw new \Exception($message);
      }

      [$entity_type_id, $schema_type] = explode(':', $type);
      $this->schemaMappingManager->createTypeValidate($entity_type_id, $schema_type);
    }
  }

  /**
   * Create Schema.org types.
   *
   * @param array $types
   *   A list of Schema.org mapping types.
   *
   * @command schemadotorg:create-type
   *
   * @usage drush schemadotorg:create-type paragraph:ContactPoint paragraph:PostalAddress
   * @usage drush schemadotorg:create-type media:AudioObject media:DataDownload media:ImageObject media:VideoObject
   * @usage drush schemadotorg:create-type user:Person
   * @usage drush schemadotorg:create-type node:Person node:Organization node:Place node:Event node:CreativeWork
   * @usage drush schemadotorg:create-type node:Place
   * @usage drush schemadotorg:create-type node:Organization
   *
   * @aliases socr
   */
  public function createType(array $types): void {
    $t_args = ['@types' => implode(', ', $types)];
    if (!$this->io()->confirm(dt('Are you sure you want to create these types (@types)?', $t_args))) {
      throw new UserAbortException();
    }

    $types = array_combine($types, $types);
    foreach ($types as $type) {
      [$entity_type, $schema_type] = explode(':', $type);
      $existing_mapping = $this->getMappingStorage()->loadByProperties([
        'target_entity_type_id' => $entity_type,
        'schema_type' => $schema_type,
      ]);
      if ($existing_mapping) {
        $t_args = ['@type' => $type];
        $this->io()->writeln(dt("Schema.org type '@type' already exists.", $t_args));
        unset($types[$type]);
      }
      else {
        $this->schemaMappingManager->createType($entity_type, $schema_type);
      }
    }

    if ($types) {
      $t_args = ['@types' => implode(', ', $types)];
      $this->io()->writeln(dt('Schema.org types (@types) created.', $t_args));
    }
  }

  /* ************************************************************************ */
  // Delete type.
  /* ************************************************************************ */

  /**
   * Validates the entity type and Schema.org type to be deleted.
   *
   * @hook validate schemadotorg:delete-type
   */
  public function deleteTypeValidate(CommandData $commandData): void {
    $arguments = $commandData->getArgsWithoutAppName();
    $types = $arguments['types'] ?? [];

    // Require Schema.org types.
    if (empty($types)) {
      throw new \Exception(dt('Schema.org types are required'));
    }

    foreach ($types as $type) {
      [$entity_type, $schema_type] = explode(':', $type);
      $this->schemaMappingManager->deleteTypeValidate($entity_type, $schema_type);
    }
  }

  /**
   * Delete Schema.org type.
   *
   * @param array $types
   *   A list of Schema.org mapping types.
   * @param array $options
   *   (optional) An array of options.
   *
   * @command schemadotorg:delete-type
   *
   * @usage drush schemadotorg:delete-type --delete-fields user:Person
   * @usage drush schemadotorg:delete-type --delete-fields media:AudioObject media:DataDownload media:ImageObject media:VideoObject
   * @usage drush schemadotorg:delete-type --delete-entity paragraph:ContactPoint paragraph:PostalAddress
   * @usage drush schemadotorg:delete-type --delete-entity node:Person node:Organization node:Place node:Event node:CreativeWork
   *
   * @option delete-entity Delete the entity associated with the Schema.org type.
   * @option delimiter Delete the fields associated with the Schema.org type.
   *
   * @aliases sode
   */
  public function deleteType(array $types, array $options = ['delete-entity' => FALSE, 'delete-fields' => FALSE]): void {
    $t_args = ['@types' => implode(', ', $types)];
    if (!$this->io()->confirm(dt('Are you sure you want to delete these Schema.org types (@types) and their associated entities and fields?', $t_args))) {
      throw new UserAbortException();
    }

    foreach ($types as $type) {
      [$entity_type, $schema_type] = explode(':', $type);
      $this->schemaMappingManager->deleteType($entity_type, $schema_type, $options);
    }
    $this->io()->writeln(dt('Schema.org types (@types) deleted.', $t_args));
  }

}
