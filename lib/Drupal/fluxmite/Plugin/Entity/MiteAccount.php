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
    $info['id'] = array(
      'label' => t('Id'),
      'description' => t("Account id."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    
    $info['mite_id'] = array(
      'label' => t('Mite id'),
      'description' => t("Mite id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );

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

    $info['created_at'] = array(
      'label' => t('Created-at'),
      'description' => t("Date which the Account was created"),
      'type' => 'date',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['updated_at'] = array(
      'label' => t('Updated-at'),
      'description' => t("Date of the last update"),
      'type' => 'date',
      'setter callback' => 'entity_property_verbatim_set',
    );
 
    return $info;
  }
}