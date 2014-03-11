<?php

/**
 * @file
 * Contains MiteTaskHandlerBase.
 */

namespace Drupal\fluxmite\TaskHandler;

use Drupal\fluxservice\Rules\TaskHandler\RepetitiveTaskHandlerBase;

/**
 * Base class for Mite task handlers that dispatch Rules events.
 */
class MiteTaskHandlerBase extends RepetitiveTaskHandlerBase {

  /**
   * Gets the configured event name to dispatch.
   */
  public function getEvent() {
    return $this->task['identifier'];
  }

  /**
   * Gets the configured Mite account.
   *
   * @throws \RulesEvaluationException
   *   If the account cannot be loaded.
   *
   * @return \Drupal\fluxmite\Plugin\Service\MiteAccount
   */
  public function getAccount() {
    $account = entity_load_single('fluxservice_account', $this->task['data']['account']);
    if (!$account) {
      throw new \RulesEvaluationException('The specified mite account cannot be loaded.', array(), NULL, \RulesLog::ERROR);
    }
    return $account;
  }

  /**
   * {@inheritdoc}
   */
  public function afterTaskQueued() {
    try {
      $service = $this->getAccount()->getService();

      // Continuously reschedule the task.
      db_update('rules_scheduler')
        ->condition('tid', $this->task['tid'])
        ->fields(array('date' => $this->task['date'] + $service->getPollingInterval()))
        ->execute();
    }
    catch(\RulesEvaluationException $e) {
      rules_log($e->msg, $e->args, $e->severity);
    }
  }

  public function invokeEvent($entity_type, $entities, $account, $change_type, $local_entity_ids=array()){
    if(!empty($entities)){
      $entities = fluxservice_entify_multiple($entities, $entity_type, $account);

      $i=0;
      foreach ($entities as $key => $entity) {
        $local_entity_id=$local_entity_ids[$i++];
        rules_invoke_event($this->getEvent(), $account, $entity, $change_type, $local_entity_id);
      }
    }
  }
}
