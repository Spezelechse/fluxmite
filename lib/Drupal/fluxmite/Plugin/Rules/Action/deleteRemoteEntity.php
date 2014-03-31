<?php

/**
 * @file
 * Contains deleteRemoteEntity.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * delete remote entities.
 */
class deleteRemoteEntity extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_delete_remote_entity',
      'label' => t('Delete remote entity'),
      'parameter' => array(
        'account' => static::getServiceParameterInfo(),
        'local_entity' => array(
          'type' => 'entity',
          'label' => t('Local: Entity'),
          'required' => TRUE,
          'wrapped' => FALSE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute(MiteAccountInterface $account, $local_entity) {
    $res=db_select('fluxmite','fm')
            ->fields('fm',array('mite_id','remote_type'))
            ->condition('fm.id',$local_entity->id,'=')
            ->condition('fm.type',$local_entity->entityType(),'=')
            ->execute()
            ->fetchAssoc();

    if($res){
      $controller = entity_get_controller($res['remote_type']);
    
      $controller->deleteRemote($local_entity->id, $local_entity->entityType(), $account, $res['remote_type'], $res['mite_id']);
    }
  }
}
