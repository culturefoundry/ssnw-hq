<?php

declare(strict_types=1);

namespace Drupal\content_model_documentation\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for CMDocument entities.
 */
class CMDocumentViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData(): array {
    $data = parent::getViewsData();
    // @todo This entire class might not be needed.
    return $data;
  }

}
