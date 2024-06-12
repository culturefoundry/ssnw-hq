<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Functional;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\File\FileSystemInterface;

/**
 * Base tests for Schema.org Blueprints config snapshot.
 *
 * For working instance of this base test see SchemaDotOrgConfigSnapshotTest.
 *
 * To create a config snapshot (../../schemadotorg/config/snapshot).
 *
 * - Create a config snapshot test by copying and adjusting
 *   SchemaDotOrgConfigSnapshotTest.php.
 * - Run the test to creates the initial snapshot.
 *   This test will fail because snapshot files are being generated
 * - Re-run the test and confirm that config snapshot passes as expected.
 * - Commit the test and the config snapshot.
 *
 * @see \Drupal\Tests\schemadotorg\Functional\SchemaDotOrgConfigSnapshotTest
 */
abstract class SchemaDotOrgConfigSnapshotTestBase extends SchemaDotOrgBrowserTestBase {

  // phpcs:disable
  /**
   * Disable config schema checking.
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * The Schema.org Blueprints config snapshot directory.
   */
  protected string $snapshotDirectory;

  /**
   * Configuration file prefixes to create and test snapshots.
   *
   * The below list of file prefix targets any configuration generated
   * by the core Schema.org Blueprints module.
   */
  protected array $configPrefixes = [
    'block_content.type.',
    'core.entity_form_display.',
    'core.entity_form_mode.',
    'core.entity_view_display.',
    'core.entity_view_mode.',
    'core.base_field_override.',
    'field.field.',
    'field.storage.',
    'node.type',
    'media.type',
    'paragraphs.paragraphs_type.',
    'schemadotorg.schemadotorg_mapping.',
    'taxonomy.vocabulary.',
  ];

  /**
   * Schema.org entity types that should be setup.
   *
   * Use `entity_type:SchemaType`, for example
   * `node:Article`' will set up an article content type.
   */
  protected array $entityTypes = [];

  /**
   * The file system service.
   */
  protected FileSystemInterface $fileSystem;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->fileSystem = \Drupal::service('file_system');

    // Make sure that the snapshot directory is defined.
    if (!isset($this->snapshotDirectory)) {
      throw new \Exception('Snapshot directory is required.');
    }

    // If the snapshot does not exist, create it.
    if (!file_exists($this->snapshotDirectory)) {
      $this->fileSystem->mkdir($this->snapshotDirectory, 0777, TRUE);
    }

    // Create entity types.
    foreach ($this->entityTypes as $entity_type) {
      [$entity_type_id, $schema_type] = explode(':', $entity_type);
      $this->createSchemaEntity($entity_type_id, $schema_type);
    }
  }

  /**
   * Test Schema.org Blueprints config snapshot.
   */
  public function testConfigSnapshot(): void {
    // Get current config snapshot filenames.
    $expected_files = $this->fileSystem->scanDirectory($this->snapshotDirectory, '/\.yml$/', ['key' => 'filename']);
    $expected_filenames = array_keys($expected_files);
    $expected_filenames = array_combine($expected_filenames, $expected_filenames);
    ksort($expected_filenames);

    // Track the actual config snapshot filenames.
    $actual_filenames = [];
    foreach ($this->configPrefixes as $config_prefix) {
      $config_names = \Drupal::configFactory()->listAll($config_prefix);
      foreach ($config_names as $config_name) {
        $config_file_name = $config_name . '.yml';
        $config_file_path = $this->snapshotDirectory . '/' . $config_file_name;

        // Get config raw data without uuid and _core.
        $config_data = \Drupal::config($config_name)->getRawData();
        unset(
          $config_data['uuid'],
          $config_data['icon_uuid'],
          $config_data['_core']
        );

        // Create config snapshot if it does not exist.
        // @todo Determine if we need to notify the user via CLI.
        if (!file_exists($config_file_path)) {
          file_put_contents($config_file_path, Yaml::encode($config_data));
        }

        // Always test the config snapshot.
        $config_snapshot_data = Yaml::decode(file_get_contents($config_file_path));
        $this->assertEquals($config_data, $config_snapshot_data, sprintf('Config snapshot matches for %s', $config_file_name));

        $actual_filenames[$config_file_name] = $config_file_name;
      }
    }

    // Check that no config snapshot files were generated.
    $this->assertEquals($expected_filenames, $actual_filenames, 'No new config snapshot files were generated. If config snapshot files were generated as expected, please re-run this test.');
  }

}
