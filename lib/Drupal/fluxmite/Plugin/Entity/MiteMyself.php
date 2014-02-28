<?php

/**
 * @file
 * Contains MiteMyself.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Myselfs.
 */
class MiteMyself extends RemoteEntity implements MiteMyselfInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_myself',
      'label' => t('Mite: Myself'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteMyselfController',
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
      'description' => t("Myself id."),
      'type' => 'integer',
    );
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("Myself name."),
      'type' => 'text',
    );
    $info['email'] = array(
      'label' => t('Email'),
      'description' => t("Myself email."),
      'type' => 'text',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Myself note."),
      'type' => 'text',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Myself archived."),
      'type' => 'boolean',
    );
    $info['role'] = array(
      'label' => t('Role'),
      'description' => t("Myself role."),
      'type' => 'text',
    );
    $info['language'] = array(
      'label' => t('Language'),
      'description' => t("Myself language."),
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