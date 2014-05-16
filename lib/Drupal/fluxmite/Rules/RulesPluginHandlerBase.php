<?php

/**
 * @file
 * Contains RulesPluginHandlerBase.
 */

namespace Drupal\fluxmite\Rules;

use Drupal\fluxservice_extension\Rules\FluxRulesPluginHandlerBaseExtended;

/**
 * Base class for mite Rules plugin handler.
 */
abstract class RulesPluginHandlerBase extends FluxRulesPluginHandlerBaseExtended {

  /**
   * Returns info-defaults for mite plugin handlers.
   */
  public static function getInfoDefaults() {
    return array(
      'category' => 'fluxmite',
      'access callback' => array(get_called_class(), 'integrationAccess'),
    );
  }

  /**
   * Rules mite integration access callback.
   */
  public static function integrationAccess($type, $name) {
    return fluxservice_access_by_plugin('fluxmite');
  }
}
