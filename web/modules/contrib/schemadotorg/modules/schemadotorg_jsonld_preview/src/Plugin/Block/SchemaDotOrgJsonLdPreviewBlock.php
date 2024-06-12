<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_jsonld_preview\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld_preview\SchemaDotOrgJsonLdPreviewBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Schema.org JSON-LD preview' block.
 *
 * @Block(
 *   id = "schemadotorg_jsonld_preview",
 *   admin_label = @Translation("Schema.org Blueprints JSON-LD Preview"),
 *   category = @Translation("Schema.org Blueprints")
 * )
 */
final class SchemaDotOrgJsonLdPreviewBlock extends BlockBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * The router admin context.
   */
  protected AdminContext $routerAdminContext;

  /**
   * The Schema.org JSON-LD preview builder service.
   */
  protected SchemaDotOrgJsonLdPreviewBuilderInterface $schemaJsonLdPreviewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->routerAdminContext = $container->get('router.admin_context');
    $instance->schemaJsonLdPreviewBuilder = $container->get('schemadotorg_jsonld_preview.builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label_display' => FALSE,
      'format' => SchemaDotOrgJsonLdPreviewBuilderInterface::JSONLD,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Select who the JSON-LD should be displayed.'),
      '#options' => [
        SchemaDotOrgJsonLdPreviewBuilderInterface::JSONLD => $this->t('JSON-LD'),
        SchemaDotOrgJsonLdPreviewBuilderInterface::DATA => $this->t('Data (table)'),
      ],
      '#require' => TRUE,
      '#default_value' => $this->configuration['format'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['format'] = $form_state->getValue('format');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): ?array {
    $configuration = $this->getConfiguration();
    $format = $configuration['format'];

    $build = $this->schemaJsonLdPreviewBuilder->build($format);
    if (!$build) {
      return NULL;
    }

    // Display the JSON-LD using a details element.
    $build['#type'] = 'details';
    $build['#title'] = ($format === SchemaDotOrgJsonLdPreviewBuilderInterface::JSONLD)
      ? $this->t('Schema.org JSON-LD')
      : $this->t('Schema.org data');
    $build['#attributes']['data-schemadotorg-details-key'] = 'schemadotorg-' . $format . '-preview';
    return ['details' => $build];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // If this is an admin route/page never add the JSON-LD to not have
    // JSON-LD impact the admin UI/UX performance.
    // @todo Determine if JSON-LD should be included on admin routes.
    if ($this->routerAdminContext->isAdminRoute()) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowedIfHasPermission($account, 'view schemadotorg jsonld');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // Apply the default cache contexts for JSON-LD to ensure that even if the
    // block has not JSON-LD is will be updated if the page now has JSON-LD.
    return array_merge(
      SchemaDotOrgJsonLdBuilderInterface::ROUTE_MATCH_CACHE_CONTEXTS,
      SchemaDotOrgJsonLdBuilderInterface::ENTITY_CACHE_CONTEXTS
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Apply the default cache tags for JSON-LD to ensure that even if the
    // block has not JSON-LD is will be updated if the page now has JSON-LD.
    return array_merge(
      SchemaDotOrgJsonLdBuilderInterface::ROUTE_MATCH_CACHE_TAGS,
      SchemaDotOrgJsonLdBuilderInterface::ENTITY_CACHE_TAGS
    );
  }

}
