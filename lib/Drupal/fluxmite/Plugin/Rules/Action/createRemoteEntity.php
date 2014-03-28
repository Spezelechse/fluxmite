<?php

/**
 * @file
 * Contains createRemoteEntity.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * create remote entities.
 */
class createRemoteEntity extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_create_remote_entity',
      'label' => t('Create remote entity'),
      'parameter' => array(
        'account' => static::getServiceParameterInfo(),
        'remote_entity' => array(
          'type' => 'entity',
          'label' => t('Mite: Entity'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
        'local_entity' => array(
          'type' => 'entity',
          'label' => t('Local: Entity'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute(MiteAccountInterface $account, $remote_entity, $local_entity) {
    dpm("create remote");
    print_r("create remote<br>");
    $controller = entity_get_controller($remote_entity->entityType());
    
    $created = $controller->createRemote($local_entity->id, $local_entity->entityType(), $account, $remote_entity);

     //local update
    if(isset($created)){
      $res=db_query("SELECT event FROM {rules_trigger} WHERE event LIKE :type", array(':type'=>$remote_entity->entityType().'_event--%'));
      $res=$res->fetch();
      rules_invoke_event($res->event, $account, $created, 'update', $local_entity->id);
    }
  }
}
