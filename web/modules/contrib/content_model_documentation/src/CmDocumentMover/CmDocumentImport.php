<?php

namespace Drupal\content_model_documentation\CmDocumentMover;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Utility\UpdateException;

/**
 * Public method for importing CM Documents.
 *
 * These are static methods so they can be used in hook_update_n and drush.
 */
class CmDocumentImport extends CmDocumentMoverBase {

  /**
   * Performs steps necessary to import CM Document items from export files.
   *
   * @param string|array $cm_document_paths
   *   The unique alias of the thing to import.
   * @param bool $strict
   *   Flag to indicate a hook_update should fail if an import fails.
   * @param bool $drush
   *   Flag to indicate if it is run by drush. Controls the kind of exception.
   *
   * @return string
   *   A summary of the successful import.
   */
  public static function import($cm_document_paths, bool $strict = TRUE, bool $drush = FALSE) {
    $completed = [];
    $cm_document_paths = (array) $cm_document_paths;
    $total_requested = count($cm_document_paths);
    $errors = [];
    try {
      self::canImport();
      foreach ($cm_document_paths as $key => $cm_document_path) {
        $filename = self::normalizeFileName($cm_document_path);
        $path = self::normalizePathName($cm_document_path);
        // If the file is there, process it.
        if (self::canReadFile($filename)) {
          $cm_document_import = self::readFileToData($filename);
          if (empty($cm_document_import)) {
            $errors[$cm_document_path] = "CM Document unable to read file data from '$filename'.";
          }
          else {
            try {
              $result = self::processOne($cm_document_import, $path);
            }
            catch (\Exception $e) {
              $errors[$cm_document_path] = $e->getMessage();
            }
          }

          // No Exceptions so far, so it must be a success.
          $completed[$path] = $result['operation'] ?? $errors[$cm_document_path];
        }
      }
      if (!empty($errors)) {
        $error_text = print_r($errors, TRUE);
        throw new \Exception("Errors found: $error_text");
      }
    }
    catch (\Exception $e) {
      $error = $e->getMessage();
      // Output a summary before shutting this down.
      $summary = self::getSummary($completed, $total_requested, 'Imported');
      \Drupal::logger('cm_document')->error($summary);
      if ($strict) {
        if ($drush) {
          throw new \Exception("Exception: Import aborted! \n $summary");
        }
        else {
          // There were problems with a hook_update_n() call it a fail.
          throw new UpdateException("Update aborted! \n $summary");
        }
      }
    }
    // Made it this far, it is a success.
    $summary = self::getSummary($completed, $total_requested, 'Imported CM Documents');
    \Drupal::logger('cm_document')->info($summary);
    return $summary;
  }

  /**
   * Validated Updates/Imports one page from the contents of an import file.
   *
   * @param array $cm_document_import
   *   The cm_document data from the file to import.
   * @param string $alias
   *   The alias of the cm_document to import/update.
   *
   * @return array
   *   Contains the elements page, operation, and edit_link.
   *
   * @throws \Exception
   *   In the event of something that fails the import.
   */
  protected static function processOne(array $cm_document_import, string $alias) {
    $msg_vars = [];
    // Determine if a cm_document exists at that alias.
    $language = (!empty($cm_document_import['language'])) ? $cm_document_import['language'] : Language::LANGCODE_NOT_SPECIFIED;
    $cm_document_existing = self::getCmDocumentFromAlias($alias, $language);
    $initial_vid = FALSE;

    if (!empty($cm_document_existing)) {
      // A CM Document already exists at this alias. Update it.
      $operation = t('Updated');
      $saved_cm_document = self::updateExistingCmDocument($cm_document_existing, $cm_document_import);
    }
    else {
      // No CM Document exists at this path, Check to see if the path is in use.
      $exists = self::aliasExists($alias, $language);
      if ($exists) {
        // The path exists, but not a cm_document.  Log and throw exception.
        $vars = [
          '!alias' => $alias,
          '!language' => $language,
        ];
        $message = t('The alias belongs to something that is not a cm_document.  Import of !language: !alias cancelled.', $vars);
        throw new \Exception($message);
      }

      // Create one.
      $operation = t('Created');
      $saved_cm_document = self::createNewCmDocument($cm_document_import);
    }
    $msg_vars['@operation'] = $operation;

    // Begin validation.
    // Case race.  First to evaluate TRUE wins.
    switch (TRUE) {
      case (empty($saved_cm_document->id())):
        // Save did not complete.  No id granted.
        $message = t('@operation of @language: @path failed: The imported cm_document did not save.', $msg_vars);
        $valid = FALSE;
        break;

      // @todo Consider other cm_document properties that should be validated
      // without leading to false negatives.
      // Perhaps making sure the two documentation_for fields match.
      default:
        // Passed all the validations, likely it is valid.
        $valid = TRUE;

    }

    if (!$valid) {
      throw new \Exception($message);
    }

    $return = [
      'cm_document' => $saved_cm_document,
      'operation' => "{$operation}: cm_document/{$saved_cm_document->id()}",
      'edit_link' => "admin/structure/cm_document/{$saved_cm_document->id()}/edit",
    ];

    return $return;
  }

