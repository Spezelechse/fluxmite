<?php
/**
 * 
 */
 namespace Drupal\fluxmite;

 use Drupal\fluxmite\MiteQueuedTasks;

 class MiteTaskQueue {
 	private $entity_type;
 	private $account;
 	private $tasks;

 	public function __construct($type, $acc){
 		$this->entity_type=$type;
 		$this->account=$acc;
 		$this->tasks=new MiteQueuedTasks();
 	}

 	public function addTask($data){
		$res=db_select('fluxmite_queue','fq')
				->fields('fq')
				->condition('fq.local_id',$data['local_id'],'=')
				->condition('fq.local_type',$data['local_type'],'=')
				->condition('fq.callback',$data['callback'],'=')
				->execute()
				->fetch();

 		if(isset($res->id)){
 			db_update('fluxmite_queue')
 			->fields(array(
 				'attempts'=>$res->attempts+1,
 				'time'=>time(),
 				'failed'=>1))
 			->condition('id',$res->id,'=')
 			->execute();
 		}
 		else{
 			$data['time']=time();
 			$data['attempts']=1;
 			$data['failed']=1;

 			db_insert('fluxmite_queue')
			->fields($data)
			->execute();
 		}
 	}

 	public function getTasks(){
 		$res=db_select('fluxmite_queue','fq')
 				->fields('fq')
 				->condition('fq.remote_type',$this->entity_type,'=')
 				->orderBy('fq.task_priority','DESC')
 				->execute()
 				->fetchAll();

 		return $res;
 	}

 	public function clean(){
 		db_delete('fluxmite_queue')
 			->condition('remote_type',$this->entity_type,'=')
 			->condition('failed',0,'=')
 			->execute();
 	}

 	public function resetTaskFailed($id){
 		db_update('fluxmite_queue')
 			->fields(array('failed'=>0))
 			->condition('id',$id,'=')
 			->execute();
 	}

 	public function process(){
	    $this->clean();
	    $queue=$this->getTasks();

	    foreach ($queue as $task) {
	      $this->resetTaskFailed($task->id);

	      $callback=$task->callback;

	      if(method_exists($this->tasks,$callback)){
	      	$this->tasks->$callback($task,$this->account);
	      	echo $task->callback.": ".$task->local_type."(".$task->local_id.")<br>";
	      }
	      else{
	      	throw new \Exception('Unkown task callback: '.$callback);
	      }
	    }
	    echo "<br>";
 	}
 }
 ?>