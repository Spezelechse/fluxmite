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
/**
   * {@inheritdoc}
   */
  public function runTask() {
  	print_r("<br>customer<br>");
  	$this->processQueue();
    $this->checkAndInvoke();
  }
}