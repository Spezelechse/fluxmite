<?php

/**
 * @file
 * Contains MiteCustomerController.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\Entity\RemoteEntityInterface;

/**
 * Class RemoteEntityController
 */
class MiteCustomerController extends MiteControllerBase {

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
    }
  }
}
