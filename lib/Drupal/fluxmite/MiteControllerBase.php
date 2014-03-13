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
    $fields=array('id', 'type', 'remote_id', 'remote_type', 'created_at', 'updated_at');
    $values=array($local_entity->id, 
                  $local_entity->entityType(), 
                  $entity->id, 
                  $entity->entityType(), 
                  $this->prepareDate($entity->getValueOf('created-at')), 
                  $this->prepareDate($entity->getValueOf('updated-at')));

    $nid=db_insert('fluxmite')->fields($fields)->values($values)->execute();

    //update the local entity
    $local_entity=entity_metadata_wrapper($local_entity->entityType(), $local_entity);
    $local_entity->field_remote_id->set($entity->id);
    $local_entity->save();
  }

/**
 *    Sends a post request to create a new mite data set of the given type
 */
  public function createRemote(RemoteEntityInterface $entity, $local_entity, $account){
      $req=$this->createRequestString($entity);
      
      //extract mite type
      $type=$entity->entityType();
      $type=explode("_",$type);
      $type=$type[1];
      $type=ucfirst($type);

      $client=$account->client();

      //send the post request
      $operation='post'.$type;
      $response=$client->$operation(array('data' => $req, 'api_key'=>$client->getConfig('access_token')));

      //generate an array from xml
      $response = json_decode(json_encode($response),1);

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
    $type=explode("_",$type);
    $req="";

    //generate a xml element for every set entity property
    foreach ($properties as $key => $value) {
      if($key=='id'||$key=='updated-at'||$key=='created-at'){
        continue;
      }

      $value=$entity->getValueOf($key);
      
      //TODO: implement array datatypes

      if(isset($value)&&getType($value)!='array'){
        $req.="<".$key.">".$value."</".$key.">"; 
      }
    }

    $req="<".$type[1].">".$req."</".$type[1].">";

    return $req;
  }

  /**
   *  Builds a database timestamp out of the mite timestamp
   */
  private function prepareDate($date){
    $tmp=date_create_from_format("Y-m-d?H:i:sP", $date);
    return date_format($tmp, "Y-m-d H:i:s");
  }

  /**
   *  Updates the local fluxmite table
   */
  public function updateLocal(RemoteEntityInterface $entity, $local_entity){
    $fields=array('updated_at'=>$this->prepareDate($entity->getValueOf('updated-at')));

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
    $type=explode("_",$type);
    $type=$type[1];
    $type=ucfirst($type);

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
      $response = json_decode(json_encode($response),1);

      $remoteEntity = fluxservice_entify($response, $entity->entityType(), $account);

      //update local database entry
      $this->updateLocal($remoteEntity, $local_entity); 
    }
  }
}
