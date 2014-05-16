<?php

/**
 * @file
 * Contains MiteControllerBase.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice_extension\RemoteEntityControllerExtended;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Class RemoteEntityController
 */
abstract class MiteControllerBase extends RemoteEntityControllerExtended {
  /**
   *  Builds an xml request string for the given entity
   */
  public function createRequest($client, $operation_type, $remote_entity=null, $remote_id=0){
    $properties=$remote_entity->getEntityPropertyInfo("","");
    $req=array('api_key'=>$client->getConfig('access_token'));

    //extract mite type
    $type=$this->extractRemoteType($remote_entity->entityType());
    
    if($operation_type=='get'){
      if(isset($remote_id)){
        $req['id']=(int)$remote_id;
      }
    }
    else if(isset($remote_entity)){
      //generate a xml element for every set entity property
      foreach ($properties as $key => $value) {
        if($key=='id'||$key=='updated_at'||$key=='created_at'||$key=='hourly_rates_per_service'||$key=='remote_id'){
          continue;
        }

        if(isset($remote_entity->$key)){
          $value=$remote_entity->$key;
          $data_type='';

          //special for dates, arrays and boolean
          if($key=='date_at'){
            if(isset($value)){
              $value=date('Y-m-d',$value);
            }
            else{
              $value=date('Y-m-d');
            }
          }
          else if($key=='since'){
            if(isset($value)){
              $value=date('Y-m-d\TH:m:sP',$value);
            }
            else{
              $value=date('Y-m-d\TH:m:sP');
            }
          }
          else if($key=='hourly_rates_per_service_json'){
            $key='hourly_rates_per_service';
            if(isset($value)&&$value!=""){
              //build array from json
              $buffer=json_decode($value);

              if(gettype($buffer)!='array'){
                $buffer=array($buffer);
              }

              $value='';
              foreach ($buffer as $rate) {
                $value.='<hourly-rate-per-service>';
                  $value.='<service-id type="integer">'.$rate->{"service-id"}.'</service-id>';
                  $value.='<hourly-rate type="integer">'.$rate->{"hourly-rate"}.'</hourly-rate>';
                $value.='</hourly-rate-per-service>';
              }
              $data_type=' type="array"';
            }
          }
          else if($key=='billable'||$key=='archived'||$key=='locked'){
            $value=($value==0 ? 'false' : 'true');
          }

          $key=str_replace('_', '-', $key);
          $data.="<".$key."".$data_type.">".$value."</".$key.">"; 
        }
      }

      $data.="<force>true</force>";

      $data="<".$type.">".$data."</".$type.">";
      $req['data']=$req;

      if(isset($remote_entity->remote_id)){
        $req['id']=(int)$remote_entity->remote_id;
      }
    }
    
    return $req;
  }

  /**
   * 
   */
  public function handle404($log_message, $data=array(), $response_message=""){
    $document_not_found_de="Der Datensatz ist nicht vorhanden";
    $document_not_found_eng="The record does not exist";

    if($data!=array()&&!strpos($response_message,$document_not_found_eng)&&!strpos($response_message,$document_not_found_de)){
      MiteTaskQueue::addTask($data);
      watchdog('Fluxmite',$log_message);
      return false;
    }
    else{
      return true;
    }
  }

  /**
   * 
   */
  public function extractRemoteType($entity_type){
    $type_split=explode("_",$entity_type);
    $type=$type_split[1];

    if($type=='time'){
        $type.='_'.$type_split[2];
    }
    return $type;
  }

  /**
   * 
   */
  public function prepareResponse($response){
    return json_decode(json_encode($response),1);
  }

  public function addAdditionalFields(&$fields, &$values, $remote_entity){}
}
