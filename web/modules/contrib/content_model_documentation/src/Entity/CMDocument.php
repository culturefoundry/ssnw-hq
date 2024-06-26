<?php

declare(strict_types=1);

namespace Drupal\content_model_documentation\Entity;

use Drupal\content_model_documentation\CMDocumentConnectorTrait;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\user\UserInterface;

/**
 * Defines the Content Model Document entity.
 *
 * @ingroup cm_document
 *
 * @ContentEntityType(
 *   id = "cm_document",
 *   label = @Translation("Content Model Document"),
 *   label_plural = @Translation("Content Model Documents"),
 *   label_collection = @Translation("Content Model Document"),
 *   handlers = {
 *     "storage" = "\Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "view_builder" = "Drupal\content_model_documentation\CmDocumentViewBuilder",
 *     "list_builder" = "Drupal\content_model_documentation\CMDocumentListBuilder",
 *     "views_data" = "Drupal\content_model_documentation\Entity\CMDocumentViewsData",
 *     "form" = {
 *       "default" = "Drupal\content_model_documentation\Form\CMDocumentForm",
 *       "add" = "Drupal\content_model_documentation\Form\CMDocumentForm",
 *       "edit" = "Drupal\content_model_documentation\Form\CMDocumentForm",
 *       "delete" = "Drupal\content_model_documentation\Form\CMDocumentDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\content_model_documentation\CMDocumentHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\content_model_documentation\CMDocumentAccessControlHandler",
 *   },
 *   base_table = "cm_document",
 *   revision_table = "cm_document_revision",
 *   revision_data_table = "cm_document_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = FALSE,
 *   admin_permission = "administer content model document entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/cm_document/{cm_document}",
 *     "add-form" = "/admin/structure/cm_document/add",
 *     "edit-form" = "/admin/structure/cm_document/{cm_document}/edit",
 *     "delete-form" = "/admin/structure/cm_document/{cm_document}/delete",
 *     "version-history" = "/admin/structure/cm_document/{cm_document}/revisions",
 *     "revision" = "/admin/structure/cm_document/{cm_document}/revisions/{cm_document_revision}/view",
 *     "revision_revert" = "/admin/structure/cm_document/{cm_document}/revisions/{cm_document_revision}/revert",
 *     "revision_delete" = "/admin/structure/cm_document/{cm_document}/revisions/{cm_document_revision}/delete",
 *     "collection" = "/admin/structure/cm_document",
 *   },
 *   field_ui_base_route = "entity.cm_document.config_form",
 *   constraints = {
 *     "DocumentNameRequiredConstraint" = {},
 *     "OneDocumentPerEntityConstraint" = {}
 *   }
 * )
 */
