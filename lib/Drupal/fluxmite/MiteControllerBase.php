<?php

/**
 * @file
 * Contains MiteControllerBase.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\RemoteEntityController;
use Drupal\fluxservice\Entity\RemoteEntityInterface;

/**
 * Class RemoteEntityController
 */
abstract class MiteControllerBase extends RemoteEntityController {

/**
 * Creates a new database entry
 */
  public function createLocal(RemoteEntityInterface $entity, $local_entity){
    $mite_id=explode(':', $entity->id);
    $mite_id=$mite_id[2];

    $fields=array('id', 'type', 'remote_id', 'remote_type', 'mite_id', 'touched_last', 'created_at', 'updated_at');
    $values=array($local_entity->id, 
                  $local_entity->entityType(), 
                  $entity->id, 
                  $entity->entityType(), 
                  $mite_id,
                  time(),
                  strtotime($entity->created_at), 
                  strtotime($entity->updated_at));

    $nid=db_insert('fluxmite')->fields($fields)->values($values)->execute();
  }

/**
 *    Sends a post request to create a new mite data set of the given type
 */
  public function createRemote(RemoteEntityInterface $entity, $local_entity, $account){
      $req=$this->createRequestString($entity);

      //extract mite type
      $type=$entity->entityType();
      $type_split=explode("_",$type);
      $type=$type_split[1];
      $type=ucfirst($type);

      if($type=='Time'){
        $type.='_'.$type_split[2];
      }

      $client=$account->client();

      //send the post request
      $operation='post'.$type;
      $response=$client->$operation(array('data' => $req, 'api_key'=>$client->getConfig('access_token')));

      //generate an array from xml
      $search=array_keys($this->miteSpecialFields());
      $replace=array_values($this->miteSpecialFields());

      $response = json_decode(str_replace($search,$replace,json_encode($response)),1);

      $remoteEntity = fluxservice_entify($response, $entity->entityType(), $account);

      //create local database entry
      $this->createLocal($remoteEntity, $local_entity);
  }

  /**
   *  Builds an xml request string for the given entity
   */
  private function createRequestString(RemoteEntityInterface $entity){
    $properties=$entity->getEntityPropertyInfo("","");

    //extract mite type
    $type=$entity->entityType();
    $type_split=explode("_",$type);
    $req="";

    $type=$type_split[1];
    if($type=='time'){
        $type.='-'.$type_split[2];
    }
      

    //generate a xml element for every set entity property
    foreach ($properties as $key => $value) {
      if($key=='id'||$key=='updated_at'||$key=='created_at'||$key=='date_at_timestamp'){
        continue;
      }

      if(isset($entity->$key)){
        $value=$entity->$key;
        
        //TODO: implement array datatypes

        //special for date types
        if($key=='date_at'){
          if(isset($value)){
            $value=date('Y-m-d',$entity->date_at_timestamp);
          }
          else{
            $avlue=date('Y-m-d');
          }
        }
        else if($key=='since'){
          if(isset($value)){
            $value=date('Y-m-d\TH:m:sP',$value);
          }
          else{
            $avlue=date('Y-m-d\TH:m:sP');
          }
        }

        if(getType($value)!='array'){
          $req.="<".$key.">".$value."</".$key.">"; 
        }
      }
    }

    $req="<".$type.">".$req."</".$type.">";

    $search=array_values($this->miteSpecialFields());
    $replace=array_keys($this->miteSpecialFields());

    $req=str_replace($search, $replace, $req);
    return $req;
  }

  public function deleteRemote($local, $account, $remote_type, $mite_id){
    $type_split=explode("_",$remote_type);
    $type=$type_split[1];

    if($type=='time'){
        $type.='_'.$type_split[2];
    }
      
    $client=$account->client();
    $operation='delete'.ucfirst($type);
    $response=$client->$operation(array(  'id' => (int)$mite_id,
                                          'api_key'=>$client->getConfig('access_token')));

    if($response['status']==200){
      $num=db_delete('fluxmite')->condition('id', $local->id)->condition('type', $local->entityType())->execute();

      if($num<=0){
        //TODO: throw exception
      }
    }
  }

  /**
   *  Updates the local fluxmite table
   */
  public function updateLocal(RemoteEntityInterface $entity, $local_entity){
    $fields=array('updated_at'=>strtotime($entity->updated_at));

    db_update('fluxmite')->fields($fields)->condition('id', $local_entity->id, '=')->execute();
  }


  /**
  *   Sends a put request to update a mite data set and if successful updates the local table
  */
  public function updateRemote(RemoteEntityInterface $entity, $local_entity, $account){
    $req=$this->createRequestString($entity);

    //extract mite id
    $id=$entity->id;
    $id=explode(':', $id);
    $id=$id[2];

    //extract mite type
    $type=$entity->entityType();
    $type_split=explode("_",$type);
    $type=$type_split[1];
    $type=ucfirst($type);

    if($type=='Time'){
        $type.='_'.$type_split[2];
    }  

    $client=$account->client();

    //send the update request
    $operation='put'.$type;
    $response=$client->$operation(array( 'data' => $req, 
                                          'id' => (int)$id,
                                          'api_key'=>$client->getConfig('access_token')));

    //check if successful
    if($response['status']==200){
      //get the new updated-at timestamp
      $operation='get'.$type;
      $response=$client->$operation(array('id' => (int)$id,
                                          'api_key'=>$client->getConfig('access_token')));

      //generate an array from xml
      $search=array_keys($this->miteSpecialFields());
      $replace=array_values($this->miteSpecialFields());

      $response = json_decode(str_replace($search,$replace,json_encode($response)),1);

      $remoteEntity = fluxservice_entify($response, $entity->entityType(), $account);

      //update local database entry
      $this->updateLocal($remoteEntity, $local_entity); 
    }
  }

  /**
   * returns an assoc array with "special" fields from mite (which contain hyphens)
   * key = original
   * value = to use local
   */
  public function miteSpecialFields(){
    return array(
      'created-at' => 'created_at',
      'updated-at' => 'updated_at',
      'active-hourly-rate' => 'active_hourly_rate',
      'hourly-rates-per-service' => 'hourly_rates_per_service',
      'hourly-rate-per-service' => 'hourly_rate_per_service',
      'hourly-rate' => 'hourly_rate',
      'budget-type' => 'budget_type',
      'customer-id' => 'customer_id',
      'customer-name' => 'customer_name',
      'date-at' => 'date_at',
      'user-id' => 'user_id',
      'user-name' => 'user_name',
      'project-id' => 'project_id',
      'project-name' => 'project_name',
      'service-id' => 'service_id',
      'service-name' => 'service_name',
      'time-entry' => 'time_entry',
      );
  }
}
