<?php

/**
 * @file
 * Contains MiteTaskHandlerBase.
 */

namespace Drupal\fluxmite\TaskHandler;

use Drupal\fluxservice\Rules\TaskHandler\RepetitiveTaskHandlerBase;
use Guzzle\Http\Exception\BadResponseException;
use Drupal\fluxmite\MiteTaskQueue;

/**
 * Base class for Mite task handlers that dispatch Rules events.
 */
class MiteTaskHandlerBase extends RepetitiveTaskHandlerBase {

  /**
   * Gets the configured event name to dispatch.
   */
  public function getEvent() {
    return $this->task['identifier'];
  }

  /**
   * Gets the configured Mite account.
   *
   * @throws \RulesEvaluationException
   *   If the account cannot be loaded.
   *
   * @return \Drupal\fluxmite\Plugin\Service\MiteAccount
   */
  public function getAccount() {
    $account = entity_load_single('fluxservice_account', $this->task['data']['account']);
    if (!$account) {
      throw new \RulesEvaluationException('The specified mite account cannot be loaded.', array(), NULL, \RulesLog::ERROR);
    }
    return $account;
  }

  /**
   * {@inheritdoc}
   */
  public function afterTaskQueued() {
    try {
      $service = $this->getAccount()->getService();

      // Continuously reschedule the task.
      db_update('rules_scheduler')
        ->condition('tid', $this->task['tid'])
        ->fields(array('date' => $this->task['date'] + $service->getPollingInterval()))
        ->execute();
    }
    catch(\RulesEvaluationException $e) {
      rules_log($e->msg, $e->args, $e->severity);
    }
  }

/**
 * invoke events for all given entities
 * 
 * @param string $entity_type
 * A string defining the entity type
 * 
 * @param array $entities
 * An array of arrays defining the entities
 * 
 * @param MiteAccount (service account) $account
 * The account used to connect to mite
 * 
 * @param string $change_type
 * Event type that happend to the entity (create, delete, update)
 * 
 * @param array $local_entity_ids
 * if needed the local entity ids which refer to the remote entities
 */
  public function invokeEvent($entity_type, $entities, $account, $change_type, $local_entity_ids=array()){
    if(!empty($entities)){
      $entities = fluxservice_entify_multiple($entities, $entity_type, $account);

      $i=0;
      if($entities){
        foreach ($entities as $key => $entity) {
          if(!empty($local_entity_ids)){
            $local_entity_id=$local_entity_ids[$i++];
            rules_invoke_event($this->getEvent(), $account, $entity, $change_type, $local_entity_id);
          }
          else{
            rules_invoke_event($this->getEvent(), $account, $entity, $change_type); 
          }
        }
      }
    }
  }

/**
 * Checks for mite "updates" and invoke the appropriate events
 */
  public function checkAndInvoke(){
    $account = $this->getAccount();

    //extract the entity type from the event type
    $type_split=explode("_",$this->getEvent());
    $type=$type_split[1];

    //workaround for time_entries
    if($type=='time'){
      $type.='_'.$type_split[2];
    }

    //get all mite data
    $operation='get'.ucfirst($type).'s';
    
    try{
      $data_sets = $account->client()->$operation(array('api_key' => $account->getAccessToken()));
        
      //get replace params
      $controller=entity_get_controller('fluxmite_'.$type);
      $search=array_keys($controller->miteSpecialFields());
      $replace=array_values($controller->miteSpecialFields());

      //generate an array from xml
      $data_sets = json_decode(str_replace($search,$replace,json_encode($data_sets)),1);
    }
    catch(BadResponseException $e){
      if($e->getResponse()->getStatusCode()==404){
        watchdog('Fluxmite','[404] Host "'.$account->client()->getBaseUrl().'" not found ('.$operation.')');
      }
    }

    //if stuff is available process it
    if(isset($data_sets[$type])){

      //arrays to store the entities which invoke events (something happend)
      $create=array();
      $update=array();
      $update_local_ids=array();
      $delete=array();
      $delete_local_ids=array();

      //get time_stamp of the last check
      $last_check=db_query("SELECT MAX(touched_last) as time FROM {fluxmite} WHERE remote_type = :type", array(':type'=>'fluxmite_'.$type));
      $last_check=$last_check->fetch();
      $last_check=$last_check->time;

      //check all data_sets
      if(isset($data_sets[$type][0])){//multiple
        foreach($data_sets[$type] as $data_set){
          $this->checkSingleResponseSet($data_set, $create, $delete, $delete_local_ids, $update, $update_local_ids);
        }
      }
      else{//single
        $this->checkSingleResponseSet($data_sets[$type], $create, $delete, $delete_local_ids, $update, $update_local_ids);
      }

      //get deleted id's
      $res=db_query("SELECT id FROM {fluxmite} WHERE touched_last <= :last_check AND remote_type = :type",
                    array(':last_check'=>$last_check, ':type'=>'fluxmite_'.$type));

      foreach($res as $data){
        array_push($delete_local_ids, $data->id);
        array_push($delete, array());
        db_delete('fluxmite')->condition('id',$data->id, '=')->condition('remote_type','fluxmite_'.$type,'=')->execute();
      }

      $this->invokeEvent('fluxmite_'.$type, $create, $account, 'create');
      $this->invokeEvent('fluxmite_'.$type, $update, $account, 'update', $update_local_ids);
      $this->invokeEvent('fluxmite_'.$type, $delete, $account, 'delete', $delete_local_ids);
    }     
  }

/**
 * checks which event is needed for the given mite data_set
 */
  private function checkSingleResponseSet($data_set, &$create, &$delete, &$delete_local_ids, &$update, &$update_local_ids){
    $res=db_query("SELECT updated_at, id FROM {fluxmite} WHERE mite_id = :id", array(':id'=>$data_set['id']));
    $res=$res->fetchAssoc();

    if($res){
      //check for updates
      if($res['updated_at']<strtotime($data_set['updated_at'])){
        array_push($update, $data_set);
        array_push($update_local_ids, $res['id']);
      }

      db_query("UPDATE {fluxmite} SET touched_last=".time()." WHERE id=:id", array(':id'=>$res['id']));
    }
    else{
      array_push($create, $data_set);
    }
  }

