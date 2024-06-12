<?php

declare(strict_types=1);

namespace Drupal\schemadotorg;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a listing of Schema.org config entities.
 */
abstract class SchemaDotOrgConfigEntityListBuilderBase extends ConfigEntityListBuilder {

  /**
   * The current request.
   */
  protected Request $request;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->request = $container->get('request_stack')->getCurrentRequest();
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $row['operations'] = [
      'data' => $this->t('Operations'),
      'width' => '1%',
    ];
    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = [];

    // Details links.
    // @see \Drupal\Core\Render\Element\SystemCompactLink
    $details_toggle = $this->getDetailsToggle();

    $t_args = ['@type' => $this->storage->getEntityType()->getSingularLabel()];

    $title = $details_toggle
      ? $this->t('Hide details')
      : $this->t('Show details');
    $attributes_title = $details_toggle
      ? $this->t('Hide @type details', $t_args)
      : $this->t('Show @type details', $t_args);
    $url = Url::fromRoute('<current>', [], ['query' => ['details' => (int) !$details_toggle]]);

    $build['details_link'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['compact-link']],
      'link' => [
        '#type' => 'link',
        '#title' => $title,
        '#url' => $url,
        '#attributes' => [
          'title' => $attributes_title,
          'class' => [
            'action-link',
            'action-link--extrasmall',
            'action-link--icon-' . ($details_toggle ? 'hide' : 'show'),
          ],
        ],
      ],
    ];

    $build += parent::render();

    $build['table']['#sticky'] = TRUE;

    return $build;
  }

  /**
   * Get the current request details toggle state.
   *
   * @return bool
   *   The current request details toggle state.
   */
  protected function getDetailsToggle(): bool {
    return (boolean) ($this->request->query->get('details') ?? 0);
  }

  /**
   * Build items.
   *
   * @param array $items
   *   An indexed array.
   *
   * @return array
   *   A renderable array containing items.
   */
  protected function buildItems(array $items): array {
    return [
      'data' => [
        '#markup' => implode('<br/>', $items),
      ],
      'nowrap' => TRUE,
    ];
  }

  /**
   * Build a source to destination association.
   *
   * @param array|null $items
   *   An associative array with the source as the key and destination
   *   as the value.
   *
   * @return array
   *   A renderable array containing a source to destination association.
   */
  protected function buildAssociationItems(?array $items): array {
    if (!$items) {
      return [];
    }

    $data = [];
    foreach ($items as $source => $destination) {
      $prefix = $data ? '<br/>' : '';
      if ($destination) {
        $data[] = [
          'source' => ['#markup' => $source],
          'relationship' => ['#markup' => ' → '],
          'destination' => ['#markup' => (is_array($destination) ? implode(', ', $destination) : $destination)],
          '#prefix' => $prefix,
        ];
      }
      else {
        $data[] = [
          '#markup' => $source,
          '#prefix' => $prefix,
        ];
      }
    }
    return ['data' => $data, 'nowrap' => TRUE];
  }

  /**
   * Build key/value pairs.
   *
   * @param array|null $items
   *   An associative array.
   *
   * @return array
   *   A renderable array containing key/value pairs.
   */
  protected function buildKeyValuePairs(?array $items): array {
    if (!$items) {
      return [];
    }

    $data = [];
    foreach ($items as $source => $destination) {
      $data[] = [
        '#markup' => "$source: $destination",
        '#prefix' => '<br/>',
      ];
    }
    return ['data' => $data, 'nowrap' => TRUE];
  }

}
