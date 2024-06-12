<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Schema.org mapping form.
 *
 * @property \Drupal\schemadotorg\SchemaDotOrgMappingInterface $entity
 */
class SchemaDotOrgMappingForm extends EntityForm {

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * The Schema.org schema type manager.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The Schema.org schema type builder service.
   */
  protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->entityFieldManager = $container->get('entity_field.manager');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->schemaTypeBuilder = $container->get('schemadotorg.schema_type_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $entity */
    $entity = $this->getEntity();

    $form['#title'] = $this->t('Schema.org mapping');

    // Display entity information with bundle or without bundle.
    $entity_type_bundle = $entity->getTargetEntityBundleEntity();
    if ($entity_type_bundle) {
      $target_entity_type_bundle_definition = $entity->getTargetEntityTypeBundleDefinition();
      $link = $entity_type_bundle->toLink($entity_type_bundle->label(), 'edit-form')->toRenderable();
      $form['entity_type'] = [
        '#type' => 'item',
        '#title' => $target_entity_type_bundle_definition->getLabel(),
        'link' => $link + ['#suffix' => ' (' . $entity_type_bundle->id() . ')'],
      ];
    }
    else {
      $target_entity_type_definition = $entity->getTargetEntityTypeDefinition();
      $form['entity_type'] = [
        '#type' => 'item',
        '#title' => $this->t('Entity type'),
        '#markup' => $target_entity_type_definition->getLabel(),
      ];
    }

    $type_definition = $this->schemaTypeManager->getType($entity->getSchemaType());
    $form['schema_type'] = [
      '#type' => 'item',
      '#title' => $this->t('Schema.org type'),
    ];
    $form['schema_type']['label'] = [
      '#type' => 'link',
      '#title' => $type_definition['label'],
      '#url' => $this->schemaTypeBuilder->getItemUrl($type_definition['label']),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];
    $form['schema_type']['comment'] = [
      '#markup' => $this->schemaTypeBuilder->formatComment($type_definition['comment']),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    $schema_properties = $entity->getSchemaProperties();
    $field_definitions = $this->entityFieldManager->getFieldDefinitions($entity->getTargetEntityTypeId(), $entity->getTargetBundle());
    if ($schema_properties) {
      $header = [];
      $header['field'] = [
        'data' => $this->t('Field name'),
        'width' => '20%',
      ];
      $header['property'] = [
        'data' => $this->t('Schema.org property'),
        'width' => '80%',
      ];
      $rows = [];
      foreach ($schema_properties as $field_name => $property) {
        $field_definition = $field_definitions[$field_name] ?? NULL;
        if (!$field_definition) {
          continue;
        }
        $row = [];
        $row['field'] = $field_definition->getLabel();
        $property_definition = $this->schemaTypeManager->getProperty($property);
        $row['property'] = [
          'data' => [
            'label' => [
              '#markup' => $property_definition['label'],
              '#prefix' => '<div><strong>',
              '#suffix' => '</strong></div>',
            ],
            'comment' => [
              '#markup' => $this->schemaTypeBuilder->formatComment($property_definition['comment']),
              '#prefix' => '<div>',
              '#suffix' => '</div>',
            ],
          ],
        ];
        $rows[] = $row;
      }
      if ($rows) {
        $form['schema_properties'] = [
          '#type' => 'table',
          '#header' => $header,
          '#rows' => $rows,
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, FormStateInterface $form_state): array {
    return [];
  }

}
