<?php

/**
 * @file
 * Contains MiteAccount.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Accounts.
 */
class MiteAccount extends MiteEntityBase implements MiteAccountInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_account',
      'label' => t('Mite (remote): Account'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteAccountController',
      'label callback' => 'entity_class_label',
      'entity keys' => array(
        'id' => 'id',
        'remote id' => 'id',
      ),
    );
  }

  /**
   * Gets the entity property definitions.
   */
  public static function getEntityPropertyInfo($entity_type, $entity_info) {
    $info=parent::getEntityPropertyInfo($entity_type,$entity_info);
    
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("Account name"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );

    $info['title'] = array(
      'label' => t('Title'),
      'description' => t("Account title"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );

    $info['currency'] = array(
      'label' => t('Currency'),
      'description' => t("Account currency"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
 
    return $info;
  }
}