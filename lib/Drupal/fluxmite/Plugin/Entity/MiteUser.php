<?php

/**
 * @file
 * Contains MiteUser.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite users.
 */
class MiteUser extends RemoteEntity implements MiteUserInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_user',
      'label' => t('Mite: User'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteUserController',
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
      'description' => t("User id."),
      'type' => 'integer',
    );
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("User name"),
      'type' => 'text',
    );
    $info['email'] = array(
      'label' => t('Email'),
      'description' => t("User email"),
      'type' => 'text',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("User note"),
      'type' => 'text',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("true if archived, false if not"),
      'type' => 'boolean',
    ); 
    $info['role'] = array(
      'label' => t('Role'),
      'description' => t("User role"),
      'type' => 'text',
    );
    $info['language'] = array(
      'label' => t('language'),
      'description' => t("User language"),
      'type' => 'text',
    );
    $info['created-at'] = array(
      'label' => t('Created-at'),
      'description' => t("Date which the user was created"),
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