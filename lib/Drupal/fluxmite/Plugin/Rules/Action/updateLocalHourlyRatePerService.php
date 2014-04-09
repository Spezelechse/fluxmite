<?php

/**
 * @file
 * Contains updateLocalHourlyRatePerService.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Plugin\Entity\MiteCustomer;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * update local hourly rate per service field collection.
 */
class updateLocalHourlyRatePerService extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_update_local_hourly_rate_per_service',
      'label' => t('Update local hourly rate per service'),
      'parameter' => array(
        'host_entity' => array(
          'type' => 'entity',
          'label' => t('Host entity'),
          'required' => true,
          'wrapped' => false,
        ),
        'collection' => array(
          'type' => 'list<field_collection_item>',
          'label' => t('Field collection'),
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
    //print_r("<br>update field collection<br>");
    $service_field='field_'.$service_field;
    $rate_field='field_'.$rate_field;
    $host_entity;

    $field_collection_name=explode(':',$settings['collection:select']);
    $field_collection_name=str_replace('-','_',$field_collection_name[count($field_collection_name)-1]);
    
    $check_list=array();
    
    foreach($collection as $key => $item){
      $host_entity=$item->hostEntity();
      $item_wrapper=entity_metadata_wrapper($item->entityType(), $item);
      
      $service_id=$item_wrapper->$service_field->value()->field_mite_id['und'][0]['value'];

      $check_list[$service_id]=array($item, $key);
    }

    $count=$remote_entity->hourly_rates_per_service->count();

    for($i=0; $i<$count; $i++){
      $rate=$remote_entity->hourly_rates_per_service[$i]->value();

      //update
      if(isset($check_list[$rate['service_id']])){
        $item=$check_list[$rate['service_id']][0];
        
        $item->{$rate_field}['und'][0]['value']=$rate['hourly_rate'];

        $item->save(TRUE);

        unset($check_list[$rate['service_id']]);
      }
      //create
      else{
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

    //delete from collection
    foreach ($check_list as $rate) {
      unset($host_entity->{$field_collection_name}['und'][$rate[1]]);
    }
  }
}
