<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_taxonomy;

use Drupal\taxonomy\VocabularyInterface;

/**
 * Taxonomy helper trait.
 */
trait SchemaDotOrgTaxonomyTrait {

  /**
   * Get a vocabulary, if it does not exist create it.
   *
   * @param string $vocabulary_id
   *   The vocabulary id.
   * @param array $settings
   *   The vocabulary settings.
   *
   * @return \Drupal\taxonomy\VocabularyInterface
   *   The vocabulary.
   */
  protected function createVocabulary(string $vocabulary_id, array $settings): VocabularyInterface {
    /** @var \Drupal\taxonomy\VocabularyStorageInterface $vocabulary_storage */
    $vocabulary_storage = $this->entityTypeManager->getStorage('taxonomy_vocabulary');
    /** @var \Drupal\taxonomy\VocabularyInterface|null $vocabulary */
    $vocabulary = $vocabulary_storage->load($vocabulary_id);
    if ($vocabulary) {
      return $vocabulary;
    }

    // Create the vocabulary.
    /** @var \Drupal\taxonomy\VocabularyInterface $vocabulary */
    $vocabulary = $vocabulary_storage->create([
      'vid' => $vocabulary_id,
      'name' => $settings['label'],
      'description' => $settings['description'] ?? '',
    ]);
    $vocabulary->save();

    // Enable translations for the vocabulary's taxonomy terms.
    if ($this->contentTranslationManager) {
      $this->contentTranslationManager->setEnabled('taxonomy_term', $vocabulary_id, TRUE);
    }

    $edit_link = $vocabulary->toLink($this->t('Edit'), 'edit-form')->toString();
    $this->messenger->addStatus($this->t('Created new vocabulary %name.', ['%name' => $vocabulary->label()]));
    $this->logger->get('taxonomy')->notice('Created new vocabulary %name.', ['%name' => $vocabulary->label(), 'link' => $edit_link]);
    return $vocabulary;
  }

}
