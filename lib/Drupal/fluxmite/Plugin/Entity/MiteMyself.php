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
class MiteMyself extends MiteEntityBase implements MiteMyselfInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_myself',
      'label' => t('Mite (remote): Myself'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteMyselfController',
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
      'description' => t("Myself name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['email'] = array(
      'label' => t('Email'),
      'description' => t("Myself email."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Myself note."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Myself archived."),
      'type' => 'boolean',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['role'] = array(
      'label' => t('Role'),
      'description' => t("Myself role."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['language'] = array(
      'label' => t('Language'),
      'description' => t("Myself language."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    return $info;
  }
}