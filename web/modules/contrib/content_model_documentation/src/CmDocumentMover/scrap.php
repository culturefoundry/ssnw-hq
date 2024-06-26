  /**
   * Rolls back a revision or cm_document creation.
   *
   * @param string $op
   *   The crud op that was performed.
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   The cm_document object to be rolled back.
   * @param int $rollback_to_vid
   *   The revision id to roll back to.
   */
  protected static function rollbackImport($op, $cm_document, $rollback_to_vid) {
    if ($op === 'create') {
      // Op was a create, so delete the cm_document if there was one created.
      if (!empty($cm_document->nid)) {
        // The presence of nid indicates one was created, so delete it.
        cm_document_delete($cm_document->nid);
        $msg = "CM Document @nid created but failed validation and was deleted.";
        $variables = array(
          '@nid' => $cm_document->nid,
        );
        Message::make($msg, $variables, WATCHDOG_INFO, 1);
      }
    }
    else {
      // Op was an update, so just delete the revision.
      $revision_list = node_revision_list($cm_document);
      $revision_id_to_rollback = $cm_document->vid;
      unset($revision_list[$revision_id_to_rollback]);
      if (count($revision_list) > 0) {
        $last_revision = max(array_keys($revision_list));
        $cm_document_last_revision = node_load($cm_document->id, $rollback_to_vid);
        node_save($cm_document_last_revision);
        node_revision_delete($revision_id_to_rollback);
        $msg = "CM Document @nid updated but failed validation, Revision @deleted deleted and rolled back to revision @rolled_to.";
        $variables = array(
          '@nid' => $cm_document->nid,
          '@deleted' => $revision_id_to_rollback,
          '@rolled_to' => $rollback_to_vid,
        );
        Message::make($msg, $variables, WATCHDOG_INFO, 1);
      }
    }
  }
