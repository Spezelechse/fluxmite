<?php

/**
 * @file
 * Contains MiteTimeEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for time entities.
 */
class MiteTimeEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_time_posted',
      'label' => t('Something happend to a time entry'),
      'variables' => array(
        'account' => static::getServiceVariableInfo(),
        'time' => array(
          'type' => 'fluxmite_time',
          'label' => t('Mite: Time'),
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
    return 'Drupal\fluxmite\TaskHandler\MiteTimeTaskHandler';
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
