<?php

/**
 * @file
 * Contains MiteCustomerPostedTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

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

    $customers = json_decode(json_encode($customers),1);

  	if(!empty($customers)){

  		$customers = fluxservice_entify_multiple($customers['customer'], 'fluxmite_customer', $account);
      $change_type = array("create", "update", "delete");
      $i=0;

      foreach ($customers as $customer) {
       	rules_invoke_event($this->getEvent(), $account, $customer,$change_type[($i)%3]);
        $i++; 
      }
    }
  }
}