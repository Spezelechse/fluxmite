<?php

/**
 * @file
 * Contains setRemoteHourlyRatePerService.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * set remote hourly rate per service field collection.
 */
class setRemoteHourlyRatePerService extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_set_remote_hourly_rate_per_service',
      'label' => t('Set remote hourly rate per service'),
      'parameter' => array(
        'collection' => array(
          'type' => 'list<field_collection_item>',
          'label' => t('Collection'),
          'required' => TRUE,
        ),
        'rate_field' => array(
          'type' => 'text',
          'label' => t('Rate field name'),
          'required' => TRUE,
        ),
        'service_id_field' => array(
          'type' => 'text',
          'label' => t('Service id field name'),
          'required' => TRUE,
        ),
        'local_service_entity_type' => array(
          'type' => 'text',
          'label' => t('Local service entity type'),
          'options list' => 'rules_entity_action_type_options',
          'description' => t('Specifies the type of referenced service entity.'),
          'restriction' => 'input',
        ),
        'remote_entity' => array(
          'type' => 'entity',
          'label' => t('Remote entity'),
          'required' => TRUE,
          'wrapped' => TRUE,
        ),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($collection, $rate_field, $service_id_field, $local_service_type, $remote_entity) {
    print_r("<br>set remote hourly rate of service<br>");
    $service_id_field='field_'.$service_id_field;
    $rate_field='field_'.$rate_field;    
    $json="";

    foreach ($collection as $value) {
      $service=entity_metadata_wrapper($local_service_type, $value->{$service_id_field}['und'][0]['target_id']);

      $json.='{"service-id":"'.$service->field_mite_id->value().'","hourly-rate":"'.$value->{$rate_field}['und'][0]['value'].'"},';
    }

    $json='['.substr($json, 0,-1).']';

    $remote_entity->hourly_rates_per_service_json=$json;
  }
}
