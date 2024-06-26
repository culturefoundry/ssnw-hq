<?php

namespace Drupal\project_browser;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Plugin\CachedDiscoveryClearerInterface;
use Drupal\Core\Recipe\RecipeAppliedEvent;
use Drupal\Core\State\StateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RecipeTracker implements EventSubscriberInterface {

  public const STATE_KEY = 'project_browser.applied_recipes';

  public function __construct(
    private readonly StateInterface $state,
    private readonly TimeInterface $time,
    private readonly CachedDiscoveryClearerInterface $cachedDiscoveryClearer,
  ) {}

  /**
   * Reacts when a recipe has been applied.
   *
   * @param \Drupal\Core\Recipe\RecipeAppliedEvent $event
   *   The recipe that was applied.
   */
  public function onApplyRecipe(RecipeAppliedEvent $event): void {
    // This should be done by the recipe system, but it's okay to polyfill it
    // for now.
    $this->cachedDiscoveryClearer->clearCachedDefinitions();

    $list = $this->state->get(static::STATE_KEY, []);
    $list[] = [
      realpath($event->recipe->path),
      $this->time->getRequestTime(),
    ];
    $this->state->set(static::STATE_KEY, $list);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      RecipeAppliedEvent::class => 'onApplyRecipe',
    ];
  }

}
