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
	print_r("service");
  	echo "<br>";
  	$this->processQueue();
	$this->checkAndInvoke();
  }
}