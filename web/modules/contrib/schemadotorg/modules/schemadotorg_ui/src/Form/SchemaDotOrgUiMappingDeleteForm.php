<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_ui\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Url;
use Drupal\field_ui\FieldUI;

/**
 * Provides a form for removing a Schema.org mapping.
 */
class SchemaDotOrgUiMappingDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): ?Url {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->getEntity();
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    return new Url("entity.{$entity_type_id}.schemadotorg_mapping", FieldUI::getRouteBundleParameter($entity_type, $bundle));
  }

  /**
   * {@inheritdoc}
   */
  protected function getRedirectUrl(): ?Url {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping */
    $mapping = $this->getEntity();
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    return FieldUI::getOverviewRouteInfo($entity_type_id, $bundle);
  }

}
