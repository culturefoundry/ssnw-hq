<?php

namespace Drupal\content_model_documentation\CmDocumentMover;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Serialization\Yaml;

/**
 * Public methods for importing CM Documents.
 *
 * These are static methods so they can be used in hook_update_n and drush.
 */
class CmDocumentExport extends CmDocumentMoverBase {

  /**
   * Exports a single CM Document based on its id. (Typically called by Drush).
   *
   * @param string $id
   *   The nid of the cm_document to export.
   *
   * @return string
   *   The URI of the item exported, or a failure message.
   */
  public static function export($id) {
    try {
      self::notEmpty('id', $id);
      self::isNumeric('id', $id);
      self::canExport();
      $msg_return = '';

      // Load the cm_document if it exists.
      $cm_document = self::loadCmDocument($id, TRUE);
      self::notEmpty('cm_document', $cm_document);
      $storage_path = self::getStoragePath();
      $cm_document_path = $cm_document->toUrl()->toString();
      self::notEmpty('cm_document alias', $cm_document_path);
      $cm_document_path = self::normalizePathName($cm_document_path);
      $file_name = self::normalizeFileName($cm_document_path);
      $file_uri = DRUPAL_ROOT . '/' . $storage_path;

      // Made it this far, it exists, so export it.
      $yaml_content = self::packageCmDocument($cm_document);

      // Save the file.
      $msg_return = self::writeFile($file_uri, $file_name, $yaml_content);

    }
    catch (\Exception $e) {
      // Any errors from this command do not need to be watchdog logged.
      $error_text = $e->getMessage();
      $msg_error = "Exception: $error_text";
    }

    return $msg_return ?? $msg_error;
  }

  /**
   * Checks to see if cm_documents can be exported.
   *
   * @return bool
   *   TRUE if can be exported.
   */
  public static function canExport() {
    if (!empty(self::getStoragePath())) {
      return TRUE;
    }
    else {
      $message = "CM Document export to a file requires the local module path to be set and exist. Check the setting /admin/config/system/cm_document";
      throw new \Exception($message);
    }
  }

  /**
   * Writes the export file.
   *
   * @param string $directory
   *   The directory where the export should be saved.
   * @param string $filename
   *   The file name of the export file.
   * @param string $content
   *   The content to write to the file.
   *
   * @throws \Exception
   *   If the file directory is unavailable or the file was not created.
   *
   * @return string
   *   The filename if the creation was successful.
   */
  protected static function writeFile(string $directory, string $filename, string $content) {
    $fileSystem = \Drupal::service('file_system');
    if (!is_dir($directory)) {
      $created_directory = $fileSystem->mkdir($directory);
      if ($created_directory === FALSE) {
        throw new \Exception("Failed to create directory $directory");
      }
    }
    if (file_put_contents($directory . $filename, $content) === FALSE) {
      throw new \Exception("Failed to write file $filename");
    }
    // Made it this far so the file must have been created.
    return $filename;
  }

  /**
   * Creates the YML representation of the cm_document.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The cm document being exported.
   *
   * @return string
   *   The yml output created from the cm_document.
   */
  protected static function packageCmDocument(CMDocumentInterface $cm_document): string {
    $export_values = [
      'uuid' => $cm_document->uuid(),
      'langcode' => $cm_document->language()->getId(),
      'type' => $cm_document->getEntityTypeId(),
      'bundle' => $cm_document->bundle(),
      'id' => $cm_document->id(),
    ];

    $export_values['dependencies'] = [
      'config' => [
        sprintf('%s.%s_type.%s', $cm_document->getEntityTypeId(), $cm_document->getEntityTypeId(), $cm_document->bundle()),
      ],
    ];

    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
    $field_manager = \Drupal::service('entity_field.manager');
    $fields = $field_manager->getFieldDefinitions($cm_document->getEntityTypeId(), $cm_document->bundle());
    $entity_references = [];
    foreach ($fields as $field) {
      if (isset($export_values[$field->getName()])) {
        continue;
      }

      if (!$field->getFieldStorageDefinition()->isBaseField()) {
        $export_values['dependencies']['config'][] = 'field.field.' . $field->getConfig($cm_document->bundle())->getOriginalId();
      }
      $field_values = $cm_document->get($field->getName())->getValue();

      foreach ($field_values as $delta => $item) {
        if (empty($item['target_id'])) {
          continue;
        }

        $referenced_entity_type = $field->getFieldStorageDefinition()->getPropertyDefinition('entity')->getConstraint('EntityType');
        $referenced_entity = self::getStorage($referenced_entity_type)->load($item['target_id']);

        if ($referenced_entity instanceof EntityInterface) {
          $field_values[$delta]['_entity'] = $referenced_entity->getConfigDependencyName();
        }

        $entity_references[] = $referenced_entity->getConfigDependencyName();
      }

      if ($field->getConfig($cm_document->bundle())->getFieldStorageDefinition()->getCardinality() === 1 && count($field_values)) {
        $field_values = array_shift($field_values);
      }

      if (count($field_values) === 1 && isset($field_values['value'])) {
        $field_values = $field_values['value'];
      }
      $export_values['fields'][$field->getName()] = $field_values;
    }
    $export_values['dependencies']['entity'] = array_unique($entity_references);
    $export_content = Yaml::encode($export_values);
    return $export_content;
  }

}
