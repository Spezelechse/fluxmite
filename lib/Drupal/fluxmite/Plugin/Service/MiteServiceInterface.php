<?php

/**
 * @file
 * Contains MiteServiceInterface
 */

namespace Drupal\fluxmite\Plugin\Service;

use Drupal\fluxservice\Service\OAuthServiceInterface;

/**
 * Interface for Mite services.
 */
interface MiteServiceInterface extends OAuthServiceInterface  {

  /**
   * Gets the update interval.
   *
   * @return int
   *   The update interval.
   */
  public function getPollingInterval();

  /**
   * Sets the update interval.
   *
   * @param int $interval
   *   The update interval.
   *
   * @return MiteServiceInterface
   *   The called object for chaining.
   */
  public function setPollingInterval($interval);

}
