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
      $this->task['remote_type']=$type.'-'.$type_split[2];
      $this->task['entity_type']=$type.'_'.$type_split[2];
    }
  }
  
  /**
   * 
   */
  protected function getRemoteDatasets(){
    $data_sets=array();

    $client=$this->getAccount()->client();
    $account=$this->getAccount();

    //get all mite data
    $operation='get'.ucfirst($this->getEntityType()).'s';
    
    try{
      $data_sets = $client->$operation(array('api_key' => $account->getAccessToken()));

      //generate an array from xml
      $data_sets = json_decode(json_encode($data_sets),1);
      if(isset($data_sets[$this->getRemoteType()])){
        $data_sets = $data_sets[$this->getRemoteType()];
        
        //workaround for single response
        if(!isset($data_sets[0])){
          $data_sets=array($data_sets);
        }
      }
      else{
        $data_sets=array();
      }
    }
    catch(BadResponseException $e){
      if($e->getResponse()->getStatusCode()==404){
        watchdog('Fluxmite','[404] Host "'.$client->getBaseUrl().'" not found ('.$operation.')');
      }
    }
    print_r($data_sets);
    print_r("<br>");
    return $data_sets;
  }

  /**
   *  
   */
  protected function getCheckvalue($data_set){
    return strtotime($data_set['updated-at']);
  }
}