<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonapi_preview;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface;
use Drupal\jsonapi_extras\EntityToJsonApi;
use Drupal\schemadotorg_jsonapi\SchemaDotOrgJsonApiManagerInterface;

/**
 * Schema.org JSON:API preview builder.
 */
class SchemaDotOrgJsonApiPreviewBuilder implements SchemaDotOrgJsonApiPreviewBuilderInterface {
  use StringTranslationTrait;

  /**
   * Constructs a SchemaDotOrgJsonApiPreviewManager object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The resource type repository.
   * @param \Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface $resourceTypeRepository
   *   The resource type repository.
   * @param \Drupal\jsonapi_extras\EntityToJsonApi $entityToJsonApi
   *   The entity to JSON:API service.
   * @param \Drupal\schemadotorg_jsonapi\SchemaDotOrgJsonApiManagerInterface $schemaJsonApiManager
   *   The Schema.org JSON:API manager.
   */
  public function __construct(
    protected RendererInterface $renderer,
    protected ResourceTypeRepositoryInterface $resourceTypeRepository,
    protected EntityToJsonApi $entityToJsonApi,
    protected SchemaDotOrgJsonApiManagerInterface $schemaJsonApiManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(EntityInterface $entity): ?array {
    // Get includes.
    $resource_type = $this->resourceTypeRepository->get(
      $entity->getEntityTypeId(),
      $entity->bundle()
    );
    $includes = $this->schemaJsonApiManager->getResourceIncludes($resource_type);

    // Retrieve JSON API representation of this node.
    $render_context = new RenderContext();
    $data = $this->renderer->executeInRenderContext($render_context, function () use ($entity, $includes) {
      try {
        return $this->entityToJsonApi->normalize($entity, $includes);
      }
      catch (\Exception $exception) {
        return NULL;
      }
    });

    if (!$data) {
      return NULL;
    }

    // Display the JSON:API using a details element.
    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['schemadotorg-jsonapi-preview'],
      ],
      '#attached' => ['library' => ['schemadotorg_jsonapi_preview/schemadotorg_jsonapi_preview']],
    ];

    // Make the JSON pretty and enhance it.
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $build['json'] = [
      '#type' => 'html_tag',
      '#tag' => 'pre',
      '#plain_text' => $json,
      '#attributes' => ['data-schemadotorg-codemirror-mode' => 'application/ld+json'],
      '#attached' => ['library' => ['schemadotorg/codemirror.javascript']],
    ];

    // JSON:API endpoint.
    $entity_type_id = $entity->getEntityTypeId();
    $jsonapi_url = $this->entityToJsonApiUrl($entity, $includes);
    // Allow other modules to link to additional endpoints.
    $build['endpoints'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['schemadotorg-jsonapi-preview-endpoints']],
    ];
    $build['endpoints'][$entity_type_id] = [
      '#type' => 'item',
      '#title' => $this->t('JSON:API endpoint'),
      '#wrapper_attributes' => ['class' => ['container-inline']],
      'link' => [
        '#type' => 'link',
        '#url' => $jsonapi_url,
        '#title' => Unicode::truncate($jsonapi_url->toString(), 255, FALSE, TRUE),
        '#attributes' => ['title' => $jsonapi_url->toString()],
      ],
    ];
    return $build;
  }

  /**
   * Return the requested entity's JSON:API URL.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to generate the JSON from.
   * @param array $includes
   *   The list of includes.
   *
   * @return \Drupal\Core\Url
   *   The entity's JSON:API URL.
   *
   * @see \Drupal\jsonapi_extras\EntityToJsonApi::normalize
   */
  protected function entityToJsonApiUrl(EntityInterface $entity, array $includes = []): Url {
    $resource_type = $this->resourceTypeRepository->get(
      $entity->getEntityTypeId(),
      $entity->bundle()
    );
    $route_name = sprintf('jsonapi.%s.individual', $resource_type->getTypeName());
    $route_options = ['absolute' => TRUE];
    if ($resource_type->isVersionable() && $entity instanceof RevisionableInterface && $revision_id = $entity->getRevisionId()) {
      $route_options['query']['resourceVersion'] = 'id:' . $revision_id;
    }
    if ($includes) {
      $route_options['query']['include'] = implode(',', $includes);
    }
    return Url::fromRoute($route_name, ['entity' => $entity->uuid()], $route_options);
  }

}
