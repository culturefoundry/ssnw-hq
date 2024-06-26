<?php

namespace Drupal\project_browser\EventSubscriber;

use Drupal\project_browser\Event\ProjectBrowserEvents;
use Drupal\project_browser\Event\UpdateFixtureEvent;
use Drupal\project_browser\ProjectBrowserFixtureHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Update Fixture event subscriber.
 */
class UpdateFixtureSubscriber implements EventSubscriberInterface {

  /**
   * Constructor for update fixture event subscriber.
   *
   * @param \Drupal\project_browser\ProjectBrowserFixtureHelper $fixtureHelper
   *   The fixture helper.
   */
  public function __construct(
    private readonly ProjectBrowserFixtureHelper $fixtureHelper,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      ProjectBrowserEvents::UPDATE_FIXTURE => 'onFixtureUpdate',
    ];
  }

  /**
   * Update fixture only if plugin id is 'drupalorg_mockapi'.
   *
   * @param \Drupal\project_browser\Event\UpdateFixtureEvent $event
   *   The event.
   */
  public function onFixtureUpdate(UpdateFixtureEvent $event) {
    $current_sources = $event->enabledSource->getCurrentSources();
    if (!empty($current_sources['drupalorg_mockapi'])) {
      $this->fixtureHelper->updateMostRecentChanges();
    }
  }

}
