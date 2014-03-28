<?php

/**
 * @file
 * Contains MiteCustomer.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Customers.
 */
class MiteCustomer extends MiteEntityBase implements MiteCustomerInterface {

  public function __construct(array $values = array(), $entity_type = NULL) {
    parent::__construct($values, $entity_type);

    if(isset($this->hourly_rates_per_service)){
      $rates=$this->hourly_rates_per_service;
      if(isset($rates['hourly-rate-per-service'])){
        $this->hourly_rates_per_service=json_encode($rates['hourly-rate-per-service']);
      }
      else{
        $this->hourly_rates_per_service="";
      }
    }
  }

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_customer',
      'label' => t('Mite (remote): Customer'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteCustomerController',
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
      'description' => t("Customer id."),
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
      'description' => t("Customer name."),
      'type' => 'text',
      'required' => TRUE,
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Customer note."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Customer archived."),
      'type' => 'boolean',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly_rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Customer hourly-rate."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['active_hourly_rate'] = array(
      'label' => t('Active-hourly-rate'),
      'description' => t("Customer active-hourly-rate."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly_rates_per_service'] = array(
      'label' => t('Hourly-rates-per-service'),
      'description' => t("Customer hourly-rates-per-service."),
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