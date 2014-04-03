<?php

/**
 * @file
 * Contains generateJsonArrayFromLists.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * update remote entities.
 */
class generateJsonArrayFromLists extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_generate_json_array_from_lists',
      'label' => t('Generate json array from lists'),
      'parameter' => array(
        'name1' => array(
          'type' => 'text',
          'label' => t('Name param 1'),
          'description' => t('Name for list 1 values'),
        ),
        'name2' => array(
          'type' => 'text',
          'label' => t('Name param 2'),
          'description' => t('Name for list 2 values'),
        ),
        'list1' => array(
          'type' => 'list',
          'label' => t('List 1'),
        ),
        'list2' => array(
          'type' => 'list',
          'label' => t('List 2'),
        ),
      ),
      'provides' => array(
        'json_array' => array(
          'type'=>'text',
          'label' => t('Json array')),
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($name1, $name2, $list1, $list2) {
    $json_array="";

    foreach ($list1 as $key => $value) {
      $json_array.='{"'.$name1.'":"'.$value.'","'.$name2.'":"'.$list2[$key].'"},';
    }
    $json_array='['.substr($json_array,0,-1).']';

    return array('json_array' => $json_array);
  }
}