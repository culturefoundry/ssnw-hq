<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\file\Entity\File;
use Drupal\media\MediaTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeStorageInterface;
use Drupal\schemadotorg\Traits\SchemaDotOrgMappingStorageTrait;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Base class to testing entity type/bundle that are mapped to Schema.org types.
 *
 * @group schemadotorg
 */
abstract class SchemaDotOrgEntityKernelTestBase extends SchemaDotOrgKernelTestBase {
  use MediaTypeCreationTrait;
  use TestFileCreationTrait;
  use SchemaDotOrgMappingStorageTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'block',
    'block_content',
    'media',
    'paragraphs',
    'field_ui',
    'entity_reference_revisions',
    'file',
    'datetime',
    'image',
    'telephone',
    'link',
    'options',
    'schemadotorg_block_content',
    'schemadotorg_media',
    'schemadotorg_paragraphs',
  ];

  /**
   * Tracks lazily installed entity schemas.
   */
  protected array $installedEntitySchemas = [
    'node' => ['node'],
    'block_content' => ['block_content'],
    'file' => ['file'],
    'media' => ['media', 'image_style'],
    'paragraph' => ['paragraph'],
  ];

  /**
   * Tracks lazily installed entity config.
   */
  protected array $installedConfig = [
    'node' => ['node'],
    'block_content' => ['block_content', 'schemadotorg_block_content'],
    'media' => ['media', 'image', 'schemadotorg_media'],
    'paragraph' => ['paragraphs', 'schemadotorg_paragraphs'],
  ];

  /**
   * Tracks lazily installed entity schema.
   */
  protected array $installedSchemas = [
    'node' => [
      'module' => 'node',
      'schemas' => ['node_access'],
    ],
    'file' => [
      'module' => 'file',
      'schemas' => ['file_usage'],
    ],
  ];

  /**
   * The Schema.org mapping type storage.
   */
  protected SchemaDotOrgMappingTypeStorageInterface $mappingTypeStorage;

  /**
   * The Schema.org mapping storage.
   */
  protected SchemaDotOrgMappingStorageInterface $mappingStorage;

  /**
   * The Schema.org mapping manager.
   */
  protected SchemaDotOrgMappingManagerInterface $mappingManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchemaDotOrg();

    // Always install the user entity which is required by all entities.
    $this->installEntitySchema('user');

    // Set commonly user Schema.org mapping services.
    $this->mappingTypeStorage = $this->getMappingTypeStorage();
    $this->mappingStorage = $this->getMappingStorage();

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface $mapping_manager */
    $mapping_manager = $this->container->get('schemadotorg.mapping_manager');
    $this->mappingManager = $mapping_manager;
  }

  /**
   * Install entity dependencies.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   */
  protected function installEntityDependencies(string $entity_type_id): void {
    // Install the target entity type schema.
    if (isset($this->installedEntitySchemas[$entity_type_id])
      && $this->installedEntitySchemas[$entity_type_id] !== TRUE) {
      foreach ($this->installedEntitySchemas[$entity_type_id] as $entity_schema) {
        $this->installEntitySchema($entity_schema);
      }
      $this->installedEntitySchemas[$entity_type_id] = TRUE;
    }

    // Install the target entity type module config.
    if (isset($this->installedConfig[$entity_type_id])
      && $this->installedConfig[$entity_type_id] !== TRUE) {
      $modules = $this->installedConfig[$entity_type_id];
      $this->installConfig($modules);
      $this->installedConfig[$entity_type_id] = TRUE;
    }

    // Install the target entity type module schemas.
    if (isset($this->installedSchemas[$entity_type_id])
      && $this->installedSchemas[$entity_type_id] !== TRUE) {
      $schema = $this->installedSchemas[$entity_type_id];
      $this->installSchema($schema['module'], $schema['schemas']);
      $this->installedSchemas[$entity_type_id] = TRUE;
    }
  }

  /**
   * Create an entity type/bundle that is mapping to a Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $schema_type
   *   The Schema.org type.
   * @param array $defaults
   *   Mapping defaults for the entity and properties.
   *
   * @return \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null
   *   The entity type/bundle's Schema.org mapping.
   */
  protected function createSchemaEntity(string $entity_type_id, string $schema_type, array $defaults = []): ?SchemaDotOrgMappingInterface {
    // Install the entity type dependencies.
    $this->installEntityDependencies($entity_type_id);

    // Create the entity type and mappings.
    $this->mappingManager->createType($entity_type_id, $schema_type, $defaults);

    $bundle = $defaults['entity']['id'] ?? NULL;

    // Load the newly created Schema.org mapping by bundle or Schema.org type.
    return ($bundle)
      ? $this->mappingStorage->loadByBundle($entity_type_id, $bundle)
      : $this->mappingStorage->loadBySchemaType($entity_type_id, $schema_type);
  }

  /**
   * Create a test image file.
   *
   * @return \Drupal\file\Entity\File
   *   A test image file.
   */
  protected function createFileImage(): File {
    $this->installEntityDependencies('file');

    $file_uri = $this->getTestFiles('image')[0]->uri;
    $file_uri = str_replace('vfs://root', 'public://', $file_uri);
    $image = File::create([
      'uri' => $file_uri,
    ]);
    $image->setPermanent();
    $image->save();
    return $image;
  }

  /**
   * Create media image type.
   *
   * @return \Drupal\media\MediaTypeInterface
   *   The image media type.
   */
  protected function createMediaImage(): MediaTypeInterface {
    return $this->createMediaType('image', ['id' => 'image', 'label' => 'Image']);
  }

}
