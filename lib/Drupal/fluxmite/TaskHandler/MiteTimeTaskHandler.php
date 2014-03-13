<?php

/**
 * @file
 * Contains MiteTimeTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite time entries.
 */
class MiteTimeTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
	$this->checkAndInvoke();
  }
}