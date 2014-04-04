<?php

/**
 * @file
 * Contains setLocalHourlyRatePerService.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * set local hourly rate per service field collection.
 */
class setLocalHourlyRatePerService extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_set_local_hourly_rate_per_service',
      'label' => t('Set local hourly rate per service'),
      'parameter' => array(
        'host_entity' => array(
          'type' => 'entity',
          'label' => t('Host entity'),
          'required' => true,
          'wrapped' => false,
        ),
        'collection' => array(
          'type' => 'list<field_collection_item>',
          'label' => t('Local collection'),
          'required' => TRUE,
        ),
        'rate_field' => array(
          'type' => 'text',
          'label' => t('Rate field name'),
          'required' => TRUE,
        ),
        'service_field' => array(
          'type' => 'text',
          'label' => t('Service id field name'),
          'required' => TRUE,
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
  public function execute($host, $collection, $rate_field, $service_field, $remote_entity, $settings, $state) {
    print_r("<br>set field collection<br>");
    $service_field='field_'.$service_field;
    $rate_field='field_'.$rate_field;
    
    $field_collection_name=explode(':',$settings['collection:select']);
    $field_collection_name=str_replace('-','_',$field_collection_name[count($field_collection_name)-1]);
    

    $count=$remote_entity->hourly_rates_per_service->count();

    for($i=0; $i<$count; $i++){
      $rate=$remote_entity->hourly_rates_per_service[$i]->value();

      $item = entity_create('field_collection_item',array('field_name'=>$field_collection_name));
      $item->setHostEntity($host->entityType(), $host);

      $item->{$rate_field}['und'][0]['value']=$rate['hourly_rate'];

      $res=db_select('fluxmite','fm')
        ->fields('fm',array('id','type','remote_type','mite_id'))
        ->condition('fm.mite_id',$rate['service_id'],'=')
        ->condition('fm.remote_type','fluxmite_service','=')
        ->execute()
        ->fetchAssoc();

      if($res){
        $ref_id=$res['id'];
      }
      else{
        $ref_id=0;
        //TODO: throw error
      }

      $item->{$service_field}['und'][0]['target_id']=$ref_id;

      $item->save(TRUE);
    }
  }
}
