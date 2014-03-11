<?php

/**
 * @file
 * Contains addRemoteCustomer.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;


/**
 * Set a Customer.
 */
class setAccount extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_set_account',
      'label' => t('setAccount'),
      'parameter' => array(
        'account' => static::getServiceParameterInfo(),
        'remote Entity' => array(
        	'type' => '*',
        	'label' => t('Remote entity'),
        	'required' => TRUE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute(MiteAccountInterface $account, $remote_entity) {
    $info=$remote_entity->getInfo();
    $controller = entity_get_controller($info['name']);
    
    $remote_entity->setAccount($account);
  }
}
