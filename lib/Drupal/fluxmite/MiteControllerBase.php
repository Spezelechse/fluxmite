<?php

/**
 * @file
 * Contains MiteControllerBase.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\RemoteEntityController;
use Drupal\fluxservice\Entity\RemoteEntityInterface;
use Guzzle\Http\Exception\BadResponseException;

/**
 * Class RemoteEntityController
 */
abstract class MiteControllerBase extends RemoteEntityController {
/**
 * Creates a new database entry
 */
  public function createLocal(RemoteEntityInterface $remote_entity, $local_entity_id, $local_entity_type){
    $fields=array('id', 'type', 'remote_id', 'remote_type', 'mite_id', 'touched_last', 'created_at', 'updated_at');
    $values=array($local_entity_id, 
                  $local_entity_type, 
                  $remote_entity->id, 
                  $remote_entity->entityType(), 
                  $remote_entity->mite_id,
                  time(),
                  strtotime($remote_entity->created_at), 
                  strtotime($remote_entity->updated_at));

    $nid=db_insert('fluxmite')
      ->fields($fields)
      ->values($values)
      ->execute();
  }

/**
 *    Sends a post request to create a new mite data set of the given type
 */
  public function createRemote($local_entity_id, $local_entity_type, $account, $remote_entity, $request="", $remote_type=""){
      if($remote_entity!=null){
        $req=$this->createRequestString($remote_entity);
        $type_org=$remote_entity->entityType();
      }
      else if($request!=""){
        $req=$request;
        $type_org=$remote_type;
      }
      else{
        //TODO: throw error missing argument
      }

      //extract mite type
      $type=$type_org;
      $type_split=explode("_",$type);
      $type=$type_split[1];

      if($type=='time'){
        $type.='_'.$type_split[2];
      }

      $client=$account->client();

      //build operation name
      $operation='post'.ucfirst($type);
      
      //try to send the post request
      try{
        $response=$client->$operation(array('data' => $req, 'api_key'=>$client->getConfig('access_token')));
      }
      catch(BadResponseException $e){
        if($e->getResponse()->getStatusCode()==404){
          $this->handle404( '[404] Host "'.$account->client()->getBaseUrl().'" not found ('.$operation.')',
                            array(
                              'callback'=>'post',
                              'task_priority'=>2,
                              'local_id'=>$local_entity_id,
                              'local_type'=>$local_entity_type,
                              'request'=>$req,
                              'remote_type'=>$type_org),
                          $e->getResponse()->getMessage());
        }
        else{
        }
      }

      if(isset($response)){
        //generate an array from xml
        $response = json_decode(json_encode($response),1);

        $remoteEntity = fluxservice_entify($response, $type_org, $account);

        //create local database entry
        $this->createLocal($remoteEntity, $local_entity_id, $local_entity_type);
        return $remoteEntity;
      }
  }

  /**
   *  Builds an xml request string for the given entity
   */
  private function createRequestString(RemoteEntityInterface $remote_entity){
    $properties=$remote_entity->getEntityPropertyInfo("","");

    //extract mite type
    $type=$remote_entity->entityType();
    $type_split=explode("_",$type);
    $req="";

    $type=$type_split[1];
    if($type=='time'){
        $type.='-'.$type_split[2];
    }
      
    //generate a xml element for every set entity property
    foreach ($properties as $key => $value) {
      if($key=='id'||$key=='updated_at'||$key=='created_at'||$key=='hourly_rates_per_service'||$key=='mite_id'){
        continue;
      }

      if(isset($remote_entity->$key)){
        $value=$remote_entity->$key;
        $data_type='';

        //special for dates, arrays and boolean
        if($key=='date_at'){
          if(isset($value)){
            $value=date('Y-m-d',$value);
          }
          else{
            $value=date('Y-m-d');
          }
        }
        else if($key=='since'){
          if(isset($value)){
            $value=date('Y-m-d\TH:m:sP',$value);
          }
          else{
            $value=date('Y-m-d\TH:m:sP');
          }
        }
        else if($key=='hourly_rates_per_service_json'){
          $key='hourly_rates_per_service';
          if(isset($value)&&$value!=""){
            //build array from json
            $buffer=json_decode($value);

            if(gettype($buffer)!='array'){
              $buffer=array($buffer);
            }

            $value='';
            foreach ($buffer as $rate) {
              $value.='<hourly-rate-per-service>';
                $value.='<service-id type="integer">'.$rate->{"service-id"}.'</service-id>';
                $value.='<hourly-rate type="integer">'.$rate->{"hourly-rate"}.'</hourly-rate>';
              $value.='</hourly-rate-per-service>';
            }
            $data_type=' type="array"';
          }
        }
        else if($key=='billable'||$key=='archived'||$key=='locked'){
          $value=($value==0 ? 'false' : 'true');
        }

        $key=str_replace('_', '-', $key);
        $req.="<".$key."".$data_type.">".$value."</".$key.">"; 
      }
    }

    $req.="<force>true</force>";

    $req="<".$type.">".$req."</".$type.">";
    return $req;
  }