  /**
   * Create a cm_document from the imported object.
   *
   * @param array $cm_document_data
   *   The cm_document array of data from the import file.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   The resulting cm_document from cm_document_save.
   */
  protected static function createNewCmDocument($cm_document_data): CMDocumentInterface {
    // Blank out the revision id (vid) can differ by environment.
    $cm_document_data['fields']['vid'] = '';
    $cm_document = self::getStorage()->create();
    $cm_document = self::addFieldData($cm_document, $cm_document_data);
    $cm_document->enforceIsNew();
    $cm_document->save();
    $vars = [
      '@name' => $cm_document->getName(),
      '@id' => $cm_document->id(),
    ];
    \Drupal::logger('cm_document')->info('Created "@name" (id = @id) by yml import.', $vars);
    return $cm_document;
  }

  /**
   * Adds field level data to the cm document entity.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The cm document entity.
   * @param array $cm_document_data
   *   The array of field data from the imported file.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   The updated cm_document with the new data added but not saved.
   */
  protected static function addFieldData(CMDocumentInterface $cm_document, array $cm_document_data): CMDocumentInterface {
    if ($cm_document instanceof FieldableEntityInterface) {
      foreach ($cm_document_data['fields'] as $name => $value) {
        if (is_array($value)) {
          foreach ($value as $key => $item) {
            if (str_starts_with($key, '_')) {
              unset($value[$key]);
            }
          }
        }
        $cm_document->set($name, $value);
      }
    }
    return $cm_document;
  }

  /**
   * Verifies that the import can be used based on an import directory.
   *
   * @return bool
   *   TRUE If the import can be run.
   *
   * @throws \DrupalUpdateException
   *   If import can not be run.
   */
  public static function canImport() {
    $storage_path = self::getStoragePath();
    if (empty($storage_path)) {
      throw new \Exception('The storage location is either not set, or can not be found.');
    }
    $file_uri = DRUPAL_ROOT . '/' . $storage_path;
    if (!is_dir($file_uri)) {
      throw new \Exception("The storage directory '$file_uri' was not found.");
    }
    return TRUE;
  }

  /**
   * Update an existing cm_document from the imported object.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The pre-exisiting cm_document object that exists in Drupal.
   * @param array $cm_document_data
   *   The cm_document data from the import that needs to be added.
   *
   * @throws \Exception
   *   If the save/import could not be performed.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface
   *   The resulting cm_document from cm_document_save.
   */
  protected static function updateExistingCmDocument(CMDocumentInterface $cm_document, array $cm_document_data): CMDocumentInterface {
    // Check timestamps and if the current is higher than the import,
    // bail out.  /// this maybe should return an error.
    if ((int) $cm_document->get('changed')->value > (int) $cm_document_data['fields']['changed']) {
      $msg = t('The revision being imported is older than the current revision. Skipping import.');
      $vars = [
        '@name' => $cm_document->getName(),
        '@id' => $cm_document->id(),
      ];
      \Drupal::logger('cm_document')->warning('Import failed "@name" (id = @id) was not imported because the current revision is newer than the export.', $vars);
      throw new \Exception($msg);
    }
    // Blank this out because revision id (vid) can differ by environment.
    $cm_document_data["fields"]["vid"] = '';
    $cm_document = self::addFieldData($cm_document, $cm_document_data);
    $cm_document->setRevisionCreationTime(\Drupal::time()->getCurrentTime());
    $cm_document->setNewRevision(TRUE);
    $cm_document->setSyncing(TRUE);
    $cm_document->save();
    $vars = [
      '@name' => $cm_document->getName(),
      '@id' => $cm_document->id(),
    ];
    \Drupal::logger('cm_document')->info('Updated "@name" (id = @id) by yml import.', $vars);
    return $cm_document;
  }

  /**
   * Checks to see if a storagefile can be read.
   *
   * @param string $filename
   *   The filename of the file.
   *
   * @return bool
   *   TRUE if the file can be read.
   *
   * @throws \Exception
   *   When the file can not be read.
   */
  protected static function canReadFile($filename) {
    $path = self::getStoragePath();
    $file = "{$path}{$filename}";
    if (file_exists($file)) {
      // The file is present.
      return TRUE;
    }
    else {
      // The file is not there.
      $vars = [
        '@path' => $path,
        '@filename' => $filename,
        '@storage' => 'cm_document',
      ];
      $message = t("The requested @storage read failed because the file '@filename' was not found in '@path'. \nRe-run update when the file has been placed there and is readable.", $vars);
      throw new \Exception($message);
    }
  }

