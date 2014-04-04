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

  public function __construct(array $task) {
    parent::__construct($task);
    
    //extract the entity type from the event type
    $type_split=explode("_",$this->task['identifier']);
    $type=$type_split[1];
    $mite_type=$type;

    //workaround for time_entries
    if($type=='time'){
      $type.='_'.$type_split[2];
      $mite_type.='-'.$type_split[2];
    }

    $this->task['entity_type']=$type;
    $this->task['mite_type']=$mite_type;
  }
  /**
   * Gets the configured event name to dispatch.
   */
  public function getEvent() {
    return $this->task['identifier'];
  }

  /**
   * 
   */
  public function getEntityType(){
    return $this->task['entity_type'];
  }

  /**
   * 
   */
  public function getMiteType(){
    return $this->task['mite_type'];
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
   *
   */
  public function checkRequirements(){
    if($this->checkDataExists()){
      //check required is handled
      foreach ($this->needed_types as $type) {
        $sched=db_select('rules_scheduler','rs')
                ->fields('rs',array('date'))
                ->condition('rs.identifier','fluxmite_'.$type.'%','LIKE')
                ->execute()
                ->fetch();
        if(!$sched){
          watchdog('fluxmite', "Missing taskhandler for ".$type."  (@".$this->getEntityType().")");
          return false;
        }else{
          //check is handled before
        $res=db_select('rules_scheduler','rs')
            ->fields('rs',array('tid'))
            ->condition('rs.date',$sched->date,'>')
            ->condition('rs.identifier','fluxmite_'.$this->getEntityType().'%','LIKE')
            ->execute()
            ->fetch();
        if(!$res){
          watchdog('fluxmite', "Notice: Wrong taskhandler order will be changed now (@".$this->getEntityType().")");
          return false;
        }
        }
      }
      return true;
    }
    return false;
  }

 /**
  * 
  */
  public function checkDataExists(){
    foreach ($this->needed_types as $type) {
      $res=db_select('fluxmite','fm')
          ->fields('fm')
          ->condition('fm.remote_type','fluxmite_'.$type)
          ->execute();

      if($res->rowCount()<=0){
        watchdog('fluxmite', "Missing database entries for ".$type." (@".$this->getEntityType().")");
        return false;
      }
    }
    return true;
  }

  /**
   * 
   */
  public function afterTaskComplete(){
    $service = $this->getAccount()->getService();

    $data=$this->getScheduleData();

    if($data){
      db_update('rules_scheduler')
        ->condition('tid', $this->task['tid'])
        ->fields(array('date' => $data->date + 1 + $service->getPollingInterval()))
        ->execute();
    }
    else{
      db_update('rules_scheduler')
        ->condition('tid', $this->task['tid'])
        ->fields(array('date' => $this->task['date'] + $service->getPollingInterval()))
        ->execute();
    }
  }

 /**
  * 
  */
  public function getScheduleData(){
    $or=db_or();

    foreach ($this->needed_types as $type) {
      $or->condition('rs.identifier','fluxmite_'.$type.'%','LIKE');
    }

    $data=db_select('rules_scheduler','rs')
            ->fields('rs',array('date'))
            ->condition($or)
            ->orderBy('rs.date','DESC')
            ->execute()
            ->fetch();

    return $data;
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
        foreach ($entities as $entity) {
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
    $mite_type=$type;

    //workaround for time_entries
    if($type=='time'){
      $type.='_'.$type_split[2];
      $mite_type.='-'.$type_split[2];
    }

    //get all mite data
    $operation='get'.ucfirst($type).'s';
    
    try{
      $data_sets = $account->client()->$operation(array('api_key' => $account->getAccessToken()));
        
      //$pattern = '~(?:<|\G(?<!^))(?:[^>-]+)*\K\-~';

      //generate an array from xml
      $data_sets = json_decode(json_encode($data_sets),1);
    }
    catch(BadResponseException $e){
      if($e->getResponse()->getStatusCode()==404){
        watchdog('Fluxmite','[404] Host "'.$account->client()->getBaseUrl().'" not found ('.$operation.')');
      }
    }

    //if stuff is available process it
    if(isset($data_sets[$mite_type])){

      //arrays to store the entities which invoke events (something happend)
      $create=array();
      $update=array();
      $update_local_ids=array();
      $delete=array();
      $delete_local_ids=array();

      $last_check=db_select('fluxmite','fm')
                    ->fields('fm',array('touched_last'))
                    ->condition('fm.remote_type','fluxmite_'.$type,'=')
                    ->orderBy('fm.touched_last','DESC')
                    ->execute()
                    ->fetch();
    
      if($last_check){
        $last_check=$last_check->touched_last;
      }
      else{
        $last_check=time();
      }

      //check all data_sets
      if(isset($data_sets[$mite_type][0])){//multiple
        foreach($data_sets[$mite_type] as $data_set){
          $this->checkSingleResponseSet($data_set, $create, $update, $update_local_ids);
        }
      }
      else{//single
        $this->checkSingleResponseSet($data_sets[$mite_type], $create, $update, $update_local_ids);
      }

      //get deleted id's
      $res=db_select('fluxmite','fm')
              ->fields('fm',array('id','mite_id','touched_last'))
              ->condition('fm.touched_last',$last_check,'<=')
              ->condition('fm.remote_type','fluxmite_'.$type,'=')
              ->execute();

      foreach($res as $data){
        print_r('delete local: '.$data->touched_last.'<br>');
        array_push($delete_local_ids, $data->id);
        array_push($delete, array('id'=>$data->mite_id));
        db_delete('fluxmite')
          ->condition('id',$data->id, '=')
          ->condition('remote_type','fluxmite_'.$type,'=')
          ->execute();
      }

      $this->invokeEvent('fluxmite_'.$type, $create, $account, 'create');
      $this->invokeEvent('fluxmite_'.$type, $update, $account, 'update', $update_local_ids);
      $this->invokeEvent('fluxmite_'.$type, $delete, $account, 'delete', $delete_local_ids);
    }     
  }

/**
 * checks which event is needed for the given mite data_set
 */
  private function checkSingleResponseSet($data_set, &$create, &$update, &$update_local_ids){
    $res=db_select('fluxmite','fm')
          ->fields('fm',array('updated_at','id'))
          ->condition('mite_id',$data_set['id'])
          ->execute()
          ->fetchAssoc();


    if($res){
      //check for updates
      if($res['updated_at']<strtotime($data_set['updated-at'])){
        array_push($update, $data_set);
        array_push($update_local_ids, $res['id']);
      }

      db_update('fluxmite')
        ->fields(array('touched_last'=>time()))
        ->condition('id',$res['id'],'=')
        ->execute();
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

    $queue=new MiteTaskQueue($type,$this->getAccount());

    $queue->process();
  }
}