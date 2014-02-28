<?php

/**
 * @file
 * Contains MiteEventHandlerBase.
 */

namespace Drupal\fluxmite\Plugin\Rules\Event;

use Drupal\fluxservice\Rules\DataUI\AccountEntity;
use Drupal\fluxservice\Rules\DataUI\ServiceEntity;
use Drupal\fluxservice\Rules\EventHandler\CronEventHandlerBase;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * Cron-based base class for Mite event handlers.
 */
abstract class MiteEventHandlerBase extends CronEventHandlerBase {

  /**
   * Returns info-defaults for mite plugin handlers.
   */
  public static function getInfoDefaults() {
    return RulesPluginHandlerBase::getInfoDefaults();
  }

  /**
   * Rules mite integration access callback.
   */
  public static function integrationAccess($type, $name) {
    return fluxservice_access_by_plugin('fluxmite');
  }

  /**
   * Returns info for the provided mite service account variable.
   */
  public static function getServiceVariableInfo() {
    return array(
      'type' => 'fluxservice_account',
      'bundle' => 'fluxmite',
      'label' => t('Mite account'),
      'description' => t('The account used for authenticating with the Mite API.'),
    );
  }
 
  /**
   * {@inheritdoc}
   */
  public function getDefaults() {
    return array(
      'account' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array &$form_state) {
    $settings = $this->getSettings();

    $form['account'] = array(
      '#type' => 'select',
      '#title' => t('Account'),
      '#description' => t('The service account used for authenticating with the Mite API.'),
      '#options' => AccountEntity::getOptions('fluxmite', $form_state['rules_config']),
      '#default_value' => $settings['account'],
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getEventNameSuffix() {
    return drupal_hash_base64(serialize($this->getSettings()));
  }

}
