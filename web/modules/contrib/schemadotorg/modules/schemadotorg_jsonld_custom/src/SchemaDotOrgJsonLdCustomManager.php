<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_custom;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\Token;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;

/**
 * Schema.org JSON-LD custom manager.
 */
class SchemaDotOrgJsonLdCustomManager implements SchemaDotOrgJsonLdCustomInterface {

  /**
   * Constructs a SchemaDotOrgJsonLdCustomManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected Token $token,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingDefaultsAlter(array &$defaults, string $entity_type_id, ?string $bundle, string $schema_type): void {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = $this->entityTypeManager->getStorage('schemadotorg_mapping');
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = $mapping_storage->load("$entity_type_id.$bundle");
    if ($mapping) {
      return;
    }

    // Make sure that the custom JSON is not defined.
    if (NestedArray::keyExists($defaults, ['third_party_settings', 'schemadotorg_jsonld_custom', 'json'])) {
      return;
    }

    $default_json = $this->getDefaultJson($entity_type_id, $schema_type, 'default_schema_mapping_json');
    if ($default_json) {

      // Tidy the default JSON.
      $default_data = @json_decode($default_json, TRUE);
      if (json_last_error() === JSON_ERROR_NONE) {
        $default_json = json_encode($default_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
      }

      NestedArray::setValue(
        $defaults,
        ['third_party_settings', 'schemadotorg_jsonld_custom', 'json'],
        $default_json
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function jsonLdSchemaTypeEntityLoad(array &$data, EntityInterface $entity, ?SchemaDotOrgMappingInterface $mapping, BubbleableMetadata $bubbleable_metadata): void {
    // Make sure this is a content entity with a mapping.
    if (!$entity instanceof ContentEntityInterface
      || !$mapping) {
      return;
    }

    // Add custom JSON-LD settings as a cache dependency.
    $config = $this->configFactory->get('schemadotorg_jsonld_custom.settings');
    $bubbleable_metadata->addCacheableDependency($config);

    // Default Schema.org types JSON-LD.
    $schema_type = $mapping->getSchemaType();
    $schema_type_json = $this->getDefaultJson($entity->getEntityTypeId(), $schema_type, 'default_schema_type_json');
    $this->schemaTypeEntityMergeJson($data, $entity, $schema_type_json);

    // Default Schema.org mappings custom JSON-LD.
    $mapping_json = $mapping->getThirdPartySetting('schemadotorg_jsonld_custom', 'json');
    $this->schemaTypeEntityMergeJson($data, $entity, $mapping_json);
  }

  /**
   * {@inheritdoc}
   */
  public function buildRouteMatchJsonLd(RouteMatchInterface $route_match, BubbleableMetadata $bubbleable_metadata): ?array {
    $config = $this->configFactory->get('schemadotorg_jsonld_custom.settings');

    // Add custom JSON-LD settings as a cache dependency.
    $bubbleable_metadata->addCacheableDependency($config);

    $url = Url::fromRouteMatch($route_match);

    $request_path = parse_url($url->toString(), PHP_URL_PATH);
    // Not using $url->getInternalPath() because we want the path prefixed
    // with a slash (/).
    $system_path = parse_url($url->setOption('path_processing', FALSE)->toString(), PHP_URL_PATH);

    // Handle <front> page JSON-LD.
    $front_path = $this->configFactory->get('system.site')->get('page.front');
    if (in_array($front_path, [$request_path, $system_path])) {
      $json = $config->get('path_json./')
        ?: $config->get("path_json.<front>");
      if ($json) {
        return @json_decode($this->token->replace($json, [], [], $bubbleable_metadata), TRUE) ?? NULL;
      }
    }

    // Handle other pages JSON-LD.
    $json = $config->get("path_json.$request_path")
      ?: $config->get("path_json.$system_path");
    if ($json) {
      return @json_decode($this->token->replace($json, [], [], $bubbleable_metadata), TRUE) ?? NULL;
    }

    return NULL;
  }

  /**
   * Merge custom JSON with Schema.org type JSON-LD data.
   *
   * @param array $data
   *   The Schema.org JSON-LD data for an entity.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string|null $json
   *   JSON string.
   */
  protected function schemaTypeEntityMergeJson(array &$data, EntityInterface $entity, ?string $json): void {
    if (!$json) {
      return;
    }

    $json = $this->token->replace($json, [$entity->getEntityTypeId() => $entity]);
    $json_data = @json_decode($json, TRUE);
    if (!$json_data) {
      return;
    }

    $data = NestedArray::mergeDeep($data, $json_data);
  }

  /**
   * Get the default custom JSON-LD for Schema.org type.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $schema_type
   *   A Schema.org type.
   * @param string $config_name
   *   The config property name.
   *
   * @return string|null
   *   The default custom JSON-LD for Schema.org type.
   */
  protected function getDefaultJson(string $entity_type_id, string $schema_type, string $config_name): ?string {
    $settings = $this->configFactory
      ->get('schemadotorg_jsonld_custom.settings')
      ->get($config_name);
    $parts = [
      'entity_type_id' => $entity_type_id,
      'schema_type' => $schema_type,
    ];
    return $this->schemaTypeManager->getSetting($settings, $parts);
  }

}
