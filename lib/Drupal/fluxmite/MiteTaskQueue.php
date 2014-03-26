<?php
/**
 * 
 */
 namespace Drupal\fluxmite;

 class MiteTaskQueue {
 	public function addTask($data){
 		$res=db_query('SELECT * FROM {fluxmite_queue} WHERE local_id=:local_id AND local_type=:local_type AND task_type=:task_type', 
 				array(
 					':local_id'=>$data['local_id'],
 					':local_type'=>$data['local_type'],
 					':task_type'=>$data['task_type']));

 		$res=$res->fetch();

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

 	public function getTasks($entity_type){
 		$res=db_query('SELECT * FROM {fluxmite_queue} WHERE remote_type=:type ORDER BY task_priority DESC',array(':type'=>$entity_type));

 		$res=$res->fetchAll();

 		return $res;
 	}

 	public function cleanQueue($entity_type){
 		db_delete('fluxmite_queue')
 			->condition('remote_type',$entity_type,'=')
 			->condition('failed',0,'=')
 			->execute();
 	}

 	public function resetTaskFailed($id){
 		db_update('fluxmite_queue')
 			->fields(array('failed'=>0))
 			->condition('id',$id,'=')
 			->execute();
 	}
 }
 ?>