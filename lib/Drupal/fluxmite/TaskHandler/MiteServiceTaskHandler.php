<?php

/**
 * @file
 * Contains MiteServicePostedTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite services.
 */
class MiteServiceTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
	print_r("<br>service<br>");
  	$this->processQueue();
	$this->checkAndInvoke();
  }
}