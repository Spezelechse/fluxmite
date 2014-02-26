<?php

/**
 * @file
 * Contains MiteUserController.
 */

namespace Drupal\fluxmite;

use Drupal\fluxservice\Entity\FluxEntityInterface;
use Drupal\fluxservice\RemoteEntityController;
use Drupal\fluxservice\Entity\RemoteEntityInterface;

/**
 * Class RemoteEntityController
 */
class MiteUserController extends RemoteEntityController {

  /**
   * {@inheritdoc}
   */
  protected function loadFromService($ids, FluxEntityInterface $agent) {
    kpr($ids);
    $output = array();
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  protected function sendToService(RemoteEntityInterface $entity) {
    // @todo Throw exception.
  }

}
