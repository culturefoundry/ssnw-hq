<?php

namespace Drupal\content_model_documentation;

use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * A trait to provide some helpers for connecting to CM Documents.
 */
trait CMDocumentConnectorTrait {

  /**
   * The alias repository.
   *
   * @var \Drupal\path_alias\AliasRepositoryInterface
   */
  protected $aliasRepository;

  /**
   * Builds the predicted alias for a CMDocument.  Not guaranteed to exist.
   *
   * @param string $type
   *   The type of thing being documented node, module, paragraph, etc.
   * @param string $bundle
   *   The bundle or project of the thing being documented.
   * @param string $field
   *   The field or submodule of the thing being documented.
   * @param string|bool $absolute_domain
   *   An fully qualified scheme and domain [https://example-site.com].
   *
   * @return string
   *   The path for the thing being documented.
   */
  public function getCmDocumentPath($type, $bundle, $field = NULL, $absolute_domain = FALSE): string {
    // The $field is not working yet.  Do not use it.
    $bundle_hyphenated = str_replace('_', '-', $bundle);
    // Case race, first to be TRUE wins.
    switch (TRUE) {
      case (is_numeric($type) || is_numeric($bundle) || is_numeric($field)):
        // None of these elements should ever be numeric.
        $alias = '';
      case ($type === 'base') && (!empty($field)):
        // Field bases don't have an admin path, so path it to CM Document.
        $alias = "/admin/structure/cm_document/{$type}/{$bundle_hyphenated}/{$field}";
        break;

      case ($type === 'block_content') && (!empty($field)):
        $alias = "/admin/structure/block/block-content/manage/{$bundle}/fields/block_content.{$bundle}.{$field}/document";
        break;

      case ($type === 'block_content'):
        $alias = "/admin/structure/block/block-content/manage/{$bundle}/document";
        break;

      case ($type === 'media') && (!empty($field)):
        $alias = "/admin/structure/media/manage/{$bundle}/fields/media.{$bundle}.{$field}/document";
        break;

      case ($type === 'media'):
        $alias = "/admin/structure/media/manage/{$bundle}/document";
        break;

      case ($type === 'menu_link_content') && (!empty($field)):
        $alias = "/admin/structure/menu/manage/{$bundle_hyphenated}/fields/menu_link_content.{$bundle_hyphenated}.{$field}/document";
        break;

      case ($type === 'menu_link_content'):
        $alias = "/admin/structure/menu/manage/{$bundle}/document";
        break;

      case ($type === 'module'):
        // Align incoming values to the module context.
        $project = $bundle;
        $module = $field;
        if ($project === $module) {
          // This is not a submodule.
          $alias = "/admin/modules/documents/{$project}";
        }
        else {
          $alias = "/admin/modules/documents/{$project}/{$module}";
        }
        break;

      case ($type === 'node') && (!empty($field)):
        $alias = "/admin/structure/types/manage/{$bundle}/fields/node.{$bundle}.{$field}/document";
        break;

      case ($type === 'node'):
        $alias = "/admin/structure/types/manage/{$bundle}/document";
        break;

      case ($type === 'paragraph') && (!empty($field)):
        $alias = "/admin/structure/paragraphs_type/{$bundle}/fields/{$bundle}.{$field}/document";
        break;

      case ($type === 'paragraph'):
        $alias = "/admin/structure/paragraphs_type/{$bundle}/document";
        break;

      case ($type === 'site'):
        // This case only works when called in CMDocument class. But there
        // is not a situation where site would be called from elsewhere.
        $name = $this->cleanForAlias($this->getName());
        $alias = "/admin/structure/cm_document/{$bundle}/{$this->id()}/{$name}";
        break;

      case ($type === 'taxonomy_term') && (!empty($field)):
        $alias = "/admin/structure/taxonomy/manage/{$bundle}/overview/fields/taxonomy_term.{$bundle}.{$field}/document";
        break;

      case ($type === 'taxonomy_term'):
        $alias = "/admin/structure/taxonomy/manage/{$bundle}/document";
        break;
    }
    if (!empty($alias)) {
      // Trim trailing slash just to be safe.
      $alias = rtrim($alias, '/');
    }

    if (!empty($alias) && !empty($absolute_domain)) {
      $full_url = $absolute_domain . $alias;
    }

    return $full_url ?? $alias ?? '';
  }

