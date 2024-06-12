<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_content_model_documentation;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\schemadotorg\SchemaDotOrgMappingInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface;
use Drupal\schemadotorg\SchemaDotOrgNamesInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Schema.org Content Model Documentation manager service.
 */
class SchemaDotOrgContentModelDocumentationManager implements SchemaDotOrgContentModelDocumentationManagerInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgContentModelDocumentationManager object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\schemadotorg\SchemaDotOrgNamesInterface $schemaNames
   *   The Schema.org names.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder
   *   The Schema.org type builder.
   */
  public function __construct(
    protected RequestStack $requestStack,
    protected ModuleHandlerInterface $moduleHandler,
    protected AccountProxyInterface $currentUser,
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected SchemaDotOrgNamesInterface $schemaNames,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
    protected SchemaDotOrgSchemaTypeBuilderInterface $schemaTypeBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function mappingTypeInsert(SchemaDotOrgMappingTypeInterface $mapping_type): void {
    $config = $this->configFactory
      ->getEditable('content_model_documentation.settings');
    $target_entity_type_id = $mapping_type->id();
    $documentable_entity = SchemaDotOrgContentModelDocumentationManagerInterface::DOCUMENTABLE_ENTITIES[$target_entity_type_id] ?? NULL;
    if ($documentable_entity) {
      $config->set($documentable_entity, 1);
    }
    $config->save();
  }

  /**
   * {@inheritdoc}
   */
  public function mappingInsert(SchemaDotOrgMappingInterface $mapping): void {
    if (!$this->hasDocumentation($mapping)) {
      return;
    }

    // Load the documentation so that it is always created when a mapping is
    // inserted via the UI and config import.
    $cm_document = $this->loadDocumentation($mapping);

    // Don't create markup field when config is syncing.
    if ($mapping->isSyncing()) {
      return;
    }

    // Check that we can use the a markup field for documentation.
    if (!$this->useMarkupField()) {
      return;
    }

    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $field_name = $this->getFieldName();

    // Create markup field storage.
    if (!FieldStorageConfig::loadByName($entity_type_id, $field_name)) {
      FieldStorageConfig::create([
        'field_name' => $field_name,
        'entity_type' => $entity_type_id,
        'type' => 'markup',
      ])->save();
    }

    // Create markup field instance.
    if (!FieldConfig::loadByName($entity_type_id, $bundle, $field_name)) {
      $description = $this->getDescription($mapping);
      $documentation_link = $cm_document->toLink(
        $this->getLinkText(),
        'canonical',
        ['attributes' => ['target' => '_blank']]
      )->toString();

      FieldConfig::create([
        'label' => $this->t('Documentation'),
        'field_name' => $field_name,
        'entity_type' => $entity_type_id,
        'bundle' => $bundle,
        'type' => 'markup',
        'settings' => [
          'markup' => [
            'value' => "<p>$description $documentation_link</p>",
            'format' => $this->getDefaultFormat(),
          ],
        ],
      ])->save();
    }

    // Set markup component in the default form display.
    $form_display = $this->entityDisplayRepository->getFormDisplay($entity_type_id, $bundle, 'default');
    if (!$form_display->getComponent($field_name)) {
      $form_display->setComponent($field_name, [
        'type' => 'markup',
        'weight' => -100,
      ]);
      $form_display->save();
    }
  }

  /**
   * Determine if Schema.org mapping has documentation.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   The Schema.org mapping.
   *
   * @return bool
   *   TRUE if Schema.org mapping has documentation.
   */
  protected function hasDocumentation(SchemaDotOrgMappingInterface $mapping): bool {
    $config = $this->configFactory
      ->get('schemadotorg_content_model_documentation.settings');

    $types = $config->get('types');
    $types = array_combine($types, $types);
    return (bool) $this->schemaTypeManager->getSetting($types, $mapping);
  }

  /**
   * {@inheritdoc}
   */
  public function openLinksInModal(): bool {
    return $this->configFactory
      ->get('schemadotorg_content_model_documentation.settings')
      ->get('link_modal');
  }

  /**
   * {@inheritdoc}
   */
  public function useMarkupField(): bool {
    $link_text = $this->getLinkText();
    return ($link_text && $this->moduleHandler->moduleExists('markup'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldName(): string {
    return $this->schemaNames->getFieldPrefix() . 'cm_documentation';
  }

  /**
   * {@inheritdoc}
   */
  public function getLinkText(): string {
    return $this->configFactory
      ->get('schemadotorg_content_model_documentation.settings')
      ->get('link_text');
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultNotes(): string {
    return trim($this->configFactory
      ->get('schemadotorg_content_model_documentation.settings')
      ->get('default_notes'));
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultFormat(): string {
    return $this->configFactory
      ->get('schemadotorg_content_model_documentation.settings')
      ->get('default_format');
  }

  /**
   * Get a description from a Schema.org mapping.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   *
   * @return string
   *   A description from a Schema.org mapping.
   */
  protected function getDescription(SchemaDotOrgMappingInterface $mapping): string {
    // Check for the target entity bundle's description.
    $target_entity_bundle_description = $mapping->getTargetEntityBundleEntity()->get('description');
    if ($target_entity_bundle_description) {
      return $target_entity_bundle_description;
    }

    // Check for a  custom description.
    $schema_type = $mapping->getSchemaType();
    $custom_description = $this->configFactory
      ->get('schemadotorg_descriptions.settings')
      ->get('custom_descriptions.' . $schema_type);
    if ($custom_description) {
      return $custom_description;
    }

    // Now, get the default Schema.org type's comment as the description.
    $type_definition = $this->schemaTypeManager->getType($schema_type);
    return $this->schemaTypeBuilder->formatComment(
      $type_definition['drupal_description'],
      ['base_path' => 'https://schema.org/']
    );
  }

  /**
   * Load, create, or convert a Schema.org mapping's documentation.
   *
   * @param \Drupal\schemadotorg\SchemaDotOrgMappingInterface $mapping
   *   A Schema.org mapping.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   A Schema.org mapping's documentation.
   */
  protected function loadDocumentation(SchemaDotOrgMappingInterface $mapping): CMDocumentInterface {
    $entity_type_id = $mapping->getTargetEntityTypeId();
    $bundle = $mapping->getTargetBundle();
    $schema_type = $mapping->getSchemaType();

    /** @var \Drupal\content_model_documentation\CMDocumentStorageInterface $cm_document_storage */
    $cm_document_storage = $this->entityTypeManager->getStorage('cm_document');

    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface[] $cm_documents */
    $cm_documents = $cm_document_storage->loadByProperties(['documented_entity' => "$entity_type_id.$bundle"])
      ?: $cm_document_storage->loadByProperties(['name' => 'https://schema.org/' . $schema_type]);

    if ($cm_documents) {
      /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document */
      $cm_document = reset($cm_documents);
      // Convert a Schema.org mapping documentation to an entity type
      // bundle documentation.
      if (str_starts_with($cm_document->getName(), 'https://schema.org')) {
        $cm_document->set('name', $mapping->getTargetEntityBundleEntity()->label());
        $cm_document->set('documented_entity', "$entity_type_id.$bundle");
        $cm_document->save();
      }
    }
    else {
      // Create content model documentation for the Schema.org mapping.
      /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document */
      $cm_document = $cm_document_storage->create([
        'status' => 1,
        'user_id' => $this->currentUser->id(),
        'name' => $mapping->getTargetEntityBundleEntity()->label(),
        'documented_entity' => "$entity_type_id.$bundle",
        'notes' => [
          'value' => '<p>'
            . $mapping->getTargetEntityBundleEntity()->get('description')
            . '</p>'
            . PHP_EOL
            . $this->getDefaultNotes(),
          'format' => $this->getDefaultFormat(),
        ],
      ]);
      $cm_document->save();
    }

    return $cm_document;
  }

}
