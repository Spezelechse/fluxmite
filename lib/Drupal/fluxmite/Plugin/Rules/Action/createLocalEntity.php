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
        'account' => static::getServiceParameterInfo(),
        'remote_entity' => array(
          'type' => 'entity',
          'label' => t('Mite: Entity'),
          'wrapped' => FALSE,
          'required' => TRUE,
        ),
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
  public function execute(MiteAccountInterface $account, $remote_entity, $local_entity) {
    dpm("create local");
    print_r("create local<br>");

    $controller = entity_get_controller($remote_entity->entityType());
    
    $controller->createLocal($remote_entity, $local_entity->id, $local_entity->entityType());
  }
}
