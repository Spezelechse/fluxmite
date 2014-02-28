<?php

/**
 * @file
 * Contains RulesPluginHandlerBase.
 */

namespace Drupal\fluxmite\Rules;

use Drupal\fluxservice\Rules\FluxRulesPluginHandlerBase;

/**
 * Base class for mite Rules plugin handler.
 */
abstract class RulesPluginHandlerBase extends FluxRulesPluginHandlerBase {

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

  /**
   * Returns info suiting for mite service account parameters.
   */
  public static function getServiceParameterInfo() {
    return array(
      'type' => 'fluxservice_account',
      'bundle' => 'fluxmite',
      'label' => t('Mite account'),
      'description' => t('The Mite account which this shall be executed.'),
    );
  }

}
