<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Traits;

/**
 * Trait for devel generate used by Schema.org mapping sets and starter kits.
 */
trait SchemaDotOrgDevelGenerateTrait {
  use SchemaDotOrgMappingStorageTrait;

  /**
   * Get entity type bundles.
   *
   * @param array $types
   *   An array of entity and Schema.org types.
   *
   * @return array
   *   An array entity type bundles.
   */
  protected function getEntityTypeBundles(array $types): array {
    // Collect the entity type and bundles to be generated.
    $entity_types = [];
    foreach ($types as $type) {
      [$entity_type, $schema_type] = explode(':', $type);
      $entity_types += [$entity_type => []];
      $existing_mapping = $this->getMappingStorage()->loadBySchemaType($entity_type, $schema_type);
      if ($existing_mapping) {
        $target_bundle = $existing_mapping->getTargetBundle();
        $entity_types[$entity_type][$target_bundle] = $target_bundle;
      }
    }
    return array_filter($entity_types);
  }

  /**
   * Execute devel generate command.
   *
   * @param array $types
   *   An array of entity and Schema.org types.
   * @param int $num
   *   The number of entities to create for each type.
   */
  protected function develGenerate(array $types, int $num = 5): void {
    // Make sure the devel generate manager and module are installed.
    if (!$this->develGenerateManager) {
      throw new \Exception('The devel_generate.module needs to be enabled.');
    }

    // Collect the entity type and bundles to be generated.
    $entity_types = $this->getEntityTypeBundles($types);

    // Mapping entity type to devel-generate command with default options.
    $commands = [
      'user' => ['users'],
      'node' => ['content', ['add-type-label' => TRUE]],
      'media' => ['media'],
      'taxonomy_term' => ['term'],
    ];
    foreach ($entity_types as $entity_type => $bundles) {
      if (!isset($commands[$entity_type])) {
        continue;
      }

      $devel_generate_plugin_id = $commands[$entity_type][0];
      foreach ($bundles as $bundle) {
        // Args which are [num] and [max_comments].
        $args = [(string) $num, 0];
        // Options.
        $options = $commands[$entity_type][1] ?? [];
        $options += [
          'kill' => TRUE,
          'bundles' => $bundle,
          'media-types' => $bundles,
          // Setting the below options to NULL prevents PHP warnings.
          'roles' => NULL,
          'base-fields' => NULL,
          'skip-fields' => NULL,
          'authors' => NULL,
          'feedback' => NULL,
          'languages' => NULL,
          'translations' => NULL,
        ];

        // Plugin.
        /** @var \Drupal\devel_generate\DevelGenerateBaseInterface $devel_generate_plugin */
        $devel_generate_plugin = $this->develGenerateManager->createInstance($devel_generate_plugin_id);
        // Parameters.
        $parameters = $devel_generate_plugin->validateDrushParams($args, $options);
        // Generate.
        $devel_generate_plugin->generate($parameters);
      }
    }
  }

}
