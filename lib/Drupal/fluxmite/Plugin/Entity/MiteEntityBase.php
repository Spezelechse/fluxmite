<?php

/**
 * @file
 * Contains MiteCustomer.
 */

namespace Drupal\fluxmite\Plugin\Entity;

use Drupal\fluxservice_extension\Plugin\Entity\RemoteEntityExtended;

/**
 * Entity class for Mite Customers.
 */
class MiteEntityBase extends RemoteEntityExtended implements MiteEntityBaseInterface {
	public function __construct(array $values = array(), $entity_type = NULL) {
		if(isset($values)&&$values!=array()){
	   		$keys=array_keys($values);
	   		
	   		array_walk($keys, function(&$value, $key){
	   			$value=str_replace('-', '_', $value);
	   		});

	   		$values=array_combine($keys, $values);
	   		$id=explode(':', $values['id']);
	   		$this->remote_id=$id[2];

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

	   		if(isset($values['updated_at'])){
	   			$values['updated_at']=strtotime($values['updated_at']);
	   		}

	   		if(isset($values['updated_at'])){
	   			$values['created_at']=strtotime($values['created_at']);	   			
	   		}

   		}

   		parent::__construct($values, $entity_type);
   	}

	/**
   	  * Gets the entity property definitions.
   	  */
  	public static function getEntityPropertyInfo($entity_type, $entity_info) {
	    $info=parent::getEntityPropertyInfo($entity_type,$entity_info);

	    $info['remote_id'] = array(
	      'label' => t('Mite id'),
	      'description' => t("Mite id."),
	      'type' => 'integer',
	      'setter callback' => 'entity_property_verbatim_set',
	    );		    
	    $info['created_at'] = array(
	      'label' => t('Created-at'),
	      'description' => t("Date which the data was created at"),
	      'type' => 'date',
	      'setter callback' => 'entity_property_verbatim_set',
	    );
	    $info['updated_at'] = array(
	      'label' => t('Updated-at'),
	      'description' => t("Date of the last update"),
	      'type' => 'date',
	      'setter callback' => 'entity_property_verbatim_set',
	    );

	    return $info;
  	}

   	public function getCheckValue(){
   		return $this->updated_at;
   	}
}