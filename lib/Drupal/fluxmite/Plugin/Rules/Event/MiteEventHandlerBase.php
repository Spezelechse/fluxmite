<?php

/**
 * @file
 * Contains MiteEventHandlerBase.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

use Drupal\fluxservice_extension\Plugin\Rules\Event\FluxserviceEventHandlerBase;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;


/**
 * Cron-based base class for Mite event handlers.
 */
abstract class MiteEventHandlerBase extends FluxserviceEventHandlerBase {

  /**
   * Returns info-defaults for plugin handlers.
   */
  public static function getInfoDefaults() {
    return RulesPluginHandlerBase::getInfoDefaults();
  }

  /**
   * Rules trello integration access callback.
   */
  public static function integrationAccess($type, $name) {
    return fluxservice_access_by_plugin('fluxtrello');
  }
}
