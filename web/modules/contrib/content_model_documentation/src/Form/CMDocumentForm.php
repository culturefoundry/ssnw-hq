<?php

namespace Drupal\content_model_documentation\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for CMDocument edit forms.
 *
 * @ingroup cm_document
 */
class CMDocumentForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * The Entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new CMDocumentForm.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $document_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $document_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user account.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The core EntityFieldManager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The EntityTypeManager service.
   */
  public function __construct(
    EntityRepositoryInterface $document_repository,
    EntityTypeBundleInfoInterface $document_type_bundle_info,
    TimeInterface $time,
    AccountProxyInterface $account,
    EntityFieldManagerInterface $entityFieldManager,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    parent::__construct($document_repository, $document_type_bundle_info, $time);
    $this->account = $account;
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('current_user'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Order form elements.
   *
   * @param array $formKeys
   *   Form keys to set the order of.
   * @param array $form
   *   The form array.
   * @param int $offsetWeight
   *   The amount to offset each weight.
   *
   * @return array
   *   The modified form.
   */
  private static function orderFormElements(array $formKeys, array $form, int $offsetWeight = 0): array {
    foreach ($formKeys as $i => $formKey) {
      if (isset($form[$formKey])) {
        $form[$formKey]['#weight'] = $i + $offsetWeight;
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);
    $form['documented_entity']['#attributes']['name'] = 'documented_entity';
    $form['name']['#type'] = 'item';
    $form['name']['#attributes']['id'] = 'document-name';
    $form['name']['#states'] = [
      'visible' => [
        ':input[name="documented_entity"]' => [
          ['value' => 'site.note'],
          'or',
          ['value' => 'site.principle'],
          'or',
          ['value' => 'site.process'],
        ],
      ],
    ];

    $form['name']['widget'][0]['value']['#states'] = [
      'required' => [
        ':input[name="documented_entity"]' => [
          ['value' => 'site.note'],
          'or',
          ['value' => 'site.principle'],
          'or',
          ['value' => 'site.process'],
        ],
      ],
    ];

    // Authoring information for administrators.
    if (isset($form['user_id'])) {
      $form['author'] = [
        '#type' => 'details',
        '#title' => $this->t('Authoring information'),
        '#group' => 'advanced',
        '#attributes' => [
          'class' => ['cm_document-form-author'],
        ],
        '#weight' => -3,
        '#optional' => TRUE,
      ];

      $form['user_id']['#group'] = 'author';
    }

    // Order the advanced form elements.
    $form = self::orderFormElements([
      'revision_information',
      'author',
    ], $form);

    // Set the active element to the end.
    $form['status']['#group'] = 'footer';
    // Make revisions default on.
    $form["revision"]["#default_value"] = TRUE;
    $form["revision_information"]["#open"] = TRUE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $document */
    $document = $this->entity;
    $documented_entity_type = $document->getDocumentedEntityParameter('type');

    if ($documented_entity_type !== 'site') {
      // It is documenting an entity so generate the name.
      $document->name = $this->buildDocumentedName($document);
    }

    // Save as a new revision if requested to do so.
    if ($form_state->getValue('revision') == TRUE) {
      $document->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $document->setRevisionCreationTime($this->time->getRequestTime());
      $document->setRevisionUserId($this->account->id());
    }
    else {
      $document->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Content Model Document.', [
          '%label' => $document->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Content Model Document.', [
          '%label' => $document->label(),
        ]));
    }
    $form_state->setRedirect('entity.cm_document.collection');
  }

  /**
   * Build the documented entity name from the entity.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $document
   *   The CM Document.
   *
   * @return string
   *   The name of the document
   */
  protected function buildDocumentedName(CMDocumentInterface $document): string {
    $entity_type = $document->getDocumentedEntityParameter('type');
    if ($entity_type === 'module') {
      $project = $document->getDocumentedEntityParameter('project');
      $module = $document->getDocumentedEntityParameter('module');
      return ($project === $module) ? "Module: {$project}" : "Module: {$project}: {$module}";
    }
    elseif ($entity_type === 'base') {
      // This is a base field and has no name by itself.
      $fieldname = $document->getDocumentedEntityParameter('field');
      return "Base field: {$fieldname}";

    }
    else {
      // This is a real entity.
      $bundlename = $document->getDocumentedEntityParameter('bundle');
      $fieldname = $document->getDocumentedEntityParameter('field');
    }

    if (!empty($entity_type)) {
      $entitydefs = $this->entityTypeManager->getDefinition($entity_type);
      $entity_name = $entitydefs->getLabel()->render();
    }
    if (!empty($bundlename)) {
      // @todo Fix menu human names of the bundle not working.
      $bundlelabel = $bundlename;
      $storage_map = $document->getStorageMap();
      $storage_name = $storage_map[$entity_type] ?? $bundlename;
      $bundle_storage = $this->entityTypeManager->getStorage($storage_name);
      $bundle = $bundle_storage->load($bundlename);
      $bundlelabel = (!empty($bundle)) ? $bundle->label() : $bundlename;

    }
    if (!empty($fieldname)) {
      // We have a field name, so we can get a label.
      $definitions = $this->entityFieldManager->getFieldDefinitions($entity_type, $bundlename);
      $fieldlabel = $definitions[$fieldname]->getLabel();
    }
    $entity_name = $entity_name ?? $entity_type;
    $bundlelabel = $bundlelabel ?? $bundlename;
    $fieldlabel = $fieldlabel ?? $fieldname;

    return "{$entity_name}: {$bundlelabel}: {$fieldlabel}";
  }

}