  /**
   * Read the contents of a file into a string for the entire contents.
   *
   * @param string $filename
   *   The filename of the file.
   *
   * @return array|null
   *   The contents of the file read.
   */
  protected static function readFileToData($filename): array|null {
    $path = self::getStoragePath();
    $file = "{$path}{$filename}";
    if (self::canReadFile($filename)) {
      // Get the contents as one string.
      $file_contents = self::readYml($file);
    }
    else {
      // Should not reach here due to exception from canReadFile.
      $file_contents = NULL;
    }
    return $file_contents;
  }

  /**
   * Get the yml file and convert it to an array.
   *
   * @param string $path
   *   The path with filename of the yml file.
   *
   * @return array
   *   The array of content assembled from the yml file.
   */
  protected static function readYml($path): array {
    $content = Yaml::decode(file_get_contents($path));
    $export_date_time = date('n/j/Y g:i A', $content['fields']['revision_created']);
    // Append a message to the log indicating it was imported.
    if (is_array($content['fields']['revision_log_message'])) {
      // This appears as an empty array when empty.
      $content['fields']['revision_log_message'] = "Imported from yml file dated $export_date_time.";
    }
    else {
      $content['fields']['revision_log_message'] = "{$content['fields']['revision_log_message']}  Imported from yml file dated $export_date_time.";

    }
    // Use the time of this import for the revision time, otherwise we get
    // a revision that could be timed earlier than an actual edit.
    return $content;
  }

  /**
   * Loads a cm_document from a path.
   *
   * @param string $alias
   *   The alias of the cm_document to import/update.
   * @param string $language
   *   The language of the alias to look up (cm_document->language).
   *
   * @return mixed
   *   (object) CM Document from that path.
   *   (bool) FALSE if there is no cm_document to load from that alias.
   */
  protected static function getCmDocumentFromAlias($alias, $language) {
    $cm_document = FALSE;
    $alias = trim($alias);
    $alias = trim($alias, '/');
    $alias = self::removeBaseDirectory($alias);
    // Put the slash back because all aliases start with it.
    $alias = "/{$alias}";
    $id = self::getIdByAlias($alias);
    if ($id) {
      $cm_document = self::loadCmDocument($id);
    }
    return $cm_document;
  }

  /**
   * Lookup the id of a CM Document by an alias.
   *
   * @param string $alias
   *   The alias to lookup /blah/some/path.
   * @param string $language
   *   The language of the alias.
   *
   * @return int|null
   *   The id of a matching CM Document, NULL otherwise.
   */
  protected static function getIdByAlias(string $alias, $language = Language::LANGCODE_NOT_SPECIFIED): int|null {
    $defined_alias = \Drupal::service('path_alias.repository')->lookupByAlias($alias, 'en');
    if (!empty($defined_alias['path'])) {
      // There is something here, not sure if it is CM Document yet.
      // Path looks like "/admin/structure/cm_document/<id>".
      $id = str_replace('/admin/structure/cm_document/', '', $defined_alias['path']);
      if (is_numeric($id)) {
        return $id;
      }
    }
    // Made it this far, means nothing was found.
    return NULL;
  }

  /**
   * Check if path exists in drupal.
   *
   * @param string $alias
   *   The alias of the cm_document to import/update.
   * @param string $language
   *   The language of the alias to look up.
   *
   * @return bool
   *   TRUE if the alias exists.
   *   FALSE if the alias does not exist.
   */
  protected static function aliasExists($alias, string $language = Language::LANGCODE_NOT_SPECIFIED): bool {
    $exists = FALSE;
    $defined_alias = \Drupal::service('path_alias.repository')->lookupByAlias($alias, $language);
    if (!empty($defined_alias['path'])) {
      $exists = TRUE;
    }
    return $exists;
  }

  /**
   * Generate the import summary.
   *
   * @param array $completed
   *   Array of completed imports.
   * @param int $total_requested
   *   The number to be processed.
   * @param string $operation
   *   The name of the operation.
   *
   * @return string
   *   The report of what was completed.
   */
  protected static function getSummary($completed, $total_requested, $operation) {
    $count = count($completed);
    $completed_string = print_r($completed, TRUE);
    $remove = ["Array", "(\n", ")\n"];
    $completed_string = str_replace($remove, '', $completed_string);
    // Adjust for misaligned second line.
    $completed_string = str_replace('             [', '     [', $completed_string);
    $completed_string = str_replace(' =>', ': ', $completed_string);
    $vars = [
      '@count' => $count,
      '@completed' => $completed_string,
      '@total' => $total_requested,
      '@operation' => $operation,
    ];

    return t("Summary: @operation @count/@total.  Completed the following:\n @completed", $vars);
  }

}
