<?php

/**
 * @file
 * Contains MiteService.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Services.
 */
class MiteService extends RemoteEntity implements MiteServiceInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_service',
      'label' => t('Mite: Service'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteServiceController',
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
      'description' => t("Service id."),
      'type' => 'integer',
    );
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("Service name."),
      'type' => 'text',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Service note."),
      'type' => 'text',
    );
    $info['billable'] = array(
      'label' => t('Billable'),
      'description' => t("Service billable."),
      'type' => 'boolean',
    );
    $info['hourly-rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Service hourly-rate."),
      'type' => 'integer',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Service archived."),
      'type' => 'boolean',
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
}