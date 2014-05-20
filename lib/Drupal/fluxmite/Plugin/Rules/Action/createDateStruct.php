<?php

/**
 * @file
 * Contains createDateStruct.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * create date struct.
 */
class createDateStruct extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_create_date_struct',
      'label' => t('Create date struct'),
      'parameter' => array(
        'start_date' => array(
          'type' => 'date',
          'label' => t('Start date'),
          'required' => TRUE,
        ),
        'duration' => array(
          'type' => 'integer',
          'label' => t('Duration'),
          'required' => TRUE,
        ),
      ),
      'provides' => array(
        'date_struct' => array(
          'type'=>'struct',
          'label' => t('Date struct'),
          'property info' => array(
            'value' => array(
              'label' => t('Start date'),
              'description' => t('Start date'),
              'type' => 'date',
            ),
            'value2' => array(
              'label' => t('End date'),
              'description' => t('End date'),
              'type' => 'date',
            ),
            'duration' => array(
              'label' => t('Duration'),
              'description' => t('Duration'),
              'type' => 'integer',
            ),
          )
        )
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($start, $duration) {
//    dpm("create date struct");
  //  print_r("create date struct<br>");

    $date_struct=array();
    $date_struct['value']=$start;
    $date_struct['value2']=$start+($duration*60);
    $date_struct['duration']=$duration;

    return array('date_struct'=>$date_struct);
  }
}
