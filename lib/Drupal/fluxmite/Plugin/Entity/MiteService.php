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
class MiteService extends MiteEntityBase implements MiteServiceInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_service',
      'label' => t('Mite (remote): Service'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteServiceController',
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
      'description' => t("Service id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("Service name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Service note."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['billable'] = array(
      'label' => t('Billable'),
      'description' => t("Service billable."),
      'type' => 'boolean',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly_rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Service hourly-rate."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Service archived."),
      'type' => 'boolean',
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