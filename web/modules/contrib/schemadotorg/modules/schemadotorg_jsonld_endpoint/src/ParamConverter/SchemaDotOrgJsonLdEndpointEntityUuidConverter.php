<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_endpoint\ParamConverter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\ParamConverter\EntityConverter;
use Drupal\schemadotorg_jsonld_endpoint\Routing\SchemaDotOrgJsonLdEndpointRoutes;
use Symfony\Component\Routing\Route;

/**
 * Parameter converter for upcasting entity UUIDs to full objects.
 *
 * Copied from the JSON:API module.
 *
 * @see \Drupal\jsonapi\ParamConverter\EntityUuidConverter
 *
 * @see https://www.drupal.org/project/drupal/issues/3032787
 *
 * @see \Drupal\Core\ParamConverter\EntityConverter
 *
 * @todo Remove when https://www.drupal.org/node/2353611 lands.
 */
class SchemaDotOrgJsonLdEndpointEntityUuidConverter extends EntityConverter {

  /**
   * The language manager.
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * Injects the language manager.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager to get the current content language.
   */
  public function setLanguageManager(LanguageManagerInterface $language_manager): void {
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, mixed $definition, $name, array $defaults): ?EntityInterface {
    $entity_type_id = $this->getEntityTypeFromDefaults($definition, $name, $defaults);
    if (!$this->entityTypeManager->hasDefinition($entity_type_id)) {
      return NULL;
    }

    $definition = $this->entityTypeManager->getDefinition($entity_type_id);
    $uuid_key = $definition->getKey('uuid');
    $entities = $this->entityTypeManager
      ->getStorage($entity_type_id)
      ->loadByProperties([$uuid_key => $value]);
    if (!$entities) {
      return NULL;
    }

    $entity = reset($entities);

    // If the entity type is translatable, ensure we return the proper
    // translation object for the current context.
    if ($entity instanceof TranslatableInterface && $entity->isTranslatable()) {
      // @see https://www.drupal.org/project/drupal/issues/2624770
      $entity = $this->entityRepository->getTranslationFromContext(
        entity: $entity,
        context: ['operation' => 'entity_upcast'],
      );
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route): bool {
    $has_jsonld_route_flag = $route->getDefault(SchemaDotOrgJsonLdEndpointRoutes::JSONLD_ROUTE_FLAG_KEY);
    $has_entity_type_definition = (!empty($definition['type']) && str_starts_with($definition['type'], 'entity'));
    return ($has_jsonld_route_flag && $has_entity_type_definition);
  }

}
