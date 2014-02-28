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
class MiteProject extends RemoteEntity implements MiteProjectInterface {

  /**
   * Defines the entity type.
   *
   * This gets exposed to hook_entity_info() via fluxservice_entity_info().
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite_project',
      'label' => t('Mite: Project'),
      'module' => 'fluxmite',
      'service' => 'fluxmite',
      'controller class' => '\Drupal\fluxmite\MiteProjectController',
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
      'description' => t("Project id."),
      'type' => 'integer',
    );
    $info['name'] = array(
      'label' => t('Name'),
      'description' => t("Project name."),
      'type' => 'text',
    );
    $info['note'] = array(
      'label' => t('Note'),
      'description' => t("Project note."),
      'type' => 'text',
    );
    $info['budget'] = array(
      'label' => t('Budget'),
      'description' => t("Project budget."),
      'type' => 'integer',
    );
    $info['budget-type'] = array(
      'label' => t('Budget-type'),
      'description' => t("Project budget-type."),
      'type' => 'text',
    );
    $info['archived'] = array(
      'label' => t('Archived'),
      'description' => t("Project archived."),
      'type' => 'boolean',
    );
    $info['customer-id'] = array(
      'label' => t('Customer-id'),
      'description' => t("Project customer-id."),
      'type' => 'integer',
    );
    $info['customer-name'] = array(
      'label' => t('Customer-name'),
      'description' => t("Project customer-name."),
      'type' => 'text',
    );
    $info['hourly-rate'] = array(
      'label' => t('Hourly-rate'),
      'description' => t("Project hourly-rate."),
      'type' => 'integer',
    );
    $info['active-hourly-rate'] = array(
      'label' => t('Active-hourly-rate'),
      'description' => t("Project active-hourly-rate."),
      'type' => 'text',
    );
    $info['hourly-rates-per-service'] = array(
      'label' => t('Hourly-rates-per-service'),
      'description' => t("Project hourly-rates-per-service."),
      'type' => 'array',
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