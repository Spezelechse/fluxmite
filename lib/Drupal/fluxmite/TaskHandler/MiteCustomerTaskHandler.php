<?php

/**
 * @file
 * Contains MiteCustomerPostedTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite customers.
 */
class MiteCustomerTaskHandler extends MiteTaskHandlerBase {
	protected $needed_types=array('service');
  /**
   * {@inheritdoc}
   */
  public function runTask() {
  	if($this->checkRequirements()){
	  	print_r("<br>customer<br>");
	  	$this->processQueue();
	    $this->checkAndInvoke();
	}

	$this->afterTaskComplete();
  }
}