<?php

/**
 * @file
 * Contains MiteTimeEntryController.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\Entity\RemoteEntityInterface;
use Drupal\fluxmite\Plugin\Rules\Event\MiteTimeEntryEventHandler;

/**
 * Class RemoteEntityController
 */
class MiteTimeEntryController extends MiteControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function loadFromService($ids, FluxEntityInterface $agent) {
    $output = array();
    $ids=array_values($ids);
    $client = $agent->client();
    
    foreach ($ids as $id) {
      if($response=$client->getTime_entry(array('id'=>(int)$id, 'api_key'=>$client->getConfig('access_token')))){

        $search=array_keys($this->miteSpecialFields());
        $replace=array_values($this->miteSpecialFields());

        $output[$id]=json_decode(str_replace($search,$replace,json_encode($response)), 1);
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