<?php

/**
 * @file
 * Contains MiteCustomer.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice\Entity\RemoteEntity;

/**
 * Entity class for Mite Customers.
 */
class MiteEntityBase extends RemoteEntity implements MiteEntityBaseInterface {
	public function __construct(array $values = array(), $entity_type = NULL) {
		if(isset($values)&&$values!=array()){
	   		$keys=array_keys($values);
	   		
	   		array_walk($keys, function(&$value, $key){
	   			$value=str_replace('-', '_', $value);
	   		});

	   		$values=array_combine($keys, $values);
	   		$id=explode(':', $values['id']);
	   		$this->mite_id=$id[2];

	   		if(isset($values['billable'])){
	   			$values['billable']=($values['billable']=='false'?0:1);
	   		}

	   		if(isset($values['locked'])){
	   			$values['locked']=($values['locked']=='false'?0:1);
	   		}

	   		if(isset($values['archived'])){
	   			$values['archived']=($values['archived']=='false'?0:1);
	   		}

	   		if(isset($values['note'])&&gettype($values['note'])=='array'){
	   			$values['note']="";
	   		}

	   		if(isset($values['hourly_rate'])&&gettype($values['hourly_rate'])=='array'){
	   			$values['hourly_rate']=0;
	   		}

	   		if(isset($values['active_hourly_rate'])&&gettype($values['active_hourly_rate'])=='array'){
	   			$values['active_hourly_rate']="";
	   		}
	   		
	   		if(isset($values['customer_id'])&&gettype($values['customer_id'])=='array'){
	   			$values['customer_id']=0;
	   		}
	   		
	   		if(isset($values['project_id'])&&gettype($values['project_id'])=='array'){
	   			$values['project_id']=0;
	   		}

	   		if(isset($values['service_id'])&&gettype($values['service_id'])=='array'){
	   			$values['service_id']=0;
	   		}
   		}

   		parent::__construct($values, $entity_type);
   	}
}