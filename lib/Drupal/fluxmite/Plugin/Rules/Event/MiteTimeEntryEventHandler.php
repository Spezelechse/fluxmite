<?php

/**
 * @file
 * Contains MiteTimeEntryEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for time entities.
 */
class MiteTimeEntryEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_time_entry_event',
      'label' => t('Something happend to a time entry'),
      'variables' => array(
        'account' => static::getServiceVariableInfo(),
        'time' => array(
          'type' => 'fluxmite_time_entry',
          'label' => t('Mite: Time entry'),
          'description' => t('The time entry that triggered the event.'),
        ),
        'change_type' => array(
          'type' => 'text',
          'label' => t('Change type'),
        ),
        'local_entity_id' => array(
          'type' => 'integer',
          'label' => t('Local entity id'),
          'optional' => TRUE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTaskHandler() {
    return 'Drupal\fluxmite\TaskHandler\MiteTimeEntryTaskHandler';
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    $settings = $this->getSettings();
    if ($settings['account'] && $account = entity_load_single('fluxservice_account', $settings['account'])) {
      return $this->eventInfo['label'] . ' ' . t('of %account', array('%account' => "@{$account->label()}"));
    }
    return $this->eventInfo['label'];
  }

}
