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
          'wrapped' => TRUE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute(MiteAccountInterface $account, $local_entity) {
    dpm("delete remote mite");
    print_r("delete remote mite<br>");

    $local_type="";
    $local_id=0;
    $isNode=1;

    if(method_exists($local_entity, 'entityType')){
      $local_type=$local_entity->entityType();
      $local_id=$local_entity->id;
      $isNode=0;
    }
    else{
      $local_type=$local_entity->type();
      $local_id=$local_entity->getIdentifier();
    }

    $res=db_select('fluxmite','fm')
            ->fields('fm',array('mite_id','remote_type'))
            ->condition('fm.id',$local_id,'=')
            ->condition('fm.type',$local_type,'=')
            ->condition('fm.isNode',$isNode)
            ->execute()
            ->fetchAssoc();

    if($res){
      $controller = entity_get_controller($res['remote_type']);
    
      $controller->deleteRemote($local_id, $local_type, $isNode, $account, $res['remote_type'], $res['mite_id']);
    }
  }
}
