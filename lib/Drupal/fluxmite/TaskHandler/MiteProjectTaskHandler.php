<?php

/**
 * @file
 * Contains MiteProjectTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

/**
 * Event dispatcher for changed mite projects.
 */
class MiteProjectTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {

  	$account = $this->getAccount();

}