  /**
   * 
   */

  protected function processQueue(){
    $type_split=explode("_",$this->getEvent());
    $type=$type_split[0].'_'.$type_split[1];

    //workaround for time_entries
    if($type_split[1]=='time'){
      $type.='_'.$type_split[2];
    }

    MiteTaskQueue::cleanQueue($type);
    $queue=MiteTaskQueue::getTasks($type);

    echo "<br>";
    foreach ($queue as $task) {
      MiteTaskQueue::resetTaskFailed($task->id);

      if($task->task_type=='post'){
        $controller = entity_get_controller($task->remote_type);
        $remote=$controller->createRemote($task->local_id,
                                  $task->local_type,
                                  $this->getAccount(),
                                  null,
                                  $task->request,
                                  $task->remote_type);

        if(isset($remote)){
          rules_invoke_event($this->getEvent(), $this->getAccount(), $remote, 'update');
        }
      }
      else if($task->task_type=='put'){
        $entity = entity_load_single($task->local_type, $task->local_id);
        $entity = entity_metadata_wrapper($task->local_type, $entity);
        $entity->save();
      }
      else if($task->task_type=='delete'){
        $controller = entity_get_controller($task->remote_type);
        $controller->deleteRemote($task->local_id,
                                  $task->local_type,
                                  $this->getAccount(),
                                  $task->remote_type,
                                  $task->mite_id);
      }
      else{
        //TODO: throw exception unkown task
      }
      echo $task->task_type.": ".$task->local_type."(".$task->local_id.")<br>";
    }
    echo "<br>";
  }
}