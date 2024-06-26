<?php

namespace Drupal\content_model_documentation\Drush\Commands;

use Drupal\content_model_documentation\CmDocumentMover\CmDocumentExport;
use Drupal\content_model_documentation\CmDocumentMover\CmDocumentImport;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 */
final class ContentModelDocumentationCommands extends DrushCommands {

  /**
   * Constructs a ContentModelDocumentationCommands object.
   */
  public function __construct(
    private readonly Token $token,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('token'),
    );
  }

    /**
   * Exports the identified Content Model Document.
   *
   * @param int $id
   *   The cm document id to export.
   */
  #[CLI\Command(name: 'content-model-documentation:export', aliases: ['cm-doc-export'])]
  #[CLI\Argument(name: 'id', description: 'The cm document id to export.')]
  #[CLI\Usage(name: 'drush content-model-documentation:export 123', description: 'Export Content Model Document id 123 to the export directory.')]
  public function export(int $id) {
    try {
      // Check that we have what it needs to export.
      CmDocumentExport::canExport();
      // It can export so can safely proceed.
      $msg = dt('Exported CM Document: ') . "[$id] => " . CmDocumentExport::export($id);
    }
    catch (\Exception $e) {
      $vars = [
        '@error' => $e->getMessage(),
      ];
      $msg = dt("Content Model Document export exception:  @error", $vars);
    }

    return $msg;
  }

  /**
   * Imports the identified Content Model Document.
   *
   * @param string $alias
   *   The alias of the Content Model Document to import.
   */
  #[CLI\Command(name: 'content-model-documentation:import', aliases: ['cm-doc-import'])]
  #[CLI\Argument(name: 'alias', description: 'the/path/of/the/document')]
  #[CLI\Usage(name: 'drush content-model-documentation:export 123', description: 'Import the Content Model Document that would have this alias.')]
  public function import(string $alias) {
    try {
      // Check that we have what it needs to export.
      CmDocumentImport::canImport();
      // It can import so can safely proceed.
      $msg = dt('Import CM Document: ') . CmDocumentImport::import($alias, TRUE, TRUE);
    }
    catch (\Exception $e) {
      $vars = [
        '@error' => $e->getMessage(),
      ];

      $msg = dt("Content Model Document import exception:  @error", $vars);
    }

    return $msg;
  }

}
