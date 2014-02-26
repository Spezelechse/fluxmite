<?php

/**
 * @file
 * Contains MiteTime.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Times.
 */
class MiteTime extends RemoteEntity implements MiteTimeInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_time',
      'label' => t('Mite: Time'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteTimeController',
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
      'description' => t("Time id."),
      'type' => 'integer',
    );
    $info['date-at'] = array(
      'label' => t('Date-at'),
      'description' => t("Time date-at."),
      'type' => 'date',
    );
    $info['minutes'] = array(
      'label' => t('Minutes'),
      'description' => t("Time minutes."),
      'type' => 'integer',
    );
    $info['revenue'] = array(
      'label' => t('Revenue'),
      'description' => t("Time revenue."),
      'type' => 'float',
    );
    $info['hourly-rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Time hourly-rate."),
      'type' => 'integer',
    );
    $info['billable'] = array(
      'label' => t('Billable'),
      'description' => t("Time billable."),
      'type' => 'boolean',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Time note."),
      'type' => 'text',
    );
    $info['user-id'] = array(
      'label' => t('User-id'),
      'description' => t("Time user-id."),
      'type' => 'integer',
    );
    $info['user-name'] = array(
      'label' => t('User-name'),
      'description' => t("Time user-name."),
      'type' => 'text',
    );
    $info['project-id'] = array(
      'label' => t('Project-id'),
      'description' => t("Time project-id."),
      'type' => 'integer',
    );
    $info['project-name'] = array(
      'label' => t('Project-name'),
      'description' => t("Time project-name."),
      'type' => 'text',
    );
    $info['service-id'] = array(
      'label' => t('Service-id'),
      'description' => t("Time service-id."),
      'type' => 'integer',
    );
    $info['service-name'] = array(
      'label' => t('Service-name'),
      'description' => t("Time service-name."),
      'type' => 'text',
    );
    $info['customer-id'] = array(
      'label' => t('Customer-id'),
      'description' => t("Time customer-id."),
      'type' => 'integer',
    );
    $info['cutsomer-name'] = array(
      'label' => t('Customer-name'),
      'description' => t("Time Customer-name."),
      'type' => 'text',
    );
    $info['locked'] = array(
      'label' => t('Locked'),
      'description' => t("Time locked."),
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