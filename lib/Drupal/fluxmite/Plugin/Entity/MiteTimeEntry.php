<?php

/**
 * @file
 * Contains MiteTimeEntry.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Time entries.
 */
class MiteTimeEntry extends MiteEntityBase implements MiteTimeEntryInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_time_entry',
      'label' => t('Mite (remote): Time entry'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteTimeEntryController',
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
      'description' => t("Time id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['date_at'] = array(
      'label' => t('Date-at'),
      'description' => t("Time date-at."),
      'type' => 'date',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['date_at_timestamp'] = array(
      'label' => t('Date-at timestamp'),
      'description' => t("Time date-at as timestamp."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['minutes'] = array(
      'label' => t('Minutes'),
      'description' => t("Time minutes."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['revenue'] = array(
      'label' => t('Revenue'),
      'description' => t("Time revenue."),
      'type' => 'float',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly_rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Time hourly-rate."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['billable'] = array(
      'label' => t('Billable'),
      'description' => t("Time billable."),
      'type' => 'boolean',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Time note."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['user_id'] = array(
      'label' => t('User-id'),
      'description' => t("Time user-id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['user_name'] = array(
      'label' => t('User-name'),
      'description' => t("Time user-name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['project_id'] = array(
      'label' => t('Project-id'),
      'description' => t("Time project-id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['project_name'] = array(
      'label' => t('Project-name'),
      'description' => t("Time project-name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['service_id'] = array(
      'label' => t('Service-id'),
      'description' => t("Time service-id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['service_name'] = array(
      'label' => t('Service-name'),
      'description' => t("Time service-name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['customer_id'] = array(
      'label' => t('Customer-id'),
      'description' => t("Time customer-id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['customer_name'] = array(
      'label' => t('Customer-name'),
      'description' => t("Time Customer-name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['locked'] = array(
      'label' => t('Locked'),
      'description' => t("Time locked."),
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