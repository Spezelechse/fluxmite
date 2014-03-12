<?php

/**
 * @file
 * Contains MiteCustomerPostedTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

use \PDO;
/**
 * Event dispatcher for changed mite customers.
 */
class MiteCustomerTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() { 
    $account = $this->getAccount();

  	$customers = $account->client()->getCustomers(array('api_key' => $account->getAccessToken()));

    dpm($customers);

    $customers = json_decode(json_encode($customers),1);

    $create=array();
    $update=array();
    $update_entities=array();
    $delete=array();

    foreach($customers['customer'] as $customer){
      $res=db_query("SELECT updated_at, id, type FROM {fluxmite} WHERE remote_id LIKE :id", array(':id'=>'%'.$customer['id']));
      $res=$res->fetchAssoc();
      
      if($res){
        //check for updates
        $remote=date_create_from_format("Y-m-d?H:i:sP", $customer['updated-at']);
        $remote=date_format($remote, "Y-m-d H:i:s");
        
        if($res['updated_at']<$remote){
          array_push($update, $customer);
          array_push($update_entities, $res['id']);
        }
      }
      else{
        array_push($create, $customer);
      }
    }

    $this->invokeEvent('fluxmite_customer', $create, $account, 'create');
    $this->invokeEvent('fluxmite_customer', $update, $account, 'update', $update_entities);
    $this->invokeEvent('fluxmite_customer', $delete, $account, 'delete');
     
  }
}