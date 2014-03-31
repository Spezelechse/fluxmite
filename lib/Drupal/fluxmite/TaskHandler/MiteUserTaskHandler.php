<?php

/**
 * @file
 * Contains MiteUserTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite users.
 */
class MiteUserTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
   	print_r("<br>user<br>");
  	$this->processQueue();
	$this->checkAndInvoke();
  }
}