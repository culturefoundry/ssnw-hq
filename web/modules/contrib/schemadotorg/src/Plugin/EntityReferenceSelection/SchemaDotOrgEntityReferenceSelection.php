<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginBase;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionWithAutocreateInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\user\EntityOwnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default plugin implementation of the Schema.org Entity Selection plugin.
 *
 * Also serves as a base class for specific types of Schema.org Entity
 * Reference Selection plugins.
 *
 * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection
 * @see \Drupal\Core\schemadotorg\Plugin\Derivative\SchemaDotOrgEntitySelectionDeriver
 * @see schemadotorg_schemadotorg_mapping_insert()
 * @see schemadotorg_field_config_presave()
 * @see schemadotorg_form_field_config_edit_form_alter()
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg",
 *   label = @Translation("Schema.org: Filter by Schema.org types"),
 *   group = "schemadotorg",
 *   weight = 0,
 *   deriver = "\Drupal\schemadotorg\Plugin\Derivative\SchemaDotOrgEntitySelectionDeriver"
 * )
 */
abstract class SchemaDotOrgEntityReferenceSelection extends SelectionPluginBase implements ContainerFactoryPluginInterface, SelectionWithAutocreateInterface {

  /**
   * The current user.
   */
  protected AccountInterface $currentUser;

  /**
   * The entity type manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The schema.org mapping service.
   */
  protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager;

  /**
   * The mapping storage.
   */
  protected SchemaDotOrgMappingStorageInterface|ConfigEntityStorageInterface $mappingStorage;

  /**
   * The entity repository.
   */
  protected EntityRepositoryInterface $entityRepository;

