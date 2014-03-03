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


    if($change_type=="new"){
    //New
     // print_r("new");
      $info=$customer->value()->getInfo();
      $controller = entity_get_controller($info['name']);
      $test=$controller->create(array('name'=>"Peter"));

      $controller->save($test);

      //entity_delete($info['name'],$test->getValueOf('drupal_entity_id'));
    }

    if($change_type=="update"){
    //Save
     // print_r("update");
      $info=$customer->value()->getInfo();
      $controller = entity_get_controller($info['name']);
      $controller->save($customer->value());
    }

    if($change_type=="delete"){
    //Load
     // print_r("delete");
      $info=$customer->value()->getInfo();
      $controller = entity_get_controller($info['name']);
      $test=$controller->load(array($customer->value()->getValueOf('drupal_entity_id')));
    }

    watchdog("log_customer", $customer->value()->getValueOf('name')." @".$account->label." (".$change_type.")");
  }
}
