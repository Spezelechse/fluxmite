<?php

/**
 * @file
 * Contains updateRemoteEntity.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * update remote entities.
 */
class updateRemoteEntity extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_update_remote_entity',
      'label' => t('updateRemoteEntity'),
      'parameter' => array(
        'remote_entity' => array(
          'type' => '*',
          'label' => t('Mite: Entity'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
        'account' => static::getServiceParameterInfo(),
        'local_entity' => array(
          'type' => '*',
          'label' => t('Local: Entity'),
          'required' => TRUE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute($remote_entity, MiteAccountInterface $account, $local_entity) {
    $info=$remote_entity->getInfo();
    $controller = entity_get_controller($info['name']);
    
    $controller->updateRemote($remote_entity, $local_entity, $account);

    watchdog("log_customer", $remote_entity->getValueOf('name')." @".$account->label." (update)");
  }
}