  /**
   * {@inheritdoc}
   */
  final public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->currentUser = $container->get('current_user');
    $instance->schemaTypeManager = $container->get('schemadotorg.schema_type_manager');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->mappingStorage = $container->get('entity_type.manager')->getStorage('schemadotorg_mapping');
    $instance->entityRepository = $container->get('entity.repository');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'schema_types' => [],
      'excluded_schema_types' => [],
      'target_bundles' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration): void {
    // Convert 'schema_types' that are passed as a string to an array.
    // The 'schema_types' will be a string when this handler is validated
    // via form submit.
    // @see
    foreach (['schema_types', 'excluded_schema_types'] as $key) {
      if (isset($configuration[$key]) && is_string($configuration[$key])) {
        $configuration[$key] = preg_split('/\s*,\s*/', $configuration[$key]);
      }
    }

    parent::setConfiguration($configuration);

    // Always recalculate target bundles, even though they are calculated on
    // field config presave.
    // @see schemadotorg_field_config_presave()
    $this->configuration['target_bundles'] = static::getTargetBundles($this->configuration);
  }

  /**
   * {@inheritdoc}
   *
   * @see schemadotorg_form_field_config_edit_form_alter()
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $configuration = $this->getConfiguration();

    $form['target_type'] = [
      '#type' => 'value',
      '#value' => $configuration['target_type'],
    ];

    $form['schema_types'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Schema.org types'),
      '#description' => $this->t('Enter one or more Schema.org types to filter available content.')
      . ' ' . $this->t("Enter 'Thing' to include all available content."),
      '#tags' => TRUE,
      '#required' => TRUE,
      '#target_type' => 'Thing',
      '#default_value' => $configuration['schema_types'],
    ];

    $form['excluded_schema_types'] = [
      '#type' => 'schemadotorg_autocomplete',
      '#title' => $this->t('Excluded Schema.org types'),
      '#description' => $this->t('Enter one or more Schema.org types to exclude from available content.'),
      '#tags' => TRUE,
      '#target_type' => 'Thing',
      '#default_value' => $configuration['excluded_schema_types'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection::getReferenceableEntities
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $target_type = $this->getConfiguration()['target_type'];

    $query = $this->buildEntityQuery($match, $match_operator);
    if ($limit > 0) {
      $query->range(0, $limit);
    }

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    $options = [];
    $entities = $this->entityTypeManager->getStorage($target_type)->loadMultiple($result);
    foreach ($entities as $entity_id => $entity) {
      $bundle = $entity->bundle();
      $options[$bundle][$entity_id] = Html::escape($this->entityRepository->getTranslationFromContext($entity)->label() ?? '');
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection::countReferenceableEntities
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    $query = $this->buildEntityQuery($match, $match_operator);
    return $query
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\schemadotorg\Plugin\EntityReferenceSelection\SchemaDotOrgEntityReferenceSelection::validateReferenceableEntities
   */
  public function validateReferenceableEntities(array $ids) {
    $result = [];
    if ($ids) {
      $target_type = $this->configuration['target_type'];
      $entity_type = $this->entityTypeManager->getDefinition($target_type);
      $query = $this->buildEntityQuery();
      $result = $query
        ->condition($entity_type->getKey('id'), $ids, 'IN')
        ->execute();
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection::createNewEntity
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

    $values = [
      $entity_type->getKey('label') => $label,
    ];

    if ($bundle_key = $entity_type->getKey('bundle')) {
      $values[$bundle_key] = $bundle;
    }

    $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);

    if ($entity instanceof EntityOwnerInterface) {
      $entity->setOwnerId($uid);
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection::validateReferenceableNewEntities
   */
  public function validateReferenceableNewEntities(array $entities): array {
    return array_filter($entities, function ($entity) {
      $target_bundles = $this->getConfiguration()['target_bundles'];
      if (isset($target_bundles)) {
        return in_array($entity->bundle(), $target_bundles);
      }
      return TRUE;
    });
  }

  /**
   * Builds an EntityQuery to get referenceable entities.
   *
   * @param string|null $match
   *   (Optional) Text to match the label against. Defaults to NULL.
   * @param string $match_operator
   *   (Optional) The operation the matching should be done with. Defaults
   *   to "CONTAINS".
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The EntityQuery object with the basic conditions and sorting applied to
   *   it.
   *
   * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection::buildEntityQuery
   */
  protected function buildEntityQuery(?string $match = NULL, string $match_operator = 'CONTAINS'): QueryInterface {
    $configuration = $this->getConfiguration();
    $target_type = $configuration['target_type'];
    $entity_type = $this->entityTypeManager->getDefinition($target_type);

    $query = $this->entityTypeManager->getStorage($target_type)->getQuery();
    $query->accessCheck(TRUE);

    // If 'target_bundles' is NULL, all bundles are referenceable, no further
    // conditions are needed.
    if (is_array($configuration['target_bundles'])) {
      // If 'target_bundles' is an empty array, no bundle is referenceable,
      // force the query to never return anything and bail out early.
      if ($configuration['target_bundles'] === []) {
        $query->condition($entity_type->getKey('id'), NULL, '=');
        return $query;
      }
      elseif ($entity_type->hasKey('bundle')) {
        $query->condition($entity_type->getKey('bundle'), $configuration['target_bundles'], 'IN');
      }
      else {
        // If 'target_bundle' is set and entity type doesn't support bundles
        // something is wrong.
        $message = \sprintf(
          "Trying to use non-empty 'target_bundle' configuration on entity type '%s' without bundle support.",
          $entity_type->id(),
        );
        throw new UnsupportedEntityTypeDefinitionException($message);
      }
    }

    if (isset($match) && $label_key = $entity_type->getKey('label')) {
      $query->condition($label_key, $match, $match_operator);
    }

    // Add entity-access tag.
    $query->addTag($target_type . '_access');

    // Add the Selection handler for system_query_entity_reference_alter().
    $query->addTag('entity_reference');
    $query->addMetaData('entity_reference_selection_handler', $this);
    return $query;
  }

  /**
   * Get target bundles for the handler's configuration/settings.
   *
   * @param array $configuration
   *   The handler's configuration.
   *
   * @return array
   *   An associative array of target bundles with the key and value being
   *   the bundle.
   */
  public static function getTargetBundles(array $configuration): array {
    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingStorageInterface $mapping_storage */
    $mapping_storage = \Drupal::entityTypeManager()->getStorage('schemadotorg_mapping');

    // Get target bundles for the selected Schema.org types.
    $target_bundles = $mapping_storage->getRangeIncludesTargetBundles(
      $configuration['target_type'],
      $configuration['schema_types'],
      FALSE,
    );

    // Excluded Schema.org types from target bundles.
    if (!empty($configuration['excluded_schema_types'])) {
      $exclude_target_bundles = $mapping_storage->getRangeIncludesTargetBundles(
        $configuration['target_type'],
        $configuration['excluded_schema_types'],
        FALSE,
      );
      if (!empty($exclude_target_bundles)) {
        $target_bundles = array_diff_key($target_bundles, $exclude_target_bundles);
      }
    }

    return $target_bundles;
  }

}
