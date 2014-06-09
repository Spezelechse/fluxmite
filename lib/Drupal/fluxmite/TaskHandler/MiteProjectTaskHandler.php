<?php

/**
 * @file
 * Contains MiteProjectTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite projects.
 */
class MiteProjectTaskHandler extends MiteTaskHandlerBase {
	protected $needed_types=array('customer','service');
  /**
   * {@inheritdoc}
   */
  public function runTask() {
  	if($this->checkDependencies()){
  		print_r("<br>project<br>");
  		$this->processQueue();
  		$this->checkAndInvoke();
  	}
  	$this->afterTaskComplete();
  }
}