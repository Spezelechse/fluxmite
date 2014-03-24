<?php

/**
 * @file
 * Contains MiteProjectTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

use \PDO;
/**
 * Event dispatcher for changed mite projects.
 */
class MiteProjectTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
  	print_r("project");
  	echo "<br>";
  	$this->processQueue();
	$this->checkAndInvoke();
  }
}