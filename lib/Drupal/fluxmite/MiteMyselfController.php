<?php

/**
 * @file
 * Contains MiteMyselfController.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\Entity\RemoteEntityInterface;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Class RemoteEntityController
 */
class MiteMyselfController extends MiteControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function loadFromService($ids, FluxEntityInterface $agent) {
    $output = array();
    $ids=array_values($ids);
    $client = $agent->client();
    
    try{
	    foreach ($ids as $id) {
	      if($response=$client->getMyself(array('id'=>(int)$id, 'api_key'=>$client->getConfig('access_token')))){

	        $search=array_keys($this->miteSpecialFields());
	        $replace=array_values($this->miteSpecialFields());

	        $output[$id]=json_decode(str_replace($search,$replace,json_encode($response)), 1);
	      }
	    }
	}
  	catch(BadResponseException $e){
       if($e->getResponse()->getStatusCode()==404){
         $this->handle404('[404] Host "'.$client->getBaseUrl().'" not found (getMyself)');
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
}
