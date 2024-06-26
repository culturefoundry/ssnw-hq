<?php

namespace Drupal\content_model_documentation\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\content_model_documentation\Entity\CMDocumentInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContentModelDocumentController.
 *
 *  Returns responses for Content Model Document routes.
 */
class ContentModelDocumentController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new ContentModelDocumentationController.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(DateFormatterInterface $date_formatter, RendererInterface $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): ContentModelDocumentController {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a CMDocument revision.
   *
   * @param int $cm_document_revision
   *   The CMDocument revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function revisionShow(int $cm_document_revision): array {
    $cm_document = $this->entityTypeManager()->getStorage('cm_document')
      ->loadRevision($cm_document_revision);
    return $this
      ->entityTypeManager()
      ->getViewBuilder('cm_document')
      ->view($cm_document);
  }

  /**
   * Page title callback for a CMDocument revision.
   *
   * @param int $cm_document_revision
   *   The CMDocument revision ID.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function revisionPageTitle(int $cm_document_revision): TranslatableMarkup {
    $cm_document = $this->entityTypeManager()->getStorage('cm_document')
      ->loadRevision($cm_document_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $cm_document->label(),
      '%date' => $this->dateFormatter->format($cm_document->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a CMDocument.
   *
   * @param \Drupal\content_model_documentation\Entity\CMDocumentInterface $cm_document
   *   A CMDocument object.
   *
   * @return array
   *   An array as expected by drupal_render().
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function revisionOverview(CMDocumentInterface $cm_document): array {
    $account = $this->currentUser();
    $cm_document_storage = $this->entityTypeManager()->getStorage('cm_document');

    $langcode = $cm_document->language()->getId();
    $langname = $cm_document->language()->getName();
    $languages = $cm_document->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $tranlation_text = $this->t('@langname revisions for %title', [
      '@langname' => $langname,
      '%title' => $cm_document->label(),
    ]);
    $no_translation_text = $this->t('Revisions for %title', ['%title' => $cm_document->label()]);
    $build['#title'] = $has_translations ? $tranlation_text : $no_translation_text;

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("manage all content model document revisions") || $account->hasPermission('administer content model document entities')));
    $delete_permission = (($account->hasPermission("manage all content model document revisions") || $account->hasPermission('administer content model document entities')));

    $rows = [];

    $vids = array_column($cm_document_storage->getAggregateQuery()
      ->allRevisions()
      ->condition('id', $cm_document->id())
      ->groupBy('vid')
      ->accessCheck(TRUE)
      ->execute(), 'vid');

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\content_model_documentation\Entity\CMDocumentInterface $revision */
      $revision = $cm_document_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode)) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => Link::fromTextAndUrl(
                $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short'),
                new Url('entity.cm_document.revision', [
                  'cm_document' => $cm_document->id(),
                  'cm_document_revision' => $vid,
                ])
              )->toString(),
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          unset($current);
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.cm_document.translation_revert', [
                'cm_document' => $cm_document->id(),
                'cm_document_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.cm_document.revision_revert', [
                'cm_document' => $cm_document->id(),
                'cm_document_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.cm_document.revision_delete', [
                'cm_document' => $cm_document->id(),
                'cm_document_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['cm_document_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
