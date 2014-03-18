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
class MiteUser extends RemoteEntity implements MiteEntityBaseInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_user',
      'label' => t('Mite (remote): User'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteUserController',
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
      'description' => t("User id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("User name"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['email'] = array(
      'label' => t('Email'),
      'description' => t("User email"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("User note"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("true if archived, false if not"),
      'type' => 'boolean',
      'setter callback' => 'entity_property_verbatim_set',
    ); 
    $info['role'] = array(
      'label' => t('Role'),
      'description' => t("User role"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['language'] = array(
      'label' => t('language'),
      'description' => t("User language"),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['created_at'] = array(
      'label' => t('Created-at'),
      'description' => t("Date which the user was created"),
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