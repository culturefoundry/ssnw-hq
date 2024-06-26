<?php

namespace Drupal\content_model_documentation\CmDocumentMover;

use Drupal\content_model_documentation\Entity\CMDocumentInterface;

/**
 * Base class for common methods for importing and exporting CM Documents.
 *
 * These are static methods so they can be used in hook_update_n and drush.
 */
class CmDocumentMoverBase {

  /**
   * Normalizes a path to have slashes and removes file appendage.
   *
   * @param string $quasi_path
   *   A path or a export file name to be normalized.
   *
   * @return string
   *   A string resembling a machine name with underscores.
   */
  protected static function normalizePathName($quasi_path) {
    $items = [
      // Remove file extension.
      '.txt' => '',
      // Convert slug back to directory slash.
      'zZz' => '/',
    ];
    $path = str_replace(array_keys($items), array_values($items), $quasi_path);

    return $path;
  }

  /**
   * Normalizes a machine  or file name to be the filename.
   *
   * @param string $quasi_name
   *   An machine name or a export file name to be normalized.
   *
   * @return string
   *   A string resembling a filename with hyphens and -export.txt.
   */
  protected static function normalizeFileName($quasi_name) {
    $quasi_name = trim($quasi_name);
    $quasi_name = trim($quasi_name, '/');
    $quasi_name = self::removeBaseDirectory($quasi_name);
    $items = [
      '.yml' => '',
      '_' => '-',
      // Replaceable token for directories.
      '/' => 'zZz',
    ];
    $file_name = str_replace(array_keys($items), array_values($items), $quasi_name);
    $file_name = "{$file_name}.yml";
    return $file_name;
  }

  /**
   * Removes the Drupal subdirectory(s) from the path if they exist.
   *
   * @param string $quasi_path
   *   A path and or filename to remove.
   *
   * @return string
   *   The path or filename with the Drupal subdirectory removed.
   */
  protected static function removeBaseDirectory(string $quasi_path): string {
    global $base_url;
    $quasi_path = trim($quasi_path, '/');
    $drupal_subdirectories = parse_url($base_url, PHP_URL_PATH);
    $drupal_subdirectories = trim($drupal_subdirectories, '/');
    $drupal_directory_parts = explode( '/', (string) $drupal_subdirectories);
    if (!empty($drupal_directory_parts)) {
      $path_parts = explode('/', $quasi_path);
      foreach ($drupal_directory_parts as $index => $directory_part) {
        if ($drupal_directory_parts[$index] === $path_parts[0]) {
          // This is part of the subdirectory the site is part of so remove it.
          // Had to reference index 0 instead of $index, because after the
          // first unset() the index will no longer match and the next thing
          // to remove is now the first thing.
          unset($path_parts[0]);
        }
      }
      $quasi_path = implode('/', $path_parts);
    }
    return $quasi_path;
  }

  /**
   * Gets the config based location to where the import export files live.
   *
   * @return string
   *   The location where cm documents will get exported or imported from.
   */
  protected static function getStoragePath(): string {
    $module = \Drupal::config('content_model_documentation.settings')->get('export_location');
    $path = (empty($module)) ? '' : \Drupal::service('extension.list.module')->getPath($module);
    return (empty($path)) ? '' : "$path/cm_documents/";
  }

  /**
   * A strict check for !empty.  Fails update if $value is empty.
   *
   * @param string $name
   *   The name of a variable being checked for empty.
   * @param mixed $value
   *   The actual value of the variable being checked for empty.
   *
   * @return bool
   *   TRUE if $value is not empty.
   *
   * @throws \Exception
   *   If it is empty.
   */
  protected static function notEmpty($name, $value) {
    if (!empty($value)) {
      $return = TRUE;
    }
    else {
      // This is strict, so throw Exception.
      $vars = ['@name' => $name];
      $message = t("The required @name was empty. Could not proceed.", $vars);
      throw new \Exception($message);
    }

    return $return;
  }

  /**
   * A strict check for numeric.  Fails update if $value is !numeric.
   *
   * @param string $name
   *   The name of a variable being checked for empty.
   * @param mixed $value
   *   The actual value of the variable being checked for empty.
   *
   * @return bool
   *   TRUE if $value is numeric.
   *
   * @throws \Exception
   *   If it is !numeric.
   */
  protected static function isNumeric($name, $value) {
    if (is_numeric($value)) {
      $return = TRUE;
    }
    else {
      // This is strict, so make message and throw DrupalUpdateException.
      $message = "The value $name was not numeric. Could not proceed.";
      throw new \Exception($message);
    }

    return $return;
  }

  /**
   * Loads a cm_document if it exists.
   *
   * @param int $id
   *   The cm_document id to load.
   * @param bool $strict
   *   Flag to indicate the it should throw exception if not found.
   *
   * @return \Drupal\content_model_documentation\Entity\CMDocumentInterface|null
   *   The cm_document matching the id. Or null if not found.
   */
  protected static function loadCmDocument(int $id, bool $strict = FALSE): CMDocumentInterface|null {
    $storage = self::getStorage();
    $cm_document = $storage->load($id);
    if (empty($cm_document) & $strict) {
      throw new \Exception("A content model document with the id of '$id' was not found.");
    }
    return $cm_document;
  }

  /**
   * Get the storage interface.
   *
   * @param string $type
   *   The type of storage to return.  Default to cm_document.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The storage interface for that type.
   */
  protected static function getStorage($type = 'cm_document') {
    /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
    $storage = \Drupal::service('entity_type.manager')->getStorage($type);
    return $storage;
  }

}
