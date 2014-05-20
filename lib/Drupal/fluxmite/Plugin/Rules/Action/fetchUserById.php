<?php

/**
 * @file
 * Contains fetchUserById.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * fetch user by id.
 */
class fetchUserById extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_fetch_user_by_id',
      'label' => t('Fetch user by id'),
      'parameter' => static::getServiceParameterInfo('fluxmite')+array(
        'remote_id' => array(
          'type' => 'integer',
          'label' => t('User id'),
          'required' => TRUE,
        ),
        'remote_entity' => array(
          'type' => 'entity',
          'label' => t('Remote entity'),
          'required' => TRUE,
          'wrapped' => FALSE,
        ),
      ),
      'provides' => array(
        'mite_user' => array(
          'type'=>'entity',
          'label' => t('Mite user')),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($account, $remote_id, $remote_entity) {

    $controller=entity_get_controller('fluxmite_user');

    $type='fluxmite_user';
    $user=$controller->loadRemote($remote_id, $account);

    if(!$user){
      $type=$remote_entity->entityType();
      $user=$remote_entity;
    }


    return array('mite_user' => entity_metadata_wrapper($type,$user));
  }
}
