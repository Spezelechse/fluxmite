<?php

/**
 * @file
 * Contains MiteMyselfEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for myself.
 */
class MiteMyselfEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_myself_event',
      'label' => t('Something happend to myself'),
      'variables' => static::getServiceVariableInfo()+array(
        'customer' => array(
          'type' => 'fluxmite_myself',
          'label' => t('Mite: Myself'),
          'description' => t('Myself who triggered the event.'),
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTaskHandler() {
    return 'Drupal\fluxmite\TaskHandler\MiteMyselfTaskHandler';
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
