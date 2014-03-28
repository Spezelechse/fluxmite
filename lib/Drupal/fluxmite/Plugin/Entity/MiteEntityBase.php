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
   		}

   		parent::__construct($values, $entity_type);
   	}
}