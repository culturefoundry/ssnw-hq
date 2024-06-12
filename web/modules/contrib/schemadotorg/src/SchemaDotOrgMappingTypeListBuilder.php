<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Schema.org mapping types.
 */
class SchemaDotOrgMappingTypeListBuilder extends SchemaDotOrgConfigEntityListBuilderBase {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $details_toggle = $this->getDetailsToggle();
    if ($details_toggle) {
      $header['entity_type'] = [
        'data' => $this->t('Type'),
      ];
      $header['default_schema_types'] = [
        'data' => $this->t('Default Schema.org types'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
      $header['recommended_schema_types'] = [
        'data' => $this->t('Recommended Schema.org types'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
      $header['multiple'] = [
        'data' => $this->t('Multiple'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
      $header['prefixes'] = [
        'data' => $this->t('Label/ID Prefixes'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
      $header['default_schema_type_properties'] = [
        'data' => $this->t('Default Schema.org type properties'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
      $header['default_base_fields'] = [
        'data' => $this->t('Base field mappings'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
      $header['default_component_weights'] = [
        'data' => $this->t('Default component weights'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '10%',
      ];
    }
    else {
      $header['entity_type'] = [
        'data' => $this->t('Type'),
        'width' => '25%',
      ];
      $header['default_schema_types'] = [
        'data' => $this->t('Default Schema.org types'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '25%',
      ];
      $header['recommended_schema_types'] = [
        'data' => $this->t('Recommended Schema.org types'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '25%',
      ];
      $header['multiple'] = [
        'data' => $this->t('Multiple'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '12%',
      ];
      $header['prefixes'] = [
        'data' => $this->t('Label/ID Prefixes'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
        'width' => '12%',
      ];
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
    // Type.
    $row['entity_type'] = $entity->label();

    // Default schema types.
    $row['default_schema_types'] = $this->buildAssociationItems($entity->get('default_schema_types'));

    // Recommended Schema.org types.
    $recommended_schema_types = $entity->get('recommended_schema_types') ?? [];
    $recommended_schema_type_labels = [];
    foreach ($recommended_schema_types as $recommended_schema_type) {
      $recommended_schema_type_labels[$recommended_schema_type['label']] = $recommended_schema_type['label'];
    }
    $row['recommended_schema_types'] = $this->buildItems($recommended_schema_type_labels);

    // Multiple.
    $row['multiple'] = $entity->get('multiple') ? $this->t('Yes') : $this->t('No');

    // Prefixes.
    $row['prefixes'] = implode(' / ', array_filter([$entity->get('label_prefix'), $entity->get('id_prefix')]));

    $details_toggle = $this->getDetailsToggle();
    if ($details_toggle) {

      // Default Schema.org type properties.
      $row['default_schema_type_properties'] = $this->buildAssociationItems($entity->get('default_schema_type_properties'));

      // Default base fields mapping.
      $row['default_base_fields'] = $this->buildAssociationItems($entity->get('default_base_fields'));

      // Default component weights.
      $row['default_component_weights'] = $this->buildKeyValuePairs($entity->get('default_component_weights'));
    }

    $row = $row + parent::buildRow($entity);

    // Highlight missing entity types.
    if (!$this->entityTypeManager->hasDefinition($entity->id())) {
      $row = [
        'data' => $row,
        'class' => ['color-warning'],
      ];
    }

    return $row;
  }

}
