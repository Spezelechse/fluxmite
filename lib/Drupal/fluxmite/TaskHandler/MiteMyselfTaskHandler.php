<?php

/**
 * @file
 * Contains MiteMyselfTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite myself.
 */
class MiteMyselfTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
  	print_r("<br>myself<br>");
	$this->checkAndInvoke();
  }
}