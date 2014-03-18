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
  public function type(){
    return $this->entityType();
  }
}