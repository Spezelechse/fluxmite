<?php

/**
 * @file
 * Contains MiteProjectTaskHandler.
 */

namespace Drupal\fluxmite\TaskHandler;

use \PDO;
/**
 * Event dispatcher for changed mite projects.
 */
class MiteProjectTaskHandler extends MiteTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
  	if($this->checkRequirements()){
  		print_r("project");
  		echo "<br>";
  		$this->processQueue();
		$this->checkAndInvoke();
	}
	$this->afterTaskComplete();
  }

  /**
   * Before Projects can be handled customers are needed
   */
  public function checkRequirements(){
  	$customer=$this->getRequiredData();

	//check customers are handled
	if($customer){
		$res=db_select('rules_scheduler','rs')
				->fields('rs',array('tid'))
				->condition('rs.date',$customer->date,'>')
				->condition('rs.identifier','fluxmite_project%','LIKE')
				->execute()
				->fetch();
		//check customers are handled before projects
		if($res){
			return true;
		}
	}
  	return false;
  }

  public function afterTaskComplete(){
    $service = $this->getAccount()->getService();

	$customer=$this->getRequiredData();

	if($customer){
		
		//make sure projects will be handled after customers
		db_update('rules_scheduler')
			->condition('tid', $this->task['tid'])
			->fields(array('date' => $customer->date + 1 + $service->getPollingInterval()))
			->execute();
	}
  }

  private function getRequiredData(){
  	$data=db_select('rules_scheduler','rs')
			->fields('rs',array('date'))
			->condition('rs.identifier','fluxmite_customer%','LIKE')
			->orderBy('rs.date','DESC')
			->execute()
			->fetch();

	return $data;
  }
}