  /**
   * Builds the predicted alias for a CMDocument.  Not guaranteed to exist.
   *
   * @param string $type
   *   The type of thing being documented node, module, paragraph, etc.
   * @param string $bundle
   *   The bundle or project of the thing being documented.
   * @param string $field
   *   The field or submodule of the thing being documented.
   * @param string|bool $absolute_domain
   *   An fully qualified scheme and domain [https://example-site.com].
   *
   * @return string
   *   The path for the thing being documented.
   */
  public function getVerifiedCmDocumentPath($type, $bundle, $field = NULL, $absolute_domain = FALSE): string {
    $link = $this->getCmDocumentPath($type, $bundle, $field, $absolute_domain);
    $path = str_replace($absolute_domain, '', $link);
    if (!empty($path)) {
      $this->getAliasRepository();
      $path_exists = $this->aliasRepository->lookupByAlias($path, 'en');
    }

    if (!empty($path_exists)) {
      return $link;
    }
    return '';

  }

  /**
   * Get the link to the CM document if it exists.
   *
   * @param mixed $path
   *   The path for the CM Document.
   * @param string $language
   *   The language of the document. (optional)
   *
   * @return link|string
   *   The link to the CM Document, or empty string if does not exist.
   */
  public function getCmDocumentLinkByPath($path, $language = 'en') {
    $this->getAliasRepository();
    $path = rtrim($path, '/');
    $this->getAliasRepository();
    $path_exists = $this->aliasRepository->lookupByAlias($path, 'en');
    if ($path_exists) {
      // The CM Document exists at this path, so link to it.
      $url = Url::fromUserInput($path);
      $link = Link::fromTextAndUrl($this->t('Document'), $url);
    }
    return $link ?? '';
  }

  /**
   * Get the link for a document if it exists.
   *
   * @param string $type
   *   The type of thing being documented node, module, paragraph, etc.
   * @param string $bundle
   *   The bundle or project of the thing being documented.
   * @param string $field
   *   The field or submodule of the thing being documented.
   *
   * @return link|string
   *   The link to the CM Document, or empty string if does not exist.
   */
  public function getCmDocumentLink($type, $bundle, $field = NULL) {
    $path = $this->getCmDocumentPath($type, $bundle, $field);
    $link = $this->getCmDocumentLinkByPath($path);

    return $link;
  }

  /**
   * Checks to see if the CM Document exists.
   *
   * @param string $type
   *   The type of thing being documented node, module, paragraph, etc.
   * @param string $bundle
   *   The bundle or project of the thing being documented.
   * @param string $field
   *   The field or submodule of the thing being documented.
   *
   * @return bool
   *   TRUE if the CM Document exists.  FALSE otherwise.
   */
  public function cmDocumentExists($type, $bundle, $field = NULL): bool {
    $path = $this->getCmDocumentPath($type, $bundle, $field);
    return $this->cmDocumentAliasExists($path);
  }

