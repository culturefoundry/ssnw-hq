<?php

namespace Drupal\schemadotorg\Plugin\ConfigAction;

use Drupal\Core\Config\Action\Attribute\ConfigAction;
use Drupal\Core\Config\Action\ConfigActionPluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\schemadotorg\SchemaDotOrgMappingManagerInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Config action to create a Schema.org type.
 */
#[ConfigAction(
  id: 'schemadotorg_mapping:createSchemaType',
  admin_label: new TranslatableMarkup('Create a Schema.org type'),
  entity_types: ['schemadotorg_mapping'],
)]
class SchemaDotOrgCreateSchemaType implements ConfigActionPluginInterface, ContainerFactoryPluginInterface {

  /**
   * The Schema.org schema names services.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * The Schema.org mapping manager service.
   */
  protected SchemaDotOrgMappingManagerInterface $schemaMappingManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = new static();
    $instance->schemaNames = $container->get('schemadotorg.names');
    $instance->schemaMappingManager = $container->get('schemadotorg.mapping_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function apply(string $configName, mixed $value): void {
    // Extract the entity type id and bundle from the config name.
    [, , $entity_type_id, $bundle] = explode('.', $configName);

    // Get Schema.org type from the value.
    if (isset($value['schema_type'])) {
      $schema_type = $value['schema_type'];
      unset($value['schema_type']);
    }
    else {
      throw new \InvalidArgumentException("A 'schema_type' is required.");
    }

    // Prepare the custom mapping defaults from the config action value.
    $defaults = $this->schemaMappingManager->prepareCustomMappingDefaults(
      $entity_type_id,
      $bundle,
      $schema_type,
      $value
    );

    $this->schemaMappingManager->createType($entity_type_id, $schema_type, $defaults);
  }

}
