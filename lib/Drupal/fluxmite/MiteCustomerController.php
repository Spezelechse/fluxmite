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
      watchdog("post_customer", $entity->name);
    }
    else{
      watchdog("put_customer", $entity->name);
    }
  }

  public function deleteFromService($ids, FluxEntityInterface $agent){

  }

  public function loadLocal(RemoteEntityInterface $entity){
    watchdog("load_local", $entity->name);
  }

  public function createLocal(RemoteEntityInterface $entity){
    watchdog("create_local", $entity->name);
  }

  public function updateLocal(RemoteEntityInterface $entity){
    watchdog("update_local", $entity->name);
  }

  public function deleteLocal(RemoteEntityInterface $entity){
    watchdog("delete_local", $entity->name);
  }
}
