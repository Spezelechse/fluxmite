<?php

/**
 * @file
 * Contains MiteProjectEventHandler.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

/**
 * Event handler for projects.
 */
class MiteProjectEventHandler extends MiteEventHandlerBase {

  /**
   * Defines the event.
   */
  public static function getInfo() {
    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_project_event',
      'label' => t('Somthing happend to a project'),
      'variables' => array(
        'account' => static::getServiceVariableInfo(),
        'project' => array(
          'type' => 'fluxmite_project',
          'label' => t('Mite: Project'),
          'description' => t('The project that triggered the event.'),
        ),
        'change_type' => array(
          'type' => 'text',
          'options list' => 'change_type_get_options',
          'label' => t('Change type'),
          'restiction' => 'input',
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
    return 'Drupal\fluxmite\TaskHandler\MiteProjectTaskHandler';
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
