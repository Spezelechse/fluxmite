<?php

/**
 * @file
 * Contains MiteProject.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Projects.
 */
class MiteProject extends MiteEntityBase implements MiteProjectInterface {
  public function __construct(array $values = array(), $entity_type = NULL) {
    parent::__construct($values, $entity_type);

    //case: active_hourly_rate = default
    if(isset($this->active_hourly_rate)&&isset($this->active_hourly_rate)&&gettype($this->active_hourly_rate)=='array'){
      $this->active_hourly_rate='nil';
    }

    
    if(isset($this->hourly_rates_per_service)){
      $rates=$this->hourly_rates_per_service;
      //serialize hourly_rates_per_service
      if(isset($rates['hourly-rate-per-service'])){
        $this->hourly_rates_per_service=json_decode(str_replace('-', '_', json_encode($rates['hourly-rate-per-service'])),1);
        if(!isset($this->hourly_rates_per_service[0])){
          $this->hourly_rates_per_service=array($this->hourly_rates_per_service);
        }
      }
      else{
        $this->hourly_rates_per_service=array();
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
      'name' => 'fluxmite_project',
      'label' => t('Mite (remote): Project'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteProjectController',
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
      'description' => t("Project id."),
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
      'description' => t("Project name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Project note."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['budget'] = array(
      'label' => t('Budget'),
      'description' => t("Project budget."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['budget_type'] = array(
      'label' => t('Budget-type'),
      'description' => t("Project budget-type."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Project archived."),
      'type' => 'boolean',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['customer_id'] = array(
      'label' => t('Customer-id'),
      'description' => t("Project customer-id."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['customer_name'] = array(
      'label' => t('Customer-name'),
      'description' => t("Project customer-name."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly_rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Project hourly-rate."),
      'type' => 'integer',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['active_hourly_rate'] = array(
      'label' => t('Active-hourly-rate'),
      'description' => t("Project active-hourly-rate."),
      'type' => 'text',
      'setter callback' => 'entity_property_verbatim_set',
    );
    $info['hourly_rates_per_service'] = array(
      'label' => t('Hourly-rates-per-service'),
      'description' => t("Project hourly-rates-per-service."),
      'type' => 'list<struct>',
      'setter callback' => 'entity_property_verbatim_set',
      'property info' => array(
        'service_id' => array(
          'label' => t('Mite service id'),
          'description' => t('Mite service id'),
          'type' => 'integer',
          'setter callback' => 'entity_property_verbatim_set',
        ),
        'hourly_rate' => array(
          'label' => t('Hourly-rate'),
          'description' => t('Hourly-rate for service'),
          'type' => 'integer',
          'setter callback' => 'entity_property_verbatim_set',
        )        
      )
    );
    $info['hourly_rates_per_service_json'] = array(
      'label' => t('Hourly-rates-per-service json'),
      'description' => t("Hourly rates per service json string."),
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