  /**
   * Checks to see if the alias has been defined, assumes if defined, exits.
   *
   * @param mixed $path
   *   The path for the CM Document.
   * @param string $language
   *   The language of the document. (optional)
   *
   * @return bool
   *   TRUE if the alias for the doc is defined.  FALSE otherwise.
   */
  public function cmDocumentAliasExists($path, $language = 'en'): bool {
    $this->getAliasRepository();
    $path_exists = $this->aliasRepository->lookupByAlias($path, 'en');
    if ($path_exists) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Gets the alias repository service.
   *
   * @return \Drupal\path_alias\AliasRepositoryInterface
   *   The string translation service.
   */
  protected function getAliasRepository() {
    if (!$this->aliasRepository) {
      $this->aliasRepository = \Drupal::service('path_alias.repository');
    }
    return $this->aliasRepository;
  }

  /**
   * Gets the content_model_documentation configuration settings.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The config for this module.
   */
  public function getConfig() {
    if (!$this->config) {
      $config_factory = \Drupal::service('config.factory');
      return $config_factory->get('content_model_documentation.settings');
    }
    return $this->config;
  }

  /**
   * Does cleanup on a string to make it ready for an alias.
   *
   * @param string $text
   *   The text to be cleaned.
   *
   * @return string
   *   The cleaned string.
   */
  protected function cleanForAlias($text): string {
    $clean_string = trim($text);
    $clean_string = strtolower($clean_string);
    // Convert n spaces to a single -.
    $clean_string = preg_replace('/\s+/', '-', $clean_string);
    // Remove all punctuation except - and _.
    $clean_string = preg_replace('/[^\w_-]+/', '', $clean_string);
    // Limit length.
    $clean_string = mb_strimwidth($clean_string, 0, 35);
    // Prevent a dangling - from trim.
    $clean_string = trim($clean_string, '-');
    return $clean_string;
  }

  /**
   * Reads config and gets entity types that are allowed to be documented.
   *
   * @return array
   *   An array whose elements in the form of 'entity name' => enabled status.
   */
  public function getDocumentableEntityTypes(): array {
    $config = $this->getConfig();
    $documentable_entities = [];
    $documentable_entities['block_content'] = $config->get('block');
    $documentable_entities['field'] = $config->get('field');
    $documentable_entities['media'] = $config->get('media');
    $documentable_entities['menu_link_content'] = $config->get('menu');
    $documentable_entities['node'] = $config->get('node');
    $documentable_entities['paragraph'] = $config->get('paragraph');
    $documentable_entities['taxonomy_term'] = $config->get('taxonomy');
    // Remove any that are disabled.
    $documentable_entities = array_filter($documentable_entities);
    $documentable_entities = array_keys($documentable_entities);

    return $documentable_entities;
  }

  /**
   * Checks to see if fieldable entity element is field-like.
   *
   * @param string $field_name
   *   An element to evaluate for being field-like.
   *
   * @return bool
   *   TRUE if its a field, FALSE otherwise.
   */
  public function isField(string $field_name) : bool {
    switch (TRUE) {
      case str_starts_with($field_name, 'field_'):
      case ($field_name === 'title'):
      case ($field_name === 'name'):
      case ($field_name === 'body'):
      case ($field_name === 'description'):
        return TRUE;

      default:
        return FALSE;
    }
  }

  /**
   * Get link to edit page using entity.field_config.node_field_edit_form route.
   *
   * @todo Update to account for other entity types supported by CM Document.
   *
   * @param string $type
   *   The entity type that the field is on.
   * @param string $bundle
   *   The bundle that the field is on.
   * @param string $machine
   *   The machine name of the field.
   *
   * @return \Drupal\Core\Link|string
   *   The link to the field instance edit page, or empty string if type not
   *   supported, or user has no access.
   */
  public function getFieldEditLink(string $type, string $bundle, string $machine) {

    // Set the $type_parameter_key based on the type.
    switch ($type) {
      case 'node':
        $type_parameter_key = 'node_type';
        break;

      case 'paragraph':
        $type_parameter_key = 'paragraphs_type';
        break;

    }

    // Get the current path.
    $current_path = \Drupal::service('path.current')->getPath();
    if (!empty($type_parameter_key)) {
      $link = Link::createFromRoute(
      $this->t('Edit'),
      "entity.field_config.{$type}_field_edit_form",
      [
        $type_parameter_key => $bundle,
        'field_config' => "{$type}.{$bundle}.{$machine}",
      ],
      [
        'query' => [
          'destination' => $current_path,
        ],
      ]
      );
      if (!$link->getUrl()->access()) {
        // User has no access to edit field, so output no link.
        $link = '';
      }
    }
    else {
      $link = '';
    }
    return $link;
  }

}
