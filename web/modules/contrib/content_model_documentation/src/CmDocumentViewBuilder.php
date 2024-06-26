<?php

namespace Drupal\content_model_documentation;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Defines a class for entity view builder for entities.
 */
class CmDocumentViewBuilder extends EntityViewBuilder {

  use CMDocumentConnectorTrait;

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $cm_document, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($cm_document, $view_mode, $langcode);
    $add_ons = $this->getAddOns($cm_document);
    $build = array_merge($build, $add_ons);

    return $build;
  }

  /**
   * Grabs all the add on render arrays for a given kind of documentation.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   An array of render arrays to be added on.
   */
  protected function getAddOnTypes(CMDocumentInterface $cm_document): array {
    $type = $cm_document->getDocumentedEntityParameter('type');
    $bundle = $cm_document->getDocumentedEntityParameter('bundle');
    $field = $cm_document->getDocumentedEntityParameter('field');
    // Order of the addons specified determines their order on the page.
    switch (TRUE) {
      case ($type === 'base' && !empty($field)):
        // This is documentation for a base field.
        $add_ons = ['SiblingFields'];
        break;

      case (!empty($field)):
        // This is documentation for a field.
        $add_ons = ['AppearsOn', 'BaseField', 'SiblingFields'];
        break;

      case ((empty($field)) && ($type !== 'site') && ($type !== 'module')):
        // This is fieldable entity.
        $add_ons = ['FieldsOnEntity'];
        break;

      default:
        $add_ons = [];
        break;
    }
    return $add_ons;
  }

  /**
   * Gets all the add ons that should appear on a CM Document.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   An array of render arrays for each of the related add ons.
   */
  protected function getAddOns(CMDocumentInterface $cm_document): array {
    $add_on_types = $this->getAddOnTypes($cm_document);
    $add_ons = [];
    foreach ($add_on_types as $i => $add_on) {
      $func = "get{$add_on}";
      $add_ons["$add_on"] = $this->$func($cm_document);
    }

    return $add_ons;
  }

  /**
   * Gets a render array showing the content type this field appears on.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   A renderable array for the appears on section of the page.
   */
  protected function getAppearsOn(CMDocumentInterface $cm_document): array {
    $add_on = [];
    $type = $cm_document->getDocumentedEntityParameter('type');
    $bundle = $cm_document->getDocumentedEntityParameter('bundle');
    $field = $cm_document->getDocumentedEntityParameter('field');
    $documented_entity = $cm_document->getDocumentedEntity();
    $label = (empty($documented_entity)) ? $this->t('undefined') : $documented_entity->label();
    $cm_doc_link = $this->getCmDocumentLink($type, $bundle);
    if ($cm_doc_link) {
      $add_on = $cm_doc_link->toRenderable();
      $add_on['#title'] = "$type $label ($bundle)";
    }
    else {
      $add_on = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => "$type $label ($bundle)",
      ];
    }
    $add_on['#prefix'] = "<span class=\"field__label\">{$this->t('Appears on: ')}</span>";
    // This one should be near the top of the page.
    $add_on['#weight'] = -10;
    return $add_on;
  }

  /**
   * Gets a link to the base field CM Document if it exists.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   A render array for a link if one exists, or an empty array.
   */
  protected function getBaseField(CMDocumentInterface $cm_document): array {
    $add_on = [];
    $field = $cm_document->getDocumentedEntityParameter('field');
    $base_cm_doc = "base.field.$field";
    // Does documentation for the base field exist?
    $cm_doc_link = $this->getCmDocumentLink('base', 'field', $field);
    if ($cm_doc_link) {
      $add_on = $cm_doc_link->toRenderable();
      $add_on['#title'] = $this->t('Base @field documentation', ['@field' => $field]);
      $add_on['#weight'] = 10;
    }
    return $add_on;
  }

  /**
   * Gets a a table of siblings if they exist.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   A render array for a table if siblings exists, or an empty array.
   */
  protected function getSiblingFields(CMDocumentInterface $cm_document): array {
    $add_on = [];
    $rows = $this->buildSiblingRows($cm_document);
    if (!empty($rows)) {
      $type = $cm_document->getDocumentedEntityParameter('type');
      $field = $cm_document->getDocumentedEntityParameter('field');
      // Adjust the count for display where the documented field was removed.
      $count = ($type === 'base') ? count($rows) : count($rows) + 1;
      $add_on['table'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Field Name'),
          $this->t('Entity Type'),
          $this->t('Bundle'),
          $this->t('Field Type'),
          $this->t('Documentation'),
          $this->t('Edit'),
        ],
        '#rows' => $rows,
        '#footer' => [["Total instances: $count", '', '', '', '', '']],
        '#empty' => $this->t('No table content found.'),
        '#caption' => $this->t("Sibling instances of field @field.", ['@field' => $field]),
        '#attributes' => [
          'class' => ['sortable'],
        ],

        '#attached' => ['library' => ['content_model_documentation/sortable-init']],
      ];
      $add_on['#weight'] = 20;
    }

    return $add_on;
  }

  /**
   * Builds the rows of all instances of a field.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   An array of table rows.
   */
  protected function buildSiblingRows(CMDocumentInterface $cm_document): array {
    $sibling_rows = [];
    $field = $cm_document->getDocumentedEntityParameter('field');
    $src_bundle = $cm_document->getDocumentedEntityParameter('bundle');
    $mapped_types = $cm_document->entityFieldManager->getFieldMap();
    foreach ($mapped_types as $entity_type => $fields) {
      if (!empty($fields[$field])) {
        foreach ($fields[$field]['bundles'] as $bundle) {
          // Do not include CM Documents in this list, or the current instance.
          if ($bundle !== 'cm_document' && $src_bundle !== $bundle) {
            $definitions = $cm_document->entityFieldManager->getFieldDefinitions($entity_type, $bundle);
            $sibling_rows[] = [
              'Field Name' => $definitions[$field]->getLabel(),
              'Entity Type' => $entity_type,
              'Bundle' => $bundle,
              'Field Type' => $fields[$field]['type'],
              'Document' => $this->getCmDocumentLink($entity_type, $bundle, $field),
              'Edit' => $edit_link = $this->getFieldEditLink($entity_type, $bundle, $field),
            ];
          }
        }
      }
    }

    return $sibling_rows;
  }

  /**
   * Gets a a table of field data for fields on a fieldable entity.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   A render array for a table if fields exist, or an empty array.
   */
  protected function getFieldsOnEntity(CMDocumentInterface $cm_document): array {
    $add_on = [];
    $rows = $this->buildFieldRows($cm_document);
    $count = count($rows);
    if (!empty($rows)) {
      $type = $cm_document->getDocumentedEntityParameter('type');
      $bundle = $cm_document->getDocumentedEntityParameter('bundle');
      $add_on['table'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Field Name'),
          $this->t('Field Machine Name'),
          $this->t('Field Type'),
          $this->t('Description'),
          $this->t('Documentation'),
          $this->t('Edit'),
        ],
        '#rows' => $rows,
        '#footer' => [["Total fields: $count", '', '', '', '', '']],
        '#empty' => $this->t('No table content found.'),
        '#caption' => $this->t("Fields that appear on @type @bundle", ['@type' => $type, '@bundle' => $bundle]),
        '#attributes' => [
          'class' => ['sortable'],
        ],

        '#attached' => ['library' => ['content_model_documentation/sortable-init']],
      ];
      $add_on['#weight'] = 20;
    }

    return $add_on;
  }

  /**
   * Builds the rows of all fields on a fieldable entity.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The current document to process.
   *
   * @return array
   *   An array of table rows.
   */
  protected function buildFieldRows(CMDocumentInterface $cm_document): array {
    $field_rows = [];
    $type = $cm_document->getDocumentedEntityParameter('type');
    $bundle = $cm_document->getDocumentedEntityParameter('bundle');
    $fields = $cm_document->entityFieldManager->getFieldDefinitions($type, $bundle);
    foreach ($fields as $machine => $value) {
      if (!$this->isField($machine)) {
        // It is not a field element, so bail out.
        continue;
      }
      $field_rows[] = [
        'field _name' => $value->getLabel(),
        'machine name' => $machine,
        'field_type' => $value->getType(),
        'description' => $value->getDescription(),
        'documentation' => $this->getCmDocumentLink($type, $bundle, $machine),
        'edit' => $this->getFieldEditLink($type, $bundle, $machine),
      ];
    }

    return $field_rows;

  }

}
