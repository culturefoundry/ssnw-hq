<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_endpoint\Controller;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for Schema.org JSON-LD endpoint routes.
 */
class SchemaDotOrgJsonLdEndpointController extends ControllerBase {

  /**
   * The renderer service.
   */
  protected RendererInterface $renderer;

  /**
   * The Schema.org JSON-LD manager.
   */
  protected SchemaDotOrgJsonLdManagerInterface $manager;

  /**
   * The Schema.org JSON-LD builder.
   */
  protected SchemaDotOrgJsonLdBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->renderer = $container->get('renderer');
    $instance->manager = $container->get('schemadotorg_jsonld.manager');
    $instance->builder = $container->get('schemadotorg_jsonld.builder');
    return $instance;
  }

  /**
   * Build the Schema.org JSON-LD response for an entity.
   *
   * We need to build the JSON-LD in a render context to ensure we collect
   * all cache contexts and tags.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\Core\Cache\CacheableResponseInterface
   *   The Schema.org JSON-LD response for an entity.
   *
   * @see https://www.drupal.org/forum/support/module-development-and-code-questions/2022-07-20/the-controller-result-claims-to-be-providing-relevant-cache-metadata-but-leaked-metadata-was#comment-14657883
   */
  public function getEntity(EntityInterface $entity): CacheableResponseInterface {
    $bubbleable_metadata = new BubbleableMetadata();

    $context = new RenderContext();

    /** @var \Drupal\Core\Cache\CacheableResponseInterface $response */
    $response = $this->renderer->executeInRenderContext($context,
      function () use ($entity, $bubbleable_metadata): CacheableResponseInterface {
        $entity_route_match = $this->manager->getEntityRouteMatch($entity);
        if ($entity_route_match) {
          $data = $this->builder->build($entity_route_match, $bubbleable_metadata);
        }
        else {
          $data = $this->builder->buildEntity(
            entity: $entity,
            bubbleable_metadata: $bubbleable_metadata,
          );
          if ($data) {
            $data = ['@context' => 'https://schema.org'] + $data;
          }
        }

        if (!$data) {
          throw new NotFoundHttpException();
        }

        return new CacheableJsonResponse($data);
      }
    );

    // Merge any bubbleable metadata for the JSON-LD builder.
    $response->addCacheableDependency($bubbleable_metadata);

    // Merge any bubbleable metadata for the context.
    if (!$context->isEmpty()) {
      $context_metadata = $context->pop();
      $response->addCacheableDependency($context_metadata);
    }

    return $response;
  }

  /**
   * Checks view access to an entity's Schema.org JSON-LD.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user session for which to check access.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, EntityInterface $entity): AccessResultInterface {
    return $entity->access('view', $account, TRUE);
  }

}
