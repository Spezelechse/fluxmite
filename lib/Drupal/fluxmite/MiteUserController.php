<?php

/**
 * @file
 * Contains MiteUserController.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\Entity\RemoteEntityInterface;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Class RemoteEntityController
 */
class MiteUserController extends MiteControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function loadFromService($ids, FluxEntityInterface $agent) {
    $output = array();
    $ids=array_values($ids);
    $client = $agent->client();
    
    try{
	    foreach ($ids as $id) {
	      if($response=$client->getUser(array('id'=>(int)$id, 'api_key'=>$client->getConfig('access_token')))){

	        $output[$id]=json_decode(json_encode($response), 1);
	      }
	    }
    }
  	catch(BadResponseException $e){
       if($e->getResponse()->getStatusCode()==404){
         $this->handle404('[404] Host "'.$client->getBaseUrl().'" not found (getUser)');
       }
       else{
       }
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  protected function sendToService(RemoteEntityInterface $entity) {
    // @todo Throw exception.
  }

  public function loadRemote($id, $agent){
    $remote=$this->loadFromService(array($id), $agent);
    return fluxservice_entify($remote[$id], 'fluxmite_user', $agent);
  }
}
