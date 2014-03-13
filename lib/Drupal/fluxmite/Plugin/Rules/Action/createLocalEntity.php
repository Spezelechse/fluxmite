<?php

/**
 * @file
 * Contains createLocalEntity.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * Create local entiy.
 */
class createLocalEntity extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_create_local_entity',
      'label' => t('Create local entity'),
      'parameter' => array(
        'remote_entity' => array(
          'type' => '*',
          'label' => t('Mite: Entity'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
        'account' => static::getServiceParameterInfo(),
        'local_entity' => array(
          'type' => '*',
          'label' => t('Local: Entity'),
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
    
    $controller->createLocal($remote_entity, $local_entity);
  }
}