  public function deleteRemote($local_entity_id, $local_entity_type, $account, $remote_type, $mite_id){
    $type_split=explode("_",$remote_type);
    $type=$type_split[1];

    if($type=='time'){
        $type.='_'.$type_split[2];
    }
      
    $client=$account->client();
    
    //build operation name
    $operation='delete'.ucfirst($type);

    //try to send delete request
    try{
      $response=$client->$operation(array(  'id' => (int)$mite_id,
                                            'api_key'=>$client->getConfig('access_token')));
    }
    catch(BadResponseException $e){
      if($e->getResponse()->getStatusCode()==404){
        $continue=$this->handle404( '[404] Host "'.$account->client()->getBaseUrl().'" not found ('.$operation.')',
                          array(
                            'callback'=>'delete',
                            'task_priority'=>0,
                            'local_id'=>$local_entity_id,
                            'local_type'=>$local_entity_type,
                            'remote_type'=>$remote_type,
                            'mite_id'=>$mite_id),
                          $e->getResponse()->getMessage());
      }
      else{
      }
    }

    if((isset($response['status'])&&$response['status']==200)||$continue){
      $num=db_delete('fluxmite')->condition('id', $local_entity_id)->condition('type', $local_entity_type)->execute();
    }
  }

  /**
   *  Updates the local fluxmite table
   */
  public function updateLocal(RemoteEntityInterface $remote_entity, $local_entity_id, $local_entity_type){
    $fields=array('updated_at'=>strtotime($remote_entity->updated_at));

    db_update('fluxmite')
      ->fields($fields)
      ->condition('id', $local_entity_id, '=')
      ->condition('type', $local_entity_type, '=')
      ->execute();
  }


  /**
  *   Sends a put request to update a mite data set and if successful updates the local table
  */
  public function updateRemote($local_entity_id, $local_entity_type, $account, $remote_entity){
    $req=$this->createRequestString($remote_entity);

    //extract mite type
    $type=$remote_entity->entityType();
    $type_split=explode("_",$type);
    $type=$type_split[1];

    if($type=='time'){
        $type.='_'.$type_split[2];
    }  

    $client=$account->client();

    //build operation name
    $operation='put'.ucfirst($type);

    //try to send the update request
    try{
      $response=$client->$operation(array(  'data' => $req, 
                                            'id' => (int)$remote_entity->mite_id,
                                            'api_key'=>$client->getConfig('access_token')));
    }
    catch(BadResponseException $e){
      if($e->getResponse()->getStatusCode()==404){
        $this->handle404( '[404] Host "'.$account->client()->getBaseUrl().'" not found ('.$operation.')', 
                          array(
                            'callback'=>'put',
                            'task_priority'=>1,
                            'local_id'=>$local_entity_id,
                            'local_type'=>$local_entity_type,
                            'request'=>$req,
                            'remote_type'=>$remote_entity->entityType()),
                          $e->getResponse()->getMessage());
      }
      else{
      }
    }

    //check if successful
    if(isset($response['status'])&&$response['status']==200){
      //get the new updated-at timestamp
      $operation='get'.ucfirst($type);
      $response=$client->$operation(array('id' => (int)$remote_entity->mite_id,
                                          'api_key'=>$client->getConfig('access_token')));

      //generate an array from xml
      $response = json_decode(json_encode($response),1);

      $remoteEntity = fluxservice_entify($response, $remote_entity->entityType(), $account);

      //update local database entry
      $this->updateLocal($remoteEntity, $local_entity_id, $local_entity_type);
      return $remoteEntity;
    }
  }

  /**
   * 
   */
  public function handle404($log_message, $data=array(), $response_message=""){
    $document_not_found_de="Der Datensatz ist nicht vorhanden";
    $document_not_found_eng="The record does not exist";

    if($data!=array()&&!strpos($response_message,$document_not_found_eng)&&!strpos($response_message,$document_not_found_de)){
      MiteTaskQueue::addTask($data);
      watchdog('Fluxmite',$log_message);
    }
    else{
      return true;
    }
  }
}
