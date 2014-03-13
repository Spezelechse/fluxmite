<?php

/**
 * @file
 * Contains MiteTaskHandlerBase.
 */

namespace Drupal\fluxmite\TaskHandler;

use Drupal\fluxservice\Rules\TaskHandler\RepetitiveTaskHandlerBase;

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
  protected function checkAndInvoke(){
    $account = $this->getAccount();

    //extract the entity type from the event type
    $type=explode("_",$this->getEvent());
    $type=$type[1];

    //get all mite data
    $operation='get'.ucfirst($type).'s';
    $data_sets = $account->client()->$operation(array('api_key' => $account->getAccessToken()));

    //generate an array from xml
    $data_sets = json_decode(json_encode($data_sets),1);

    //arrays to store the entities which invoke events (something happend)
    $create=array();
    $update=array();
    $update_entities=array();
    $delete=array();
    $delete_ids=array();

    //get time_stamp of the last check
    $last_check=db_query("SELECT MAX(touched_last) as time FROM {fluxmite}");
    $last_check=$last_check->fetch();
    $last_check=$last_check->time;

    //check create, delete, update for all mite data sets
    foreach($data_sets[$type] as $data_set){
      $res=db_query("SELECT updated_at, id, type FROM {fluxmite} WHERE remote_id LIKE :id", array(':id'=>'%'.$data_set['id']));
      $res=$res->fetchAssoc();

      if($res){
        //check for updates
        $remote=date_create_from_format("Y-m-d?H:i:sP", $data_set['updated-at']);
        $remote=date_format($remote, "Y-m-d H:i:s");
        
        if($res['updated_at']<$remote){
          array_push($update, $data_set);
          array_push($update_entities, $res['id']);
        }

        db_query("UPDATE {fluxmite} SET touched_last=CURRENT_TIMESTAMP WHERE id=:id", array(':id'=>$res['id']));
      }
      else{
        array_push($create, $data_set);
      }
    }

    //get deleted id's
    $res=db_query("SELECT id FROM {fluxmite} WHERE touched_last <= '".$last_check."'");

    foreach($res as $data){
      array_push($delete_ids, $data->id);
      array_push($delete, array());
      db_delete('fluxmite')->condition('id',$data->id, '=')->execute();
    }

    $this->invokeEvent('fluxmite_'.$type, $create, $account, 'create');
    $this->invokeEvent('fluxmite_'.$type, $update, $account, 'update', $update_entities);
    $this->invokeEvent('fluxmite_'.$type, $delete, $account, 'delete', $delete_ids);     
  }
}