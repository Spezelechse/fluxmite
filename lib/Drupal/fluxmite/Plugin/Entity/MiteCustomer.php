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
class MiteCustomer extends RemoteEntity implements MiteCustomerInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_customer',
      'label' => t('Mite: Customer'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'base table' => 'fluxmite',
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
    $info['hourly-rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Customer hourly-rate."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['active-hourly-rate'] = array(
      'label' => t('Active-hourly-rate'),
      'description' => t("Customer active-hourly-rate."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly-rates-per-service'] = array(
      'label' => t('Hourly-rates-per-service'),
      'description' => t("Customer hourly-rates-per-service."),
      'type' => 'array',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['created-at'] = array(
      'label' => t('Created-at'),
      'description' => t("Date which the Account was created"),
      'type' => 'date',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['updated-at'] = array(
      'label' => t('Updated-at'),
      'description' => t("Date of the last update"),
      'type' => 'date',
      'setter callback' => 'entity_property_verbatim_set',
    );
    return $info;
  }

  public function getValueOf($value=""){
    return $this->{$value};
  }

  public function getFields(){
    return $this->entityInfo['schema_fields_sql']['base table'];
  }

  public function type(){
    return $this->entityType();
  }
}