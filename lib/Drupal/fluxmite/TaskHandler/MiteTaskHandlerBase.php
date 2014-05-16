<?php

/**
 * @file
 * Contains MiteTaskHandlerBase.
 */

namespace Drupal\fluxmite\TaskHandler;

use Drupal\fluxservice_extension\TaskHandler\RepetitiveTaskHandlerBaseExtended;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Base class for Mite task handlers that dispatch Rules events.
 */
class MiteTaskHandlerBase extends RepetitiveTaskHandlerBaseExtended {

  public function __construct(array $task) {
    parent::__construct($task);
    
    //extract the entity type from the event type
    $type_split=explode("_",$this->task['identifier']);
    $type=$type_split[1];

    //workaround for time_entries
    if($type=='time'){
      $mite_type.=$type.'-'.$type_split[2];
      $this->task['remote_type']=$mite_type;
    }
  }
  
  /**
   * 
   */
  protected function getRemoteDatasets(){
    $data_sets=array();

    $client=$this->getAccount()->client();


    //get all mite data
    $operation='get'.ucfirst($this->getEntityType()).'s';
    
    try{
      $data_sets = $account->client()->$operation(array('api_key' => $account->getAccessToken()));
        
      //$pattern = '~(?:<|\G(?<!^))(?:[^>-]+)*\K\-~';

      //generate an array from xml
      $data_sets = json_decode(json_encode($data_sets),1);
      $data_sets = $data_sets[$this->getRemoteType()];

      //workaround for single response
      if(!isset($data_sets[$mite_type][0])){
        $data_sets=array($data_sets);
      }
    }
    catch(BadResponseException $e){
      if($e->getResponse()->getStatusCode()==404){
        watchdog('Fluxmite','[404] Host "'.$account->client()->getBaseUrl().'" not found ('.$operation.')');
      }
    }

    return $data_sets;
  }

  /**
   *  
   */
  protected function getCheckvalue($data_set){
    return strtotime($data_set['updated-at']);
  }
}