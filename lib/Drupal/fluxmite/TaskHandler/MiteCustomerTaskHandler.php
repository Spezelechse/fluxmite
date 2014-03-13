<?php

/**
 * @file
 * Contains MiteCustomerPostedTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

use \PDO;
/**
 * Event dispatcher for changed mite customers.
 */
class MiteCustomerTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() { 
    $this->checkAndInvoke();
  }
}