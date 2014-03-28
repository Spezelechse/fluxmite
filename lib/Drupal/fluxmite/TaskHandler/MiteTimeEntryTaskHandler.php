<?php

/**
 * @file
 * Contains MiteTimeTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite time entries.
 */
class MiteTimeEntryTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
    if($this->checkRequirements()){
    	print_r("time entry");
    	echo "<br>";
    	$this->processQueue();
  	  $this->checkAndInvoke();
    }
    $this->afterTaskComplete();
  }


  /**
   *
   */
  public function checkRequirements(){
    //check needed types are handled
    $needed_types=array('customer','user','project','service');
    foreach ($needed_types as $type) {
      $res=db_select('rules_scheduler','rs')
              ->fields('rs',array('tid'))
              ->condition('rs.identifier','fluxmite_'.$type.'%','LIKE')
              ->execute()
              ->fetch();
      if(!$res){
        return false;
      }
    }

    //check time_entries are handled last
    $time_entry=db_select('rules_scheduler','rs')
                  ->fields('rs',array('identifier'))
                  ->condition('rs.identifier','fluxmite%','LIKE')
                  ->orderBy('rs.date','DESC')
                  ->execute()
                  ->fetch();

    if(!strpos($time_entry->identifier,'time_entry')){
      return false;
    }

    return true;
  }

  /**
   * 
   */
  public function afterTaskComplete(){
    $service = $this->getAccount()->getService();
    
    //make sure time entries will be handled last
    $res=db_select('rules_scheduler','rs')
      ->fields('rs',array('date'))
      ->condition('rs.identifier', 'fluxmite%','LIKE')
      ->orderBy('rs.date','DESC')
      ->execute()
      ->fetch();

    // Continuously reschedule the task.
    db_update('rules_scheduler')
      ->condition('tid', $this->task['tid'])
      ->condition('date', $res->date, '<>')
      ->fields(array('date' => $res->date+1 + $service->getPollingInterval()))
      ->execute();
  }
}