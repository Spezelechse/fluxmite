<?php

/**
 * @file
 * Contains printValue.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * create date struct.
 */
class printValue extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_print_value',
      'label' => t('Print value'),
      'parameter' => array(
        'value' => array(
          'type' => 'unknown',
          'label' => t('Value'),
          'required' => TRUE,
        )
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($value) {
    print_r($value);
  }
}
