<?php

/**
 * @file
 * Contains syncLocalCustomer.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * Send a customer action.
 */
class syncLocalCustomer extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_sync_customer',
      'label' => t('syncLocalCustomer'),
      'parameter' => array(
        'customer' => array(
          'type' => 'fluxmite_customer',
          'label' => t('Mite: Customer'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
        'account' => static::getServiceParameterInfo(),
        'local_entity' => array(
          'type' => '*',
          'label' => t('Local Entity'),
          'required' => FALSE,
        ),
        'change_type' => array(
          'type' => 'text',
          'label' => t('Change type'),
          'required' => TRUE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute(MiteCustomer $customer, MiteAccountInterface $account, $local_entity, $change_type="") {
    $info=$customer->getInfo();
    $controller = entity_get_controller($info['name']);
    
    $change_type=$change_type."Local";
    $controller->$change_type($customer, $local_entity);

    watchdog("log_customer", $customer->getValueOf('name')." @".$account->label." (".$change_type.")");
  }
}
