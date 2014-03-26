<?php

/**
 * @file
 * Contains fetchReferenceByMiteId.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * fetch reference by mite id.
 */
class fetchReferenceByMiteId extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_fetch_reference_by_mite_id',
      'label' => t('Fetch reference by mite id'),
      'parameter' => array(
        'mite_id' => array(
          'type' => 'integer',
          'label' => t('Mite id'),
          'required' => TRUE,
        ),
        'local_type' => array(
          'type' => 'text',
          'label' => t('Local entity type'),
          'options list' => 'rules_entity_action_type_options',
          'description' => t('Specifies the type of referenced entity.'),
          'restriction' => 'input',
        ),
      ),
      'provides' => array(
        'reference' => array(
          'type'=>'entity',
          'label' => t('Fetched reference')),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($mite_id, $local_type) {
    $res=db_query("SELECT id, type FROM {fluxmite} WHERE mite_id = :id AND type =  :type", 
                  array(  ':id'=>$mite_id, 
                          ':type'=>$local_type));

    $res=$res->fetchAssoc();

    if($res){
      if(!$reference=entity_load_single($res['type'], $res['id'])){
          $reference=$remote_entity;
          $res['type']=$remote_entity->entityType();
      }

      return array('reference' => entity_metadata_wrapper($res['type'],$reference));
    }
  }
}