class CMDocument extends EditorialContentEntityBase implements CMDocumentInterface {
  use CMDocumentConnectorTrait;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  public $entityFieldManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public $entityTypeManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $pathAliasStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type, $bundle = FALSE, $translations = []) {
    parent::__construct($values, $entity_type, $bundle, $translations);
    // Can't use Dependency Injection on a @ContentEntityType.
    $this->entityTypeManager = \Drupal::service('entity_type.manager');
    $this->entityFieldManager = \Drupal::service('entity_field.manager');
    $this->pathAliasStorage = $this->entityTypeManager->getStorage('path_alias');
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage, array &$values): void {
    parent::preCreate($storage, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel): array {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);

    // If no revision author has been set explicitly,
    // make the cm_document owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE): void {
    parent::postSave($storage, $update);
    if ($update) {
      // This is an update, so we have to delete aliases first.
      $exists = $this->removeExistingAlias();
      if (!$exists) {
        $this->setAlias();
      }
    }
    else {
      $this->setAlias();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName(string $name): CMDocumentInterface {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime(): int {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime(int $timestamp): CMDocumentInterface {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDocumentedEntityParameter($element): string {
    $documented = $this->get('documented_entity')->value;
    $return = '';
    if (!empty($documented)) {
      $elements = explode('.', $documented);
      $documented_entity = [
        'type' => $elements[0] ?? '',
        'bundle' => $elements[1] ?? '',
        'field' => $elements[2] ?? '',
      ];
      if ($documented_entity['type'] === 'module') {
        // Realign the parameters to match modules.
        $documented_entity = [
          'type' => $elements[0] ?? '',
          'project' => $elements[1] ?? '',
          'module' => $elements[2] ?? $elements[1] ?? '',
        ];

      }
      $return = !empty($documented_entity[$element]) ? $documented_entity[$element] : '';
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function getDocumentedEntity(): object|null {
    $content_type = NULL;
    $type = $this->getDocumentedEntityParameter('type');
    $bundle = $this->getDocumentedEntityParameter('bundle');
    $type_map = $this->getStorageMap();
    if (!empty($type_map[$type])) {
      $storage = $this->entityTypeManager->getStorage($type_map[$type]);
      $content_type = $storage->load($bundle);
    }
    return $content_type;
  }

  /**
   * Remove existing aliases that do not match new & return remaining count.
   *
   * @return int
   *   The number of aliases remaining.  Expecting either 0 or 1.
   */
  protected function removeExistingAlias(): int {
    $existing_aliases = $this->getExistingAliases();
    $new_alias = $this->getAliasPattern();
    $existing_count = count($existing_aliases);
    $deleted_count = 0;
    foreach ($existing_aliases as $existing_alias) {
      // Check to see if this matches the new one to create.
      if ($existing_alias->get('alias')->value !== $new_alias) {
        // It does not match the new one, so get rid of it.
        $existing_alias->delete();
        $deleted_count++;
      }
    }
    $aliases_remaining = $existing_count - $deleted_count;
    return $aliases_remaining;
  }

  /**
   * Sets the alias for the CM Document.
   */
  protected function setAlias(): void {
    $alias = $this->getAliasPattern();
    $path = $this->getUri();
    $new_alias = PathAlias::Create([
      'path' => $path,
      'alias' => $alias,
      'langcode' => $this->language()->getId() ?? 'en',
    ]);
    $new_alias->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getAliasPattern(): string {
    $alias = '';
    $type = $this->getDocumentedEntityParameter('type');
    if ($type === 'module') {
      $project = $this->getDocumentedEntityParameter('project');
      $module = $this->getDocumentedEntityParameter('module');
      $alias = $this->getCmDocumentPath($type, $project, $module);
    }
    else {
      $bundle = $this->getDocumentedEntityParameter('bundle');
      $field = $this->getDocumentedEntityParameter('field');
      $alias = $this->getCmDocumentPath($type, $bundle, $field);
    }

    return $alias;
  }

  /**
   * Get any aliases that exist for this document.
   *
   * @return array
   *   An array of existing Pathalias objects for this CM Document.
   */
  protected function getExistingAliases(): array {
    // Retrieve existing aliases for this CM Document.
    $path = $this->getUri();
    $existing_aliases = $this->pathAliasStorage->loadByProperties([
      'path' => $path,
      'langcode' => 'en',
    ]);

    return $existing_aliases;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri(): string {
    $uri = '/admin/structure/cm_document/' . $this->id();

    return $uri;
  }

  /**
   * {@inheritdoc}
   */
  public static function getOtherDocumentableTypes(): array {
    $non_entities = [
      'site.note' => 'Site Note',
      'site.principle' => 'Site Principle',
      'site.process' => 'Site Process',
    ];

    return $non_entities;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of this Content Model Document.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'region' => 'hidden',
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('A name for this Content Model Document.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 300,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -15,
      ])
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setRequired(FALSE);

    $fields['status']
      ->setLabel(new TranslatableMarkup('Published'))
      ->setDescription(t('If selected this Content Model Document will show.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -1,
      ]);

    $fields['documented_entity'] = BaseFieldDefinition::create('list_string')
      ->setLabel(new TranslatableMarkup('Documentation for'))
      ->setDescription(new TranslatableMarkup('Choose the entity or field that this document applies to.'))
      ->setTranslatable(FALSE)
      ->setSettings([
        'allowed_values_function' => '\Drupal\content_model_documentation\DocumentableEntityProvider::getUnDocumentedEntities',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -18,
      ])
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(TRUE);

    $fields['notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Notes'))
      ->setDescription(new TranslatableMarkup("Add freeform notes about this item."))
      ->setTranslatable(FALSE)
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -13,
        'settings' => [
          'rows' => 4,
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the Content Model Document was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the Content Model Document was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setInitialValue(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageMap(): array {
    return [
      'node' => 'node_type',
      'media' => 'media_type',
      'block_content' => 'block_content_type',
      'menu_link_content' => 'menu_link_content',
      'paragraph' => 'paragraphs_type',
      'taxonomy_term' => 'taxonomy_vocabulary',
    ];
  }

}
