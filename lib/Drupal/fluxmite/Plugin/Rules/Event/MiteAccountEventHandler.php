<?php

/**
 * @file
 * Contains MiteAccountEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for accounts.
 */
class MiteAccountEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_account_event',
      'label' => t('Something happend to this account'),
      'variables' => array(
        'account' => static::getServiceVariableInfo(),
        'mite_account' => array(
          'type' => 'fluxmite_account',
          'label' => t('Mite: Account'),
          'description' => t('The account that triggered the event.'),
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
    return 'Drupal\fluxmite\TaskHandler\MiteAccountTaskHandler';
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
