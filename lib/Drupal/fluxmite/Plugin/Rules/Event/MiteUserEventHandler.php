<?php

/**
 * @file
 * Contains MiteUserEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for user.
 */
class MiteUserEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_user_posted',
      'label' => t('Something happend to a user'),
      'variables' => array(
        'account' => static::getServiceVariableInfo(),
        'user' => array(
          'type' => 'fluxmite_user',
          'label' => t('Mite: User'),
          'description' => t('The user that triggered the event.'),
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
    return 'Drupal\fluxmite\TaskHandler\MiteUserTaskHandler';
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
