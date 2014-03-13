<?php

/**
 * @file
 * Contains MiteServiceEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for services.
 */
class MiteServiceEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_service_event',
      'label' => t('Somthing happend to a service'),
      'variables' => array(
        'account' => static::getServiceVariableInfo(),
        'service' => array(
          'type' => 'fluxmite_service',
          'label' => t('Mite: Service'),
          'description' => t('The service that triggered the event.'),
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
    return 'Drupal\fluxmite\TaskHandler\MiteServiceTaskHandler';
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
