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
class MiteAccount extends RemoteEntity implements MiteAccountInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_account',
      'label' => t('Mite: Account'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteAccountController',
      'label callback' => 'entity_class_label',
      'entity keys' => array(
        'id' => 'drupal_entity_id',
        'remote id' => 'id',
      ),
    );
  }

  /**
   * Gets the entity property definitions.
   */
  public static function getEntityPropertyInfo($entity_type, $entity_info) {
    $info['id'] = array(
      'label' => t('Id'),
      'description' => t("Account id."),
      'type' => 'integer',
    );

    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("Account name"),
      'type' => 'text',
    );

    $info['title'] = array(
      'label' => t('Title'),
      'description' => t("Account title"),
      'type' => 'text',
    );

    $info['currency'] = array(
      'label' => t('Currency'),
      'description' => t("Account currency"),
      'type' => 'text',
    );

    $info['created-at'] = array(
      'label' => t('Created-at'),
      'description' => t("Date which the Account was created"),
      'type' => 'date',
    );
    $info['updated-at'] = array(
      'label' => t('Updated-at'),
      'description' => t("Date of the last update"),
      'type' => 'date',
    );
 
    return $info;
  }
  
  public function toString(){
    return "[".$this->id."] ".$this->name;
  }
}