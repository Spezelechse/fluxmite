<?php

/**
 * @file
 * Contains updateLocalEntity.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * Send a customer action.
 */
class updateLocalEntity extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_update_local_entity',
      'label' => t('Update local entity'),
      'parameter' => array(
        'remote_entity' => array(
          'type' => 'entity',
          'label' => t('Mite: Entity'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
        'account' => static::getServiceParameterInfo(),
        'local_entity' => array(
          'type' => 'entity',
          'label' => t('Local: Entity'),
          'wrapped' => FALSE,
          'required' => FALSE,
        ),
      ),
    );
  }

  /**
   * Executes the action.
   */
  public function execute($remote_entity, MiteAccountInterface $account, $local_entity) {
    $controller = entity_get_controller($remote_entity->entityType());
    
    $controller->updateLocal($remote_entity, $local_entity);
  }
}
