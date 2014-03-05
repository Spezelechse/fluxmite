<?php

/**
 * @file
 * Contains LogData.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * Send a tweet action.
 */
class LogData extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_logdata',
      'label' => t('LogData'),
      'parameter' => array(
        'customer' => array(
          'type' => 'fluxmite_customer',
          'label' => t('Mite: Customer'),
          'wrapped' => TRUE,
        ),
        'account' => static::getServiceParameterInfo(),
        'change_type' => array(
          'type' => 'text',
          'label' => t('Change type'),
          'required' => FALSE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute(\EntityDrupalWrapper $customer, MiteAccountInterface $account, $change_type="") {

    $info=$customer->value()->getInfo();
    $controller = entity_get_controller($info['name']);
    
    $change_type=$change_type."Local";
    $controller->$change_type($customer->value());

    watchdog("log_customer", $customer->value()->getValueOf('name')." @".$account->label." (".$change_type.")");
  }
}
