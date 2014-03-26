<?php

/**
 * @file
 * Contains MiteAccountTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite accounts.
 */
class MiteAccountTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
	print_r("account");
  	echo "<br>";
  	$this->processQueue();
	$this->checkAndInvoke();
  }
}