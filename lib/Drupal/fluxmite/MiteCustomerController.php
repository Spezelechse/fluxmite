<?php

/**
 * @file
 * Contains MiteCustomerController.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\RemoteEntityController;
use Drupal\fluxservice\Entity\RemoteEntityInterface;

/**
 * Class RemoteEntityController
 */
class MiteCustomerController extends RemoteEntityController {

  /**
   * {@inheritdoc}
   */
  protected function loadFromService($ids, FluxEntityInterface $agent) {
    $output = array();
    $ids=array_values($ids);
    $client = $agent->client();
    
    foreach ($ids as $id) {
      if($response=$client->getCustomer(array('id'=>(int)$id, 'api_key'=>$client->getConfig('access_token')))){
        $output[$id]=json_decode(json_encode($response), 1);
      }
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  protected function sendToService(RemoteEntityInterface $entity) {

    if($entity->isNew()){
    }
    else{
      watchdog("put_customer", $entity->name);
    }
  }

  public function createRemote(RemoteEntityInterface $entity, $local_entity, $account){
      $req=$this->createRequestString($entity);

      $client=$account->client();
      $response=$client->postCustomer(array('data' => $req, 'api_key'=>$client->getConfig('access_token')));

      $response = json_decode(json_encode($response),1);
      $remoteEntity = fluxservice_entify($response, $entity->entityType(), $account);

      $this->createLocal($remoteEntity, $local_entity);
  }
  private function createRequestString(RemoteEntityInterface $entity){
    $properties=$entity->getEntityPropertyInfo("","");
    $type=$entity->entityType();
    $type=explode("_",$type);
    $req="";

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

  public function updateRemote(RemoteEntityInterface $entity, $local_entity, $account){
    $req=$this->createRequestString($entity);

    $id=$entity->id;
    $id=explode(':', $id);
    $id=$id[2];

    $client=$account->client();
    $response=$client->putCustomer(array( 'data' => $req, 
                                          'id' => (int)$id,
                                          'api_key'=>$client->getConfig('access_token')));

    if($response['status']==200){
      $response=$client->getCustomer(array('id' => (int)$id,
                                          'api_key'=>$client->getConfig('access_token')));

      $response = json_decode(json_encode($response),1);
      $remoteEntity = fluxservice_entify($response, $entity->entityType(), $account);

      $this->updateLocal($remoteEntity, $local_entity); 
    }
  }

  public function loadLocal(RemoteEntityInterface $entity){
    watchdog("load_local", $entity->name);
  }

  public function createLocal(RemoteEntityInterface $entity, $local_entity){
    $info=$entity->getInfo();
    if($info['name']=="fluxmite_customer"){
      $fields=array('id', 'type', 'remote_id', 'remote_type', 'created_at', 'updated_at');
      $values=array($local_entity->id, 
                    $local_entity->entityType(), 
                    $entity->id, 
                    $entity->entityType(), 
                    $this->prepareDate($entity->getValueOf('created-at')), 
                    $this->prepareDate($entity->getValueOf('updated-at')));

      $nid=db_insert('fluxmite')->fields($fields)->values($values)->execute();

      $local_entity=entity_metadata_wrapper($local_entity->entityType(), $local_entity);
      $local_entity->field_remote_id->set($entity->id);
      $local_entity->save();
    }
  }

  public function updateLocal(RemoteEntityInterface $entity, $local_entity){
    $info=$entity->getInfo();
    if($info['name']=="fluxmite_customer"){
      $fields=array('updated_at'=>$this->prepareDate($entity->getValueOf('updated-at')));

      db_update('fluxmite')->fields($fields)->condition('remote_id', $entity->id, '=')->execute();
    }
  }

  public function deleteLocal(RemoteEntityInterface $entity){
    watchdog("delete_local", $entity->name);
  }

  private function prepareDate($date){
    $tmp=date_create_from_format("Y-m-d?H:i:sP", $date);
    return date_format($tmp, "Y-m-d H:i:s");
  }
}
