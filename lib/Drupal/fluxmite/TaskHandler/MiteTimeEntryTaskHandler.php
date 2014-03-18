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
  	print_r("time");
  	echo "<br>";
	$this->checkAndInvoke();
  }
}