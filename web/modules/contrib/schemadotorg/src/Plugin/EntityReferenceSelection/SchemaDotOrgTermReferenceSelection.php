<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * User plugin implementation of the Schema.org Entity Selection plugin.
 *
 * @see \Drupal\user\Plugin\EntityReferenceSelection\UserSelection
 *
 * @EntityReferenceSelection(
 *   id = "schemadotorg:taxonomy_term",
 *   label = @Translation("Schema.org: Filter by Schema.org types"),
 *   entity_types = {"taxonomy_term"},
 *   group = "schemadotorg",
 *   weight = 1,
 * )
 */
class SchemaDotOrgTermReferenceSelection extends SchemaDotOrgEntityReferenceSelection {

  /**
   * Entity type bundle info service.
   */
  public EntityTypeBundleInfoInterface $entityTypeBundleInfo;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeBundleInfo = $container->get('entity_type.bundle.info');
    return $instance;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection::getReferenceableEntities
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    if ($match || $limit) {
      return parent::getReferenceableEntities($match, $match_operator, $limit);
    }

    $options = [];

    $bundles = $this->entityTypeBundleInfo->getBundleInfo('taxonomy_term');
    $bundle_names = $this->getConfiguration()['target_bundles'] ?: array_keys($bundles);

    $has_admin_access = $this->currentUser->hasPermission('administer taxonomy');
    $unpublished_terms = [];
    foreach ($bundle_names as $bundle) {
      if ($vocabulary = Vocabulary::load($bundle)) {
        /** @var \Drupal\taxonomy\TermInterface[] $terms */
        $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree(
          vid: $vocabulary->id(),
          load_entities: TRUE,
        );
        if ($terms) {
          foreach ($terms as $term) {
            if (!$has_admin_access && (!$term->isPublished() || in_array($term->parent->target_id, $unpublished_terms))) {
              $unpublished_terms[] = $term->id();
              continue;
            }
            $options[$vocabulary->id()][$term->id()] = str_repeat('-', $term->depth) . Html::escape($this->entityRepository->getTranslationFromContext($term)->label());
          }
        }
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection::countReferenceableEntities
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    if ($match) {
      return parent::countReferenceableEntities($match, $match_operator);
    }

    $total = 0;
    $referenceable_entities = $this->getReferenceableEntities($match, $match_operator, 0);
    foreach ($referenceable_entities as $entities) {
      $total += count($entities);
    }
    return $total;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection::buildEntityQuery
   */
  protected function buildEntityQuery(?string $match = NULL, string $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);

    // Adding the 'taxonomy_term_access' tag is sadly insufficient for terms:
    // core requires us to also know about the concept of 'published' and
    // 'unpublished'.
    if (!$this->currentUser->hasPermission('administer taxonomy')) {
      $query->condition('status', 1);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection::createNewEntity
   */
  public function createNewEntity($entity_type_id, $bundle, $label, $uid) {
    $term = parent::createNewEntity($entity_type_id, $bundle, $label, $uid);

    // In order to create a referenceable term, it needs to published.
    /** @var \Drupal\taxonomy\TermInterface $term */
    $term->setPublished();

    return $term;
  }

}
