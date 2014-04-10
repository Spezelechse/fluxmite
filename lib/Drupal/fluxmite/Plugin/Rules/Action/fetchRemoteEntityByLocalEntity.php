<?php

/**
 * @file
 * Contains fetchRemoteEntityByLocalEntity.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * update remote entities.
 */
class fetchRemoteEntityByLocalEntity extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_fetch_remote_entity_by_local_entity',
      'label' => t('Fetch remote entity by local entity'),
      'parameter' => array(
        'local_entity' => array(
          'type' => 'entity',
          'label' => t('Local entity'),
          'required' => TRUE,
          'wrapped' => FALSE,
        ),
      ),
      'provides' => array(
        'entity_fetched' => array(
          'type'=>'entity',
          'label' => t('Fetched entity')),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($local_entity) {
    //print_r("<br>fetch remote<br>");
    $local_type="";
    $local_id=0;
    $isNode=1;
    if(method_exists($local_entity, 'entityType')){
      $local_type=$local_entity->entityType();
      $local_id=$local_entity->id;
      $isNode=0;
    }
    else{
      $local_type=$local_entity->type;
      $local_id=$local_entity->nid;
    }

    $res=db_select('fluxmite','fm')
            ->fields('fm',array('remote_id','remote_type'))
            ->condition('fm.id',$local_id,'=')
            ->condition('fm.type',$local_type,'=')
            ->condition('fm.isNode',$isNode)
            ->execute()
            ->fetchAssoc();


    if($res){
      if(!$remote_entity=entity_load_single($res['remote_type'], $res['remote_id'])){
        $remote_entity=$local_entity;
        $res['remote_type']=$local_type;
      }

      return array('entity_fetched' => entity_metadata_wrapper($res['remote_type'],$remote_entity));
    }
  }
}
