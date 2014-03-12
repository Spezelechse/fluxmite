<?php

/**
 * @file
 * Contains fetchRemoteEntityById.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * update remote entities.
 */
class fetchRemoteEntityById extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_fetch_remote_entity_by_id',
      'label' => t('Fetch remote entity by id'),
      'parameter' => array(
        'remote_entity_id' => array(
          'type' => 'text',
          'label' => t('Remote id'),
          'required' => TRUE,
        ),
        'remote_entity_type' => array(
          'type' => 'text',
          'options list' => 'rules_entity_action_type_options',
           // Add the entity-type for the options list callback.
          //'options list entity type' => $type,
          'label' => t('Remote entity type'),
          'restriction' => 'input',
          'required' => TRUE,
        ),
      ),
      'provides' => array(
        'entity_fetched' => array('type'=>'entity','label' => t('Fetched entity')),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($id, $type) {
    $remote_entity=entity_load_single($type, $id);

    return array('entity_fetched' => entity_metadata_wrapper($type,$remote_entity));   
  }
}
