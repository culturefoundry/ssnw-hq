<?php

namespace Drupal\schemadotorg_content_model_documentation;

use Drupal\content_model_documentation\CmDocumentViewBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Overrides the Content Model Document view builder.
 */
class SchemaDotOrgContentModelDocumentationCmDocumentViewBuilder extends CmDocumentViewBuilder {

  /**
   * The Schema.org names service.
   */
  protected SchemaDotOrgNamesInterface $schemaNames;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->schemaNames = $container->get('schemadotorg.names');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function isField(string $field_name): bool {
    return str_starts_with($field_name, $this->schemaNames->getFieldPrefix()) || parent::isField($field_name);
  }

}
