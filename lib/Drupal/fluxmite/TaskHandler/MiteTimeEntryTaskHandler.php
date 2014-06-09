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
  protected $needed_types=array('customer','user','project','service');

  /**
   * {@inheritdoc}
   */
  public function runTask() {
    if($this->checkDependencies()){
    	print_r("<br>time entry<br>");
    	$this->processQueue();
  	  $this->checkAndInvoke();
    }
    $this->afterTaskComplete();
  }
}