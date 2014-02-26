<?php

/**
 * @file
 * Contains MiteAccountInterface
 */

namespace Drupal\fluxmite\Plugin\Service;

use Drupal\fluxservice\Service\OAuthAccountInterface;

/**
 * Interface for Mite accounts.
 */
interface MiteAccountInterface extends OAuthAccountInterface {

  /**
   * Gets the account's access token.
   *
   * @return string
   *   The access token of the account.
   */
  public function getAccessToken();

  /**
   * Gets the Mite Graph API client.
   *
   * @return \Guzzle\Service\Client
   *   The web service client for the Graph API.
   */
  public function client();

}
