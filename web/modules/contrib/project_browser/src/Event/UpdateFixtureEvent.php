<?php

namespace Drupal\project_browser\Event;

use Drupal\project_browser\EnabledSourceHandler;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Subclass of Event for updating fixtures.
 */
class UpdateFixtureEvent extends Event {

  /**
   * Constructor for the Update Fixture Event.
   *
   * @param \Drupal\project_browser\EnabledSourceHandler $enabledSource
   *   The enabled project browser source.
   */
  public function __construct(
    public EnabledSourceHandler $enabledSource,
  ) {}

}
