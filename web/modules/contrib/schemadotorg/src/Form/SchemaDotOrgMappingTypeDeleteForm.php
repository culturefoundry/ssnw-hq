<?php

declare(strict_types=1);

namespace Drupal\schemadotorg\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for deleting a Schema.org mapping type.
 *
 * @see \Drupal\node\Form\NodeTypeDeleteConfirm
 */
class SchemaDotOrgMappingTypeDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingTypeInterface|null $mapping_type */
    $mapping_type = $this->getEntity();
    $target_entity_type_id = $mapping_type->id();
    $num_mappings = $this->entityTypeManager->getStorage('schemadotorg_mapping')
      ->getQuery()
      ->accessCheck()
      ->condition('target_entity_type_id', $target_entity_type_id)
      ->count()
      ->execute();
    if ($num_mappings) {
      $t_args = ['%type' => $mapping_type->label()];
      $form['description']['#markup'] = $this->formatPlural(
          $num_mappings,
          'The %type Schema.org mapping type is used by 1 Schema.org mapping on your site.',
          'The %type Schema.org mapping type is used by @count Schema.org mappings on your site.',
          $t_args
        )
        . ' '
        . $this->t('You can not remove this Schema.org mapping type until you have removed all of the %type Schema.org mappings.', $t_args);
      $form['actions']['#access'] = FALSE;
    }
    return $form;
  }

}
