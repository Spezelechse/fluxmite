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
   		}

   		parent::__construct($values, $entity_type);
   	}
}