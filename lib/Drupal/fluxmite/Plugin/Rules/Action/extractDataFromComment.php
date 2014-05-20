<?php

/**
 * @file
 * Contains extractDataFromComment.
 */

namespace Drupal\fluxmite\Plugin\Rules\Action;

use Drupal\fluxmite\Plugin\Service\MiteAccountInterface;
use Drupal\fluxmite\Rules\RulesPluginHandlerBase;

/**
 * extract data from comment.
 */
class extractDataFromComment extends RulesPluginHandlerBase implements \RulesActionHandlerInterface {

  /**
   * Defines the action.
   */
  public static function getInfo() {

    return static::getInfoDefaults() + array(
      'name' => 'fluxmite_extract_data_from_comment',
      'label' => t('Extract data from comment'),
      'parameter' => array(
        'comment' => array(
          'type' => 'text',
          'label' => t('Comment'),
          'required' => TRUE,
        ),
      ),
      'provides' => array(
        'comment_data' => array(
          'type'=>'struct',
          'label' => t('Comment data'),
          'property info' => array(
            'title' => array(
              'label' => t('Title'),
              'description' => t('Title'),
              'type' => 'text',
            ),
            'task_id' => array(
              'label' => t('Task id'),
              'description' => t('Task id'),
              'type' => 'integer',
            ),
            'category' => array(
              'label' => t('Category'),
              'description' => t('Category'),
              'type' => 'text',
            ),
          )
        )
      )
    );
  }

  /**
   * Executes the action.
   */
  public function execute($comment) {
//    dpm("extract from comment");
  //  print_r("extract from comment<br>");
    $comment_data=array();

    $comment=explode(']', $comment);
    foreach ($comment as $data) {
      $data=explode('=', $data);

      switch ($data[0]) {
        case '[title':
          $comment_data['title']=$data[1];
          break;
        case '[task_id':
          $comment_data['task_id']=(int)$data[1];
          break;
        case '[category':
          $comment_data['category']=$data[1];
          break;
        
        default:
          break;
      }
    }
    return array('comment_data'=>$comment_data);
  }
}
