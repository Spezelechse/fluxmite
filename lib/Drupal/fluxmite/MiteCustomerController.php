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
      $remoteEntities = fluxservice_entify_multiple(array($response), $entity->entityType(), $account);

      $this->createLocal(array_shift($remoteEntities), $local_entity);
  }
  private function createRequestString(RemoteEntityInterface $entity){
    $properties=$entity->getEntityPropertyInfo("","");
    $type=$entity->entityType();
    $type=explode("_",$type);
    $req="";

    foreach ($properties as $key => $value) {
      $value=$entity->getValueOf($key);

      if(isset($value)){
        $req.="<".$key.">".$value."</".$key.">"; 
      }
    }

    $req="<".$type[1].">".$req."</".$type[1].">";

    return $req;
  }

  public function deleteFromService($ids, FluxEntityInterface $agent){

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

      watchdog("create_local", $entity->name." (".$nid.")");
    }
  }

  public function updateLocal(RemoteEntityInterface $entity, $local_entity){
    $info=$entity->getInfo();
    if($info['name']=="fluxmite_customer"){

      $fields=array('updated_at'=>$this->prepareDate($entity->getValueOf('updated-at')));

      db_update('fluxmite')->fields($fields)->condition('remote_id', $entity->id, '=')->execute();

      watchdog("update_local", $entity->name);